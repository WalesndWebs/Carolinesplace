<?php
/**
 * Caroline's Place — Automated Installer & Schema Initializer
 * Run this in browser (e.g. yourdomain.com/api/install.php) to install MySQL/SQLite tables and seed data.
 */
require_once __DIR__ . '/db.php';

try {
    $db = getDb();
    $driver = $db->getAttribute(PDO::ATTR_DRIVER_NAME);

    if ($driver === 'mysql') {
        $schema = file_get_contents(__DIR__ . '/../schema.sql');
        $statements = array_filter(array_map('trim', explode(';', $schema)));
        foreach ($statements as $stmt) {
            if (!empty($stmt)) {
                $db->exec($stmt);
            }
        }
    } else {
        // SQLite setup
        $db->exec("
            CREATE TABLE IF NOT EXISTS categories (
              id INTEGER PRIMARY KEY AUTOINCREMENT,
              name TEXT NOT NULL,
              description TEXT,
              icon TEXT DEFAULT '✨',
              sort_order INTEGER DEFAULT 0,
              is_active INTEGER DEFAULT 1,
              created_at TEXT DEFAULT CURRENT_TIMESTAMP
            );
            CREATE TABLE IF NOT EXISTS services (
              id INTEGER PRIMARY KEY AUTOINCREMENT,
              category_id INTEGER NOT NULL,
              name TEXT NOT NULL,
              description TEXT,
              duration_minutes INTEGER DEFAULT 60,
              sort_order INTEGER DEFAULT 0,
              is_active INTEGER DEFAULT 1,
              created_at TEXT DEFAULT CURRENT_TIMESTAMP
            );
            CREATE TABLE IF NOT EXISTS options (
              id INTEGER PRIMARY KEY AUTOINCREMENT,
              service_id INTEGER NOT NULL,
              option_label TEXT NOT NULL,
              price_ngn REAL NOT NULL DEFAULT 0.00,
              sort_order INTEGER DEFAULT 0,
              is_active INTEGER DEFAULT 1,
              created_at TEXT DEFAULT CURRENT_TIMESTAMP
            );
            CREATE TABLE IF NOT EXISTS admins (
              id INTEGER PRIMARY KEY AUTOINCREMENT,
              username TEXT NOT NULL UNIQUE,
              password TEXT NOT NULL,
              display_name TEXT,
              email TEXT,
              is_active INTEGER DEFAULT 1,
              created_at TEXT DEFAULT CURRENT_TIMESTAMP
            );
            CREATE TABLE IF NOT EXISTS bookings (
              id INTEGER PRIMARY KEY AUTOINCREMENT,
              reference_code TEXT NOT NULL UNIQUE,
              full_name TEXT NOT NULL,
              email TEXT NOT NULL,
              phone TEXT NOT NULL,
              division TEXT DEFAULT 'spa',
              service_id INTEGER DEFAULT NULL,
              preferred_date TEXT NOT NULL,
              preferred_time TEXT NOT NULL,
              total_amount_ngn REAL NOT NULL DEFAULT 0.00,
              notes TEXT,
              status TEXT DEFAULT 'pending',
              payment_status TEXT DEFAULT 'unpaid',
              admin_notes TEXT,
              created_at TEXT DEFAULT CURRENT_TIMESTAMP,
              updated_at TEXT DEFAULT CURRENT_TIMESTAMP
            );
            CREATE TABLE IF NOT EXISTS booking_items (
              id INTEGER PRIMARY KEY AUTOINCREMENT,
              booking_id INTEGER NOT NULL,
              service_id INTEGER,
              option_id INTEGER,
              service_name TEXT NOT NULL,
              option_label TEXT DEFAULT 'Standard',
              unit_price_ngn REAL NOT NULL DEFAULT 0.00,
              quantity INTEGER NOT NULL DEFAULT 1,
              line_total_ngn REAL NOT NULL DEFAULT 0.00
            );
        ");
    }

    $catCount = $db->query("SELECT count(*) FROM categories")->fetchColumn();
    $svcCount = $db->query("SELECT count(*) FROM services")->fetchColumn();

    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'success',
        'message' => "Installation verified. Driver: {$driver}. Categories: {$catCount}, Services: {$svcCount}",
        'database_driver' => $driver
    ]);
} catch (Exception $e) {
    header('Content-Type: application/json', true, 500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
