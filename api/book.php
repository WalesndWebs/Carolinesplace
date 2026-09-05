<?php
/**
 * Caroline's Place — Single Service Booking JSON Endpoint
 * Used by interactive booking form (assets/js/book.js).
 */
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$raw = file_get_contents('php://input');
$data = json_decode($raw, true) ?: $_POST;

$fullName      = trim($data['full_name'] ?? '');
$email         = trim($data['email'] ?? '');
$phone         = trim($data['phone'] ?? '');
$serviceId     = (int)($data['service_id'] ?? 0);
$division      = trim($data['division'] ?? 'spa');
$preferredDate = trim($data['preferred_date'] ?? '');
$preferredTime = trim($data['preferred_time'] ?? '');
$notes         = trim($data['notes'] ?? '');

if (empty($fullName) || empty($email) || empty($phone) || empty($preferredDate) || empty($preferredTime)) {
    http_response_code(400);
    echo json_encode(['error' => 'Please fill in all required fields.']);
    exit;
}

$db = getDb();

// Resolve service and price
$serviceName = 'General Reservation';
$unitPrice = 0.0;

if ($serviceId > 0) {
    $svcStmt = $db->prepare("SELECT name FROM services WHERE id = ?");
    $svcStmt->execute([$serviceId]);
    $svc = $svcStmt->fetch();
    if ($svc) {
        $serviceName = $svc['name'];
        // Fetch first option price if available
        $optStmt = $db->prepare("SELECT price_ngn FROM options WHERE service_id = ? ORDER BY sort_order ASC LIMIT 1");
        $optStmt->execute([$serviceId]);
        $opt = $optStmt->fetch();
        if ($opt) {
            $unitPrice = (float)$opt['price_ngn'];
        }
    }
}

$ref = 'RES-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 6));

try {
    $db->beginTransaction();

    $driver = $db->getAttribute(PDO::ATTR_DRIVER_NAME);
    $nowExpr = $driver === 'sqlite' ? "datetime('now')" : "NOW()";

    $ins = $db->prepare("
        INSERT INTO bookings (
            reference_code, full_name, email, phone, division,
            service_id, preferred_date, preferred_time, total_amount_ngn,
            notes, status, payment_status, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', 'unpaid', {$nowExpr})
    ");

    $ins->execute([
        $ref, $fullName, $email, $phone, $division,
        $serviceId > 0 ? $serviceId : null,
        $preferredDate, $preferredTime,
        $unitPrice, $notes
    ]);

    $bookingId = $db->lastInsertId();

    if ($serviceId > 0) {
        $insItem = $db->prepare("
            INSERT INTO booking_items (
                booking_id, service_id, service_name, option_label,
                unit_price_ngn, quantity, line_total_ngn
            ) VALUES (?, ?, ?, 'Standard', ?, 1, ?)
        ");
        $insItem->execute([$bookingId, $serviceId, $serviceName, $unitPrice, $unitPrice]);
    }

    $db->commit();

    echo json_encode([
        'success' => true,
        'reference_code' => $ref,
        'booking_id' => $bookingId,
        'message' => 'Reservation recorded successfully'
    ]);
} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
