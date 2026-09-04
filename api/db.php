<?php
/** ============================================================
 * 🗄️ DATABASE CONNECTION — Caroline's Place (api/db.php)
 * ============================================================
 * 3-TIER FALLBACK:
 *   1. FIRST TRY Hostinger MySQL → lines 8-11 creds below
 *   2. IF ANY FAILURE, TRY local SQLite file inside api/ folder
 *   3. IF SQLite unwritable, use system temp directory
 * NO SIDE EFFECTS! No extra code, no appended seeder data!
 * ============================================================
 * 🚨 EDIT BELOW LINES 9-12 WITH YOUR NEW INDIGO-LOCUST-512829 CREDENTIALS! 🚨
 * ============================================================*/

ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);

define('DB_HOST',    'localhost');
// ============================================================
// 🚨 HOSTINGER PRODUCTION MODE (CURRENTLY ACTIVE FOR UPLOAD!)
//    Live DB: u989099624_caros @ carolinesplace.org
// ============================================================
define('DB_NAME',    'u989099624_caros');
define('DB_USER',    'u989099624_caros');
define('DB_PASS',    'Lumid33.');
define('DB_CHARSET', 'utf8mb4');
// ============================================================
// 💻 LOCAL PREVIEW MODE — COMMENT OUT THE 4 PROD LINES ABOVE, THEN UNCOMMENT BELOW ONLY FOR LOCALHOST TESTING
// define('DB_NAME',    'REPLACE_ME_WITH_INDIGO_DB_NAME');
// define('DB_USER',    'REPLACE_ME_WITH_INDIGO_DB_USER');
// define('DB_PASS',    'REPLACE_ME_WITH_INDIGO_PASSWORD');
// ============================================================

/** ============================================================
 * 💰 GLOBAL PRICE FORMATTER — EVERY page needs this!
 *  Called by: spa_menu.php, confirmation.php, admin/dashboard.php,
 *             admin/spa_products.php, api/admin_spa_items.php, etc.
 *  NEVER DELETE THIS FUNCTION — Undefined = FATAL BLANK SCREEN!
 * ============================================================ */
function priceFmt(float|int|null $amount): string {
    if ($amount === null) return '₦0';
    $naira = (int)round((float)$amount);
    return '₦' . number_format($naira, 0, '.', ',');
}

function _cpDbFatal(string $title, string $body, Throwable $ex = null): never {
    http_response_code(500);
    $dbg = '';
    if ($ex !== null && isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] === '127.0.0.1') {
        $dbg = "\n<!-- DEBUG TRACE (localhost only):\n" . $ex->getMessage() . "\n" . $ex->getTraceAsString() . "\n-->";
    }
    echo '<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">';
    echo '<title>Site Temporary Unavailable</title>';
    echo '<style>body{margin:0;font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif;background:#FAF9F6;color:#1F2937;}';
    echo '.wrap{min-height:100vh;display:flex;align-items:center;justify-content:center;padding:28px;}.card{max-width:520px;width:100%;background:#fff;border-radius:14px;box-shadow:0 10px 30px rgba(17,17,17,.08);padding:32px 28px;}';
    echo '.h1{color:#8B6F2E;font-family:Georgia,"Times New Roman",serif;font-size:26px;margin:0 0 12px;}';
    echo '.p{line-height:1.55;margin:0 0 14px;color:#374151;font-size:15px;}';
    echo '.ul{margin:0 0 14px;padding-left:20px;line-height:1.6;color:#374151;}';
    echo '.btn{display:inline-block;padding:10px 16px;background:#8B6F2E;color:#fff;border-radius:8px;text-decoration:none;font-weight:600;font-size:14px;}';
    echo '</style></head><body><div class="wrap"><div class="card">';
    echo '<h1 class="h1">' . htmlspecialchars($title) . '</h1>';
    echo '<p class="p">' . $body . '</p>';
    echo '<a class="btn" href="/">← Back Home</a>';
    echo '</div></div>'.$dbg.'</body></html>';
    exit(1);
}

function getDB(): PDO {
    static $pdo = null;
    if ($pdo instanceof PDO) return $pdo;

    /* ──────────────────────────────────────────────
     *  FALLBACK #1: HOSTINGER MYSQL
     * ──────────────────────────────────────────────*/
    $needsReal = (
        DB_HOST !== '' &&
        strpos(DB_NAME, 'REPLACE_') !== 0 &&
        strpos(DB_USER, 'REPLACE_') !== 0 &&
        strpos(DB_PASS, 'REPLACE_') !== 0
    );
    try {
        if ($needsReal) {
            $dsn = 'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset='.DB_CHARSET;
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::ATTR_TIMEOUT            => 5,
            ]);
            return $pdo;
        }
    } catch (Throwable $mysqlEx) {
        /* ── 🚨 REAL CREDS but MySQL FAILED → DIE RED, NEVER fall back! ──
         * (Silent fallback to empty SQLite on Hostinger causes the famous
         *  "seed said success but menu shows 0 services" wahala) */
        if ($needsReal) {
            $title = 'Booking system temporary unavailable (MySQL error)';
            $body  = "<b>We could not connect to your Hostinger MySQL database.</b><br><br>"
                    ."<b>Why you see this (NEW behaviour — no more silent 0-services fallback):</b><br>"
                    ."You have real credentials in <code>api/db.php</code>, so we refuse to silently fall back to an empty SQLite file (which caused empty menus after seed).<br><br>"
                    ."<b>MySQL error message for your Hostinger support:</b><br>"
                    ."<code style=\"background:#FEE2E2;color:#991B1B;\">".htmlspecialchars($mysqlEx->getMessage())."</code><br><br>"
                    ."<small style=\"color:#6B7280\"><b>What to do now:</b><br>"
                    ."<b>1)</b> Double-check values in <code>api/db.php lines 18-24</code> match your Hostinger MySQL Databases page exactly (DB_NAME, DB_USER, DB_PASS).<br>"
                    ."<b>2)</b> In Hostinger → MySQL Databases → click your DB user → Manage → Ensure password is current (not expired).<br>"
                    ."<b>3)</b> In Hostinger → Remote MySQL → make sure 'localhost' is in allowed hosts.<br>"
                    ."<b>4)</b> Run <code>checkdb.php</code> diagnostic for full line-by-line checklist.</small>";
            _cpDbFatal($title, $body, $mysqlEx);
        }
    }

    /* ──────────────────────────────────────────────
     *  FALLBACK #2: LOCAL SQLITE (ONLY for localhost / REPLACE_ placeholder creds)
     * ──────────────────────────────────────────────*/
    $sqlitePath1 = __DIR__ . '/carolines.sqlite';
    try {
        if ((file_exists($sqlitePath1) && is_writable($sqlitePath1)) || is_writable(__DIR__)) {
            $pdo = new PDO('sqlite:'.$sqlitePath1);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $pdo->exec("PRAGMA journal_mode = WAL; PRAGMA busy_timeout = 3000; PRAGMA foreign_keys = ON;");
            return $pdo;
        }
    } catch (Throwable $sqlite1Ex) { /* continue */ }

    /* ──────────────────────────────────────────────
     *  FALLBACK #3: SQLITE IN SYSTEM TMP DIR
     * ──────────────────────────────────────────────*/
    $tmpDir = sys_get_temp_dir() ?: '/tmp';
    $unique = substr(md5(__DIR__ . ($_SERVER['HTTP_HOST'] ?? 'cp')), 0, 10);
    $sqlitePath2 = rtrim($tmpDir, '/\\') . DIRECTORY_SEPARATOR . 'carolines_'.$unique.'.sqlite';
    try {
        $pdo = new PDO('sqlite:'.$sqlitePath2);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $pdo->exec("PRAGMA journal_mode = WAL; PRAGMA busy_timeout = 3000; PRAGMA foreign_keys = ON;");
        return $pdo;
    } catch (Throwable $ex) {
        $title = 'Our booking system is temporarily undergoing maintenance';
        $body  = "We apologize for the inconvenience. Our team has been notified and this will be resolved shortly.<br><br>"
                ."<small style='color:#6B7280'>If you are the site administrator:<br>"
                ."<b>1)</b> Verify the MySQL credentials in <code>api/db.php lines 9-12</code> are correct for your hosting account.<br>"
                ."<b>2)</b> Confirm the database user has ALL privileges (SELECT, INSERT, UPDATE, DELETE, CREATE, ALTER, INDEX, DROP) on your database.<br>"
                ."<b>3)</b> If using SQLite locally, make sure the folder <code>api/</code> has write permissions (CHMOD 755 or 777).</small>";
        _cpDbFatal($title, $body, $ex);
    }
}
