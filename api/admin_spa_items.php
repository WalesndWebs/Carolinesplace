<?php
header('Content-Type: application/json');
session_start();

// Admin only
if (empty($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

require_once __DIR__ . '/db.php';

$bookingId = isset($_GET['booking_id']) ? (int)$_GET['booking_id'] : 0;
if ($bookingId <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing or invalid booking_id']);
    exit;
}

try {
    $db = getDB();

    // 1. Basic booking info + customer + grand total
    $bookingStmt = $db->prepare("
        SELECT sb.*
        FROM spa_bookings sb
        WHERE sb.id = ?
        LIMIT 1
    ");
    $bookingStmt->execute([$bookingId]);
    $booking = $bookingStmt->fetch();

    if (!$booking) {
        http_response_code(404);
        echo json_encode(['error' => 'Spa booking not found']);
        exit;
    }

    // 2. All line items (snapshot data saved at time of booking)
    $itemsStmt = $db->prepare("
        SELECT
            sbi.id,
            sbi.service_name,
            sbi.option_label,
            sbi.unit_price_ngn,
            sbi.quantity,
            sbi.line_total_ngn,
            sbi.service_id,
            sbi.option_id
        FROM spa_booking_items sbi
        WHERE sbi.booking_id = ?
        ORDER BY sbi.id ASC
    ");
    $itemsStmt->execute([$bookingId]);
    $items = $itemsStmt->fetchAll();

    // 3. Compute subtotals on return (add formatted strings for display)
    $formattedItems = [];
    $calcTotal = 0;
    foreach ($items as $it) {
        $calcTotal += (float)$it['line_total_ngn'];
        $formattedItems[] = $it + [
            'unit_price_formatted' => priceFmt((float)$it['unit_price_ngn']),
            'line_total_formatted' => priceFmt((float)$it['line_total_ngn']),
        ];
    }

    echo json_encode([
        'ok' => true,
        'booking' => $booking,
        'items' => $formattedItems,
        'item_count' => count($formattedItems),
        'calculated_total' => $calcTotal,
        'calculated_total_formatted' => priceFmt($calcTotal),
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error loading items: ' . $e->getMessage()]);
}
