<?php
header('Content-Type: application/json');
session_start();

if (empty($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

require_once __DIR__ . '/db.php';

try {
    $db   = getDB();
    $row  = $db->query("
        SELECT
          COUNT(*)                   AS total,
          SUM(status='pending')      AS pending,
          SUM(status='confirmed')    AS confirmed,
          SUM(status='completed')    AS completed,
          SUM(payment_status='paid') AS paid
        FROM bookings
    ")->fetch();

    echo json_encode([
        'total'     => (int)$row['total'],
        'pending'   => (int)$row['pending'],
        'confirmed' => (int)$row['confirmed'],
        'completed' => (int)$row['completed'],
        'paid'      => (int)$row['paid'],
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error']);
}
