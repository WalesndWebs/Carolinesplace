<?php
/**
 * Caroline's Place — Spa & Club Booking Statistics API
 */
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/db.php';

if (empty($_SESSION['admin'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

try {
    $db = getDb();

    $stats = [
        'total'     => (int)$db->query("SELECT COUNT(*) FROM bookings")->fetchColumn(),
        'pending'   => (int)$db->query("SELECT COUNT(*) FROM bookings WHERE status = 'pending'")->fetchColumn(),
        'confirmed' => (int)$db->query("SELECT COUNT(*) FROM bookings WHERE status = 'confirmed'")->fetchColumn(),
        'completed' => (int)$db->query("SELECT COUNT(*) FROM bookings WHERE status = 'completed'")->fetchColumn(),
        'cancelled' => (int)$db->query("SELECT COUNT(*) FROM bookings WHERE status = 'cancelled'")->fetchColumn(),
        'revenue'   => (float)$db->query("SELECT COALESCE(SUM(total_amount_ngn), 0) FROM bookings WHERE status != 'cancelled'")->fetchColumn(),
    ];

    $stats['revenue_formatted'] = priceFmt($stats['revenue']);

    echo json_encode($stats);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
