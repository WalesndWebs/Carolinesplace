<?php
/**
 * Caroline's Place — Multi-Treatment Spa Booking Submission
 * Processes spa reservations, records line items, and redirects to confirmation.
 */
require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /spa_menu.php');
    exit;
}

$db = getDb();

$fullName      = trim($_POST['full_name'] ?? '');
$email         = trim($_POST['email'] ?? '');
$phone         = trim($_POST['phone'] ?? '');
$preferredDate = trim($_POST['preferred_date'] ?? '');
$preferredTime = trim($_POST['preferred_time'] ?? '');
$notes         = trim($_POST['notes'] ?? '');

if (empty($fullName) || empty($email) || empty($phone) || empty($preferredDate) || empty($preferredTime)) {
    header('Location: /spa_menu.php');
    exit;
}

// Retrieve line items
$svcIds = $_POST['line_service_id'] ?? [];
$optIds = $_POST['line_option_id'] ?? [];
$units  = $_POST['line_unit'] ?? [];
$qtys   = $_POST['line_qty'] ?? [];

if (!is_array($svcIds)) $svcIds = [$svcIds];
if (!is_array($optIds)) $optIds = [$optIds];
if (!is_array($units))  $units  = [$units];
if (!is_array($qtys))   $qtys   = [$qtys];

if (empty($svcIds)) {
    header('Location: /spa_menu.php');
    exit;
}

// Generate unique reference code: e.g. SPA-7K2M9P
$ref = 'SPA-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 6));

// Prepare service lookup
$svcStmt = $db->prepare("SELECT id, name FROM services WHERE id = ?");
$optStmt = $db->prepare("SELECT id, option_label, price_ngn FROM options WHERE id = ?");

$calculatedTotal = 0.0;
$itemsToInsert = [];

for ($i = 0; $i < count($svcIds); $i++) {
    $sid = (int)($svcIds[$i] ?? 0);
    if ($sid <= 0) continue;

    $svcStmt->execute([$sid]);
    $svc = $svcStmt->fetch();
    if (!$svc) continue;

    $oid = !empty($optIds[$i]) ? (int)$optIds[$i] : null;
    $opt = null;
    if ($oid) {
        $optStmt->execute([$oid]);
        $opt = $optStmt->fetch();
    }

    $unitPrice = $opt ? (float)$opt['price_ngn'] : (float)($units[$i] ?? 0.0);
    $qty = max(1, min(10, (int)($qtys[$i] ?? 1)));
    $lineTotal = $unitPrice * $qty;
    $calculatedTotal += $lineTotal;

    $itemsToInsert[] = [
        'service_id'   => $sid,
        'option_id'    => $oid,
        'service_name' => $svc['name'],
        'option_label' => $opt ? $opt['option_label'] : 'Standard',
        'unit_price'   => $unitPrice,
        'qty'          => $qty,
        'line_total'   => $lineTotal
    ];
}

if (empty($itemsToInsert)) {
    header('Location: /spa_menu.php');
    exit;
}

try {
    $db->beginTransaction();

    $driver = $db->getAttribute(PDO::ATTR_DRIVER_NAME);
    $nowExpr = $driver === 'sqlite' ? "datetime('now')" : "NOW()";

    $insBooking = $db->prepare("
        INSERT INTO bookings (
            reference_code, full_name, email, phone, division,
            preferred_date, preferred_time, total_amount_ngn, notes,
            status, payment_status, created_at
        ) VALUES (?, ?, ?, ?, 'spa', ?, ?, ?, ?, 'pending', 'unpaid', {$nowExpr})
    ");

    $insBooking->execute([
        $ref, $fullName, $email, $phone,
        $preferredDate, $preferredTime,
        $calculatedTotal, $notes
    ]);

    $bookingId = $db->lastInsertId();

    $insItem = $db->prepare("
        INSERT INTO booking_items (
            booking_id, service_id, option_id, service_name,
            option_label, unit_price_ngn, quantity, line_total_ngn
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");

    foreach ($itemsToInsert as $item) {
        $insItem->execute([
            $bookingId,
            $item['service_id'],
            $item['option_id'],
            $item['service_name'],
            $item['option_label'],
            $item['unit_price'],
            $item['qty'],
            $item['line_total']
        ]);
    }

    $db->commit();

    header('Location: /confirmation.php?ref=' . urlencode($ref));
    exit;
} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    die("An error occurred while confirming your reservation: " . htmlspecialchars($e->getMessage()));
}
