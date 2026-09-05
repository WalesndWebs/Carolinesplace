<?php
/**
 * Router script for PHP built-in web server (emulating Apache mod_rewrite / Hostinger environment)
 */
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// If the file exists directly in public root (assets, images, css, js, etc.), let the server deliver it
if ($uri !== '/' && file_exists(__DIR__ . $uri)) {
    return false;
}

// Clean URL routing: e.g. /clubhouse -> /clubhouse.php
if (file_exists(__DIR__ . $uri . '.php')) {
    require __DIR__ . $uri . '.php';
    exit;
}

// Root
if ($uri === '/' || $uri === '/index') {
    require __DIR__ . '/index.php';
    exit;
}

// Admin clean routing
if ($uri === '/admin' || $uri === '/admin/') {
    require __DIR__ . '/admin/dashboard.php';
    exit;
}

if (str_starts_with($uri, '/admin/')) {
    $adminFile = __DIR__ . $uri . '.php';
    if (file_exists($adminFile)) {
        require $adminFile;
        exit;
    }
}

// API clean routing
if (str_starts_with($uri, '/api/')) {
    $apiFile = __DIR__ . $uri . '.php';
    if (file_exists($apiFile)) {
        require $apiFile;
        exit;
    }
}

// Review route fallback
if ($uri === '/spa_menu/review') {
    require __DIR__ . '/spa_menu.php';
    exit;
}

// Default fallback to 404 or index
http_response_code(404);
echo "404 Not Found";
