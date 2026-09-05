<?php
/**
 * Caroline's Place — Services API Endpoint
 * Provides catalog of categories, services, and pricing options.
 */
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/db.php';

try {
    $db = getDb();
    $division = isset($_GET['division']) ? trim($_GET['division']) : '';

    if ($division === 'clubhouse') {
        // Return Clubhouse private spaces & executive meeting offerings
        echo json_encode([
            ['id' => 1001, 'name' => 'The Executive Boardroom (Full Day)', 'category' => 'Executive Spaces'],
            ['id' => 1002, 'name' => 'The Executive Boardroom (Half Day)', 'category' => 'Executive Spaces'],
            ['id' => 1003, 'name' => 'Private Dining & Meeting Salon', 'category' => 'Private Dining'],
            ['id' => 1004, 'name' => 'Members Cigar & Spirits Lounge Access', 'category' => 'Lounges'],
            ['id' => 1005, 'name' => 'Private Terrace & Event Sanctuary', 'category' => 'Terrace & Events']
        ]);
        exit;
    }

    // Default: fetch categories and all active services with options
    $catStmt = $db->query("SELECT * FROM categories WHERE is_active = 1 ORDER BY sort_order ASC, id ASC");
    $categories = $catStmt->fetchAll();

    $svcStmt = $db->query("SELECT * FROM services WHERE is_active = 1 ORDER BY sort_order ASC, id ASC");
    $services = $svcStmt->fetchAll();

    $optStmt = $db->query("SELECT * FROM options WHERE is_active = 1 ORDER BY sort_order ASC, id ASC");
    $options = $optStmt->fetchAll();

    // Group options by service_id
    $optionsBySvc = [];
    foreach ($options as $o) {
        $optionsBySvc[$o['service_id']][] = $o;
    }

    // Attach options to services
    $servicesByCat = [];
    $flatServices = [];
    foreach ($services as &$s) {
        $s['options'] = $optionsBySvc[$s['id']] ?? [];
        $s['first_price_ngn'] = !empty($s['options'][0]) ? (float)$s['options'][0]['price_ngn'] : 0.0;
        $servicesByCat[$s['category_id']][] = $s;
        
        $flatServices[] = [
            'id' => $s['id'],
            'name' => $s['name'],
            'category_id' => $s['category_id'],
            'description' => $s['description'],
            'price_ngn' => $s['first_price_ngn'],
            'options' => $s['options']
        ];
    }

    // If simple division query (e.g. from single service book dropdown)
    if (!empty($division) && $division !== 'all') {
        echo json_encode($flatServices);
        exit;
    }

    // Structure with categories and child services
    $catalog = [];
    foreach ($categories as $c) {
        $catalog[] = [
            'cat' => $c,
            'services' => $servicesByCat[$c['id']] ?? []
        ];
    }

    echo json_encode($catalog);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Unable to fetch services: ' . $e->getMessage()]);
}
