<?php
header('Content-Type: application/json');
session_start();

// Admin only
if (empty($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

require_once __DIR__ . '/db.php';

$raw  = file_get_contents('php://input');
$body = json_decode($raw, true);

if (!$body || !isset($body['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing booking ID']);
    exit;
}

$id = (int)$body['id'];
if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid booking ID']);
    exit;
}

$bookingType = isset($body['type']) && $body['type'] === 'spa' ? 'spa' : 'legacy';
$targetTable = $bookingType === 'spa' ? 'spa_bookings' : 'bookings';

// Build update set
$sets   = ['updated_at = CURRENT_TIMESTAMP'];
$params = [];

if (isset($body['status'])) {
    $allowed = ['pending','confirmed','cancelled','completed'];
    if (!in_array($body['status'], $allowed)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid status value']);
        exit;
    }
    $sets[]   = 'status = ?';
    $params[] = $body['status'];
}

if (isset($body['payment_status'])) {
    if (!in_array($body['payment_status'], ['unpaid','paid'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid payment status']);
        exit;
    }
    $sets[]   = 'payment_status = ?';
    $params[] = $body['payment_status'];
}

if (array_key_exists('admin_notes', $body)) {
    $sets[]   = 'admin_notes = ?';
    $params[] = $body['admin_notes'] ?: null;
}

if (array_key_exists('staff_assigned', $body)) {
    $sets[]   = 'staff_assigned = ?';
    $params[] = $body['staff_assigned'] ?: null;
}

if (count($sets) === 1) {
    http_response_code(400);
    echo json_encode(['error' => 'No fields to update']);
    exit;
}

try {
    $db = getDB();
    $params[] = $id;
    $sql      = "UPDATE {$targetTable} SET " . implode(', ', $sets) . " WHERE id = ?";
    $stmt     = $db->prepare($sql);
    $stmt->execute($params);

    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'Booking not found']);
        exit;
    }

    // Return updated booking
    $fetch = $db->prepare("SELECT * FROM {$targetTable} WHERE id = ?");
    $fetch->execute([$id]);
    $booking = $fetch->fetch();

    echo json_encode($booking);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error']);
}
