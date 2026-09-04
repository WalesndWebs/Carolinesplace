<?php
header('Content-Type: application/json');
header('Cache-Control: no-cache');

require_once __DIR__ . '/db.php';

$division = $_GET['division'] ?? '';
if (!in_array($division, ['clubhouse', 'spa'])) {
    echo json_encode([]);
    exit;
}

try {
    $db   = getDB();
    $stmt = $db->prepare("SELECT id, name, description, category, duration_minutes FROM services WHERE division = ? AND is_active = 1 ORDER BY id");
    $stmt->execute([$division]);
    $rows = $stmt->fetchAll();

    // Convert numeric strings to numbers
    foreach ($rows as &$r) {
        $r['id']               = (int)$r['id'];
        $r['duration_minutes'] = $r['duration_minutes'] ? (int)$r['duration_minutes'] : null;
    }

    echo json_encode($rows);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch services']);
}
