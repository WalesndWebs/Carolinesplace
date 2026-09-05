<?php
/**
 * Caroline's Place — Update Booking Status & Notes
 * Admin authenticated endpoint. Supports both AJAX JSON and Form POST.
 */
session_start();
require_once __DIR__ . '/db.php';

// Authentication check
if (empty($_SESSION['admin'])) {
    http_response_code(401);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$raw = file_get_contents('php://input');
$json = json_decode($raw, true);

$id           = (int)($json['id'] ?? $_POST['booking_id'] ?? 0);
$status       = $json['status'] ?? $_POST['status'] ?? null;
$paymentStatus = $json['payment_status'] ?? $_POST['payment_status'] ?? null;
$adminNotes   = $json['admin_notes'] ?? $_POST['admin_notes'] ?? null;

if ($id <= 0) {
    http_response_code(400);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => 'Invalid booking ID']);
    exit;
}

try {
    $db = getDb();
    $fields = [];
    $params = [];

    if ($status !== null) {
        $allowed = ['pending', 'confirmed', 'completed', 'cancelled'];
        if (in_array($status, $allowed, true)) {
            $fields[] = "status = ?";
            $params[] = $status;
        }
    }

    if ($paymentStatus !== null) {
        $allowedPay = ['unpaid', 'paid', 'refunded', 'partially_paid'];
        if (in_array($paymentStatus, $allowedPay, true)) {
            $fields[] = "payment_status = ?";
            $params[] = $paymentStatus;
        }
    }

    if ($adminNotes !== null) {
        $fields[] = "admin_notes = ?";
        $params[] = $adminNotes;
    }

    if (empty($fields)) {
        http_response_code(400);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['error' => 'No fields to update']);
        exit;
    }

    $driver = $db->getAttribute(PDO::ATTR_DRIVER_NAME);
    $nowExpr = $driver === 'sqlite' ? "datetime('now')" : "NOW()";
    $fields[] = "updated_at = {$nowExpr}";

    $sql = "UPDATE bookings SET " . implode(', ', $fields) . " WHERE id = ?";
    $params[] = $id;

    $stmt = $db->prepare($sql);
    $stmt->execute($params);

    // If redirected via standard form POST
    if (!empty($_POST['booking_id'])) {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/admin/dashboard.php';
        header('Location: ' . $referer);
        exit;
    }

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => true, 'id' => $id]);
} catch (Exception $e) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => $e->getMessage()]);
}
