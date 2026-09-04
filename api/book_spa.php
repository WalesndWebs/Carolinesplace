<?php
session_start();
require __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../spa_menu.php');
    exit;
}

$fullName      = trim($_POST['full_name']       ?? '');
$email         = trim($_POST['email']           ?? '');
$phone         = trim($_POST['phone']           ?? '');
$preferredDate = trim($_POST['preferred_date']  ?? '');
$preferredTime = trim($_POST['preferred_time']  ?? '');
$notes         = trim($_POST['notes']           ?? '');
$totalAmount   = isset($_POST['total_amount'])  ? (float)$_POST['total_amount'] : 0;

$lineSvcIds = $_POST['line_service_id'] ?? [];
$lineOptIds = $_POST['line_option_id']  ?? [];
$lineUnits  = $_POST['line_unit']       ?? [];
$lineQtys   = $_POST['line_qty']        ?? [];

$errors = [];

if (strlen($fullName) < 2)      $errors[] = 'Full name is required.';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';
if (strlen($phone) < 5)         $errors[] = 'Phone number is required.';
if (!$preferredDate || !strtotime($preferredDate)) $errors[] = 'Valid date is required.';
if (!$preferredTime)            $errors[] = 'Time is required.';

if ($preferredDate && strtotime($preferredDate) < strtotime('today')) {
    $errors[] = 'Date cannot be in the past.';
}

if (!is_array($lineSvcIds) || !$lineSvcIds) {
    $errors[] = 'No services selected.';
}

$lineCount = is_array($lineSvcIds) ? count($lineSvcIds) : 0;

if ($lineCount > 0) {
    if (!is_array($lineOptIds) || count($lineOptIds) !== $lineCount) $errors[] = 'Invalid service options.';
    if (!is_array($lineQtys)   || count($lineQtys)   !== $lineCount) $errors[] = 'Invalid quantities.';
}

if ($errors) {
    $errorMsg = implode(' ', $errors);
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head><meta charset="UTF-8"><title>Booking Error</title>
    <style>
      body { font-family: system-ui, sans-serif; background:#FAF9F6; display:flex; align-items:center; justify-content:center; min-height:100vh; padding:24px; }
      .err-card { background:#F0EBE0; border:1px solid rgba(28,26,24,0.12); padding:48px; max-width:480px; text-align:center; }
      h1 { font-family: Georgia, serif; margin-bottom:16px; color:#8B6F2E; }
      p { color:#7A7268; margin-bottom:24px; line-height:1.6; }
      a { display:inline-block; padding:14px 28px; background:#1C1A18; color:#FAF9F6; text-decoration:none; font-size:0.7rem; letter-spacing:0.2em; text-transform:uppercase; }
    </style></head>
    <body>
      <div class="err-card">
        <h1>Booking Error</h1>
        <p><?= htmlspecialchars($errorMsg) ?></p>
        <a href="../spa_menu.php">Go Back to Booking</a>
      </div>
    </body></html>
    <?php
    exit;
}

try {
    $db = getDB();

    $validLines = [];
    $verifiedTotal = 0;

    for ($i = 0; $i < $lineCount; $i++) {
        $sid   = (int)($lineSvcIds[$i] ?? 0);
        $oid   = (int)($lineOptIds[$i] ?? 0);
        $qty   = max(1, min(9, (int)($lineQtys[$i] ?? 1)));

        if ($sid <= 0 || $oid <= 0) continue;

        $stmt = $db->prepare("
            SELECT s.name AS service_name,
                   o.option_label,
                   o.price_ngn AS unit_price
            FROM spa_services s
            JOIN spa_service_options o ON o.service_id = s.id
            WHERE s.id = ? AND o.id = ?
              AND s.is_active = 1 AND o.is_active = 1
            LIMIT 1
        ");
        $stmt->execute([$sid, $oid]);
        $row = $stmt->fetch();

        if (!$row) continue;

        $unit = (float)$row['unit_price'];
        $lineTotal = $unit * $qty;
        $verifiedTotal += $lineTotal;

        $validLines[] = [
            'sid'          => $sid,
            'oid'          => $oid,
            'service_name' => $row['service_name'],
            'option_label' => $row['option_label'],
            'unit_price'   => $unit,
            'qty'          => $qty,
            'line_total'   => $lineTotal,
        ];
    }

    if (!$validLines) {
        header('Location: ../spa.php');
        exit;
    }

    $referenceCode = 'SPA-' . strtoupper(substr(md5(uniqid()), 0, 8));
    $NOW = date('Y-m-d H:i:s');

    $db->beginTransaction();

    $stmt = $db->prepare("
        INSERT INTO spa_bookings
          (reference_code, full_name, email, phone, preferred_date, preferred_time,
           total_amount_ngn, notes, status, payment_status, admin_notes, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', 'unpaid', ?, ?, ?)
    ");
    $stmt->execute([
        $referenceCode,
        $fullName,
        $email,
        $phone,
        date('Y-m-d', strtotime($preferredDate)),
        $preferredTime,
        $verifiedTotal,
        $notes ?: null,
        null,
        $NOW,
        $NOW,
    ]);

    $bid = (int)$db->lastInsertId();

    $itemStmt = $db->prepare("
        INSERT INTO spa_booking_items
          (booking_id, service_id, option_id, service_name, option_label,
           unit_price_ngn, quantity, line_total_ngn)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");

    foreach ($validLines as $l) {
        $itemStmt->execute([
            $bid,
            $l['sid'],
            $l['oid'],
            $l['service_name'],
            $l['option_label'],
            $l['unit_price'],
            $l['qty'],
            $l['line_total'],
        ]);
    }

    $db->commit();

    header('Location: ../confirmation.php?ref=' . urlencode($referenceCode) . '&source=spa');
    exit;

} catch (Exception $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    $err = $e->getMessage();
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head><meta charset="UTF-8"><title>Server Error</title>
    <style>
      body { font-family: system-ui, sans-serif; background:#FAF9F6; display:flex; align-items:center; justify-content:center; min-height:100vh; padding:24px; }
      .err-card { background:#F0EBE0; border:1px solid rgba(28,26,24,0.12); padding:48px; max-width:560px; text-align:center; }
      h1 { font-family: Georgia, serif; margin-bottom:16px; color:#8B6F2E; }
      p { color:#7A7268; margin-bottom:16px; line-height:1.6; }
      pre { background:#fff; color:#a91414; font-family:Consolas, monospace; padding:12px; text-align:left; border:1px solid rgba(169,20,20,0.25); border-radius:4px; overflow-x:auto; font-size:12px; }
      a { display:inline-block; padding:14px 28px; background:#1C1A18; color:#FAF9F6; text-decoration:none; font-size:0.7rem; letter-spacing:0.2em; text-transform:uppercase; }
    </style></head>
    <body>
      <div class="err-card">
        <h1>Server Error</h1>
        <p>Something went wrong while processing your booking. Details below (for debugging):</p>
        <pre><?= htmlspecialchars($err) ?></pre>
        <br>
        <a href="../spa_menu.php">Go Back to Booking</a>
      </div>
    </body></html>
    <?php
    exit;
}
