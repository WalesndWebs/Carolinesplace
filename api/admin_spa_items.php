<?php
/**
 * Caroline's Place — Admin Spa Items Drilldown API
 * Used by admin dashboard accordion to view itemized services for a booking.
 */
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/db.php';

if (empty($_SESSION['admin'])) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'Unauthorized']);
    exit;
}

$bookingId = (int)($_GET['booking_id'] ?? 0);
if ($bookingId <= 0) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Invalid booking ID']);
    exit;
}

try {
    $db = getDb();

    // Fetch booking
    $bStmt = $db->prepare("SELECT * FROM bookings WHERE id = ?");
    $bStmt->execute([$bookingId]);
    $booking = $bStmt->fetch();

    if (!$booking) {
        http_response_code(404);
        echo json_encode(['ok' => false, 'error' => 'Booking not found']);
        exit;
    }

    // Fetch line items
    $iStmt = $db->prepare("SELECT * FROM booking_items WHERE booking_id = ? ORDER BY id ASC");
    $iStmt->execute([$bookingId]);
    $items = $iStmt->fetchAll();

    $calculatedTotal = 0.0;
    $formattedItems = [];

    foreach ($items as $item) {
        $calculatedTotal += (float)$item['line_total_ngn'];
        $formattedItems[] = [
            'id'                   => $item['id'],
            'service_name'         => $item['service_name'],
            'option_label'         => $item['option_label'] ?: 'Standard',
            'quantity'             => (int)$item['quantity'],
            'unit_price'           => (float)$item['unit_price_ngn'],
            'unit_price_formatted' => priceFmt($item['unit_price_ngn']),
            'line_total'           => (float)$item['line_total_ngn'],
            'line_total_formatted' => priceFmt($item['line_total_ngn']),
        ];
    }

    echo json_encode([
        'ok'                         => true,
        'item_count'                 => count($formattedItems),
        'calculated_total'           => $calculatedTotal,
        'calculated_total_formatted' => priceFmt($calculatedTotal),
        'booking'                    => $booking,
        'items'                      => $formattedItems,
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}
