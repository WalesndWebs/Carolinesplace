<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

require_once __DIR__ . '/db.php';

// Parse JSON body
$raw  = file_get_contents('php://input');
$body = json_decode($raw, true);

if (!$body) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON']);
    exit;
}

// ── Validation ───────────────────────────────────────────────
$errors = [];

$fullName     = trim($body['full_name']     ?? '');
$email        = trim($body['email']         ?? '');
$phone        = trim($body['phone']         ?? '');
$division     = trim($body['division']      ?? '');
$serviceId    = isset($body['service_id'])  ? (int)$body['service_id'] : null;
$preferredDate= trim($body['preferred_date']?? '');
$preferredTime= trim($body['preferred_time']?? '');
$notes        = trim($body['notes']         ?? '');

if (strlen($fullName) < 2)                               $errors[] = 'Full name is required.';
if (!filter_var($email, FILTER_VALIDATE_EMAIL))           $errors[] = 'Valid email is required.';
if (strlen($phone) < 5)                                   $errors[] = 'Phone number is required.';
if (!in_array($division, ['clubhouse', 'spa']))           $errors[] = 'Invalid division.';
if (!$preferredDate || !strtotime($preferredDate))        $errors[] = 'Valid date is required.';
if (!$preferredTime)                                      $errors[] = 'Time is required.';

// Date must not be in the past
if ($preferredDate && strtotime($preferredDate) < strtotime('today')) {
    $errors[] = 'Date cannot be in the past.';
}

if ($errors) {
    http_response_code(400);
    echo json_encode(['error' => implode(' ', $errors)]);
    exit;
}

try {
    $db = getDB();

    // Validate service
    $serviceName = 'General Inquiry';
    if ($serviceId) {
        $stmt = $db->prepare("SELECT name FROM services WHERE id = ? AND is_active = 1");
        $stmt->execute([$serviceId]);
        $svc = $stmt->fetch();
        if (!$svc) {
            http_response_code(400);
            echo json_encode(['error' => 'Service not found.']);
            exit;
        }
        $serviceName = $svc['name'];
    }

    // Generate unique reference code
    $chars  = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    $refCode = '';
    for ($attempts = 0; $attempts < 10; $attempts++) {
        $code = 'CP-';
        for ($i = 0; $i < 8; $i++) {
            $code .= $chars[random_int(0, strlen($chars) - 1)];
        }
        $chk  = $db->prepare("SELECT id FROM bookings WHERE reference_code = ?");
        $chk->execute([$code]);
        if (!$chk->fetch()) {
            $refCode = $code;
            break;
        }
    }

    if (!$refCode) {
        http_response_code(500);
        echo json_encode(['error' => 'Could not generate reference code. Please try again.']);
        exit;
    }

    // Insert booking
    $stmt = $db->prepare("
        INSERT INTO bookings
          (reference_code, full_name, email, phone, division, service_id, service_name,
           preferred_date, preferred_time, notes, status, payment_status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', 'unpaid')
    ");
    $stmt->execute([
        $refCode,
        $fullName,
        $email,
        $phone,
        $division,
        $serviceId,
        $serviceName,
        date('Y-m-d', strtotime($preferredDate)),
        $preferredTime,
        $notes ?: null,
    ]);

    $id = $db->lastInsertId();

    http_response_code(201);
    echo json_encode([
        'id'             => (int)$id,
        'reference_code' => $refCode,
        'full_name'      => $fullName,
        'service_name'   => $serviceName,
        'division'       => $division,
        'preferred_date' => $preferredDate,
        'preferred_time' => $preferredTime,
        'status'         => 'pending',
        'payment_status' => 'unpaid',
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error. Please try again.']);
}
