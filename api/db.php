<?php
/**
 * Caroline's Place — Database Connection
 * Compatible with MySQL (Hostinger PDO) and SQLite fallback for local development.
 */

// Hostinger / Production MySQL credentials
$db_host = getenv('DB_HOST') ?: 'localhost';
$db_name = getenv('DB_NAME') ?: 'carolines_place';
$db_user = getenv('DB_USER') ?: 'root';
$db_pass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';
$db_port = getenv('DB_PORT') ?: '3306';

function getDb() {
    global $db_host, $db_name, $db_user, $db_pass, $db_port;
    static $pdo = null;

    if ($pdo !== null) {
        return $pdo;
    }

    // Try MySQL first if configured or in production
    if (!empty(getenv('DB_NAME')) || (!empty($db_pass) && $db_user !== 'root')) {
        try {
            $dsn = "mysql:host={$db_host};port={$db_port};dbname={$db_name};charset=utf8mb4";
            $pdo = new PDO($dsn, $db_user, $db_pass, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
            return $pdo;
        } catch (PDOException $e) {
            // Fall through to SQLite if MySQL fails
            error_log("MySQL connection failed: " . $e->getMessage());
        }
    }

    // SQLite connection (ideal for development, testing, and offline preview)
    $sqlitePath = __DIR__ . '/carolines.sqlite';
    try {
        $pdo = new PDO("sqlite:" . $sqlitePath, null, null, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        return $pdo;
    } catch (PDOException $e) {
        die("Database connection failed: " . htmlspecialchars($e->getMessage()));
    }
}

/**
 * Format Nigerian Naira currency string
 */
function priceFmt($amount) {
    if ($amount === null || $amount === '') return '₦0';
    $num = round((float)$amount);
    return '₦' . number_format($num);
}
