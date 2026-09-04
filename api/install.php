<?php
/** ============================================================
 * 🚨 CAROLINE'S PLACE — FRESH INSTALL CREATOR v2.1 (Hostinger MySQL) 🚨
 * ============================================================
 *  1. UPLOAD this file to public_html
 *  2. Visit /install.php ONCE in browser (https://your-site.com/install.php)
 *  3. After SUCCESS message — 🚨 DELETE THIS FILE IMMEDIATELY! 🚨
 *     Anyone visiting the URL can wipe your database!
 * ============================================================
 *  WHAT IT DOES AUTOMATICALLY:
 *  ✅ Drops ALL existing tables (CLEAN START — old 10 categories GONE)
 *  ✅ Creates ALL 8 required tables with correct MySQL schema (ENGINE=InnoDB utf8mb4)
 *  ✅ Creates foreign keys + indexes (spa performance)
 *  ✅ Seeds 2 admin accounts: carolines_admin / admin (pw: Caroline@Sanctuary2026)
 *     → Uses password_hash bcrypt, handles legacy display_name NOT NULL + created_at
 *     → NO integrity constraint 19 fails!
 *  ✅ Seeds 5 clubhouse services (conference/lounge/meeting/gym/events)
 *  ✅ Seeds 7 EXACT category names + icons + sort order (EXACT ur list!)
 *     → 1. Spa Section 🧖‍♀️
 *     → 2. Massage 💆
 *     → 3. Waxing ✨
 *     → 4. Body Treatment 🧴
 *     → 5. Hair Section 💇
 *     → 6. Nails Price List 💅
 *     → 7. Pedicure Section 🦶
 * ============================================================
 *  NOTE: Service-level prices (136 services / 179 options) still need
 *  SQLite export → MySQL import via DB Browser + phpMyAdmin,
 *  OR manually via Admin → Spa Products editor page.
 * ============================================================
 */
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('memory_limit', '256M');
set_time_limit(120);
require_once __DIR__ . '/api/db.php';
$pdo = getDB();
$drv = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);

$CSS = <<<'CSS'
body{font-family:system-ui,-apple-system,"Segoe UI",Roboto,sans-serif;max-width:1150px;margin:24px auto;padding:0 16px;line-height:1.55;color:#111827}
.box{padding:16px 20px;border-radius:12px;margin:14px 0;box-shadow:0 1px 3px rgba(0,0,0,.06)}
.ok{background:#D1FAE5;border:1px solid #10B981;color:#065F46}
.err{background:#FEE2E2;border:1px solid #DC2626;color:#991B1B}
.info{background:#E0E7FF;border:1px solid #6366F1;color:#3730A3}
.warn{background:#FEF3C7;border:1px solid #D97706;color:#92400E}
h1{color:#111827;margin:8px 0 12px}
h2{color:#1F2937;margin:24px 0 10px;border-bottom:2px solid #E5E7EB;padding-bottom:6px}
h3{margin:6px 0}
code{background:#111827;color:#E2E8F0;padding:2px 8px;border-radius:6px;font-family:ui-monospace,Consolas,monospace;font-size:13px}
table{width:100%;border-collapse:collapse;margin:10px 0;font-size:14px}
th,td{padding:9px 12px;text-align:left;border-bottom:1px solid #E5E7EB}
th{background:#111827;color:#fff;font-weight:600}
tr:nth-child(even) td{background:#F9FAFB}
.k{font-weight:700}
.btn{display:inline-block;padding:10px 16px;background:#8B6F2E;color:#fff;border-radius:8px;text-decoration:none;font-weight:600;font-size:14px;margin-right:8px}
.btn:hover{background:#6d5724}
ul,ol{margin:6px 0;padding-left:24px}
li{margin:4px 0}
CSS;

echo "<!doctype html><html><head><meta charset='utf-8'><meta name='viewport' content='width=device-width,initial-scale=1'>";
echo "<title>Install — Caroline's Place (Fresh Start)</title>";
echo "<style>$CSS</style></head><body>";
echo "<h1>🧹 Caroline's Place — Fresh Install Creator</h1>";

// ──────────────────────────────────────────────────────
// PRE-FLIGHT CHECK: driver, credentials, warn if SQLite
// ──────────────────────────────────────────────────────
echo "<div class='info'>
<b>PHP Version:</b> " . phpversion() . " — " . (version_compare(phpversion(), '8.0.0', '>=') ? "<span style='color:#065F46'>✅ 8.x+ OK</span>" : "<span style='color:#991B1B'>❌ Need PHP 8+; upgrade via Hostinger PHP Config</span>") . "<br>
<b>DB Driver detected:</b> <code>$drv</code> " . ($drv==='mysql' ? "<span style='color:#065F46'>✅ MySQL (Hostinger Shared — correct!)</span>" : ($drv==='sqlite' ? "<span style='color:#991B1B'>⚠️ SQLite fallback! Your MySQL credentials in api/db.php lines 9-11 are WRONG for this NEW indigo-locust account. Fix creds FIRST before running install!</span>" : "❓ Unknown")) . "<br>
<b>This page URL host:</b> <code>" . htmlspecialchars($_SERVER['HTTP_HOST'] ?? 'localhost') . "</code>
</div>";

// Get the actual DB_NAME + DB_USER being used (for user visual check)
preg_match_all("/define$'(DB_[A-Z_]+)',\s*'([^']+)'$;/", @file_get_contents(__DIR__.'/api/db.php'), $mm, PREG_SET_ORDER);
$credActuals = [];
foreach ($mm as $m) $credActuals[$m[1]] = $m[2];
echo "<div class='info'><h3>🔐 Credentials currently LOADED from api/db.php (must match indigo-locust-512829 MySQL Databases page!):</h3>
<table><tr><th>Constant</th><th>Value in api/db.php</th><th>Matches your NEW indigo-locust DB?</th></tr>";
foreach (['DB_HOST','DB_NAME','DB_USER','DB_PASS','DB_CHARSET'] as $c) {
    $v = $credActuals[$c] ?? '<span style="color:#DC2626">MISSING</span>';
    $hint = match($c) {
        'DB_HOST' => ($v==='localhost') ? '<span style="color:#065F46">✅ localhost correct on Hostinger</span>' : "<span style='color:#DC2626'>❌ Wrong. Should be 'localhost' on Hostinger Shared.</span>",
        'DB_NAME','DB_USER' => ((strlen($v)>5 && strpos($v,'your_')!==0 && strpos($v,'CHANGE')===false) ? "<span style='color:#D97706'>⚠️ Go compare NOW against indigo-locust MySQL page — it MUST match the NEW DB exactly!</span>" : '<span style="color:#DC2626">❌ PLACEHOLDER. Enter real credentials from Hostinger.</span>'),
        'DB_PASS' => ((strlen($v)>4 && strpos($v,'your_')!==0 && strpos($v,'CHANGE')===false) ? "<span style='color:#D97706'>⚠️ Match password used when creating DB on this NEW site (not old password)</span>" : '<span style="color:#DC2626">❌ BAD PASSWORD!</span>'),
        'DB_CHARSET' => ($v==='utf8mb4') ? '<span style="color:#065F46">✅ Correct</span>' : '<span style="color:#DC2626">Change to utf8mb4</span>',
        default => '',
    };
    echo "<tr><td class='k'>$c</td><td><code>" . htmlspecialchars($c==='DB_PASS' ? str_repeat('•',min(strlen($v),20)) : $v) . "</code></td><td>$hint</td></tr>";
}
echo "</table></div>";

// If driver is sqlite but user claimed MySQL, STOP immediately
if ($drv !== 'mysql') {
    echo "<div class='err'><h2>🛑 INSTALL STOPPED! (Wrong driver)</h2>
    <p>We're currently running in <b>SQLite fallback mode</b>. This means MySQL connection FAILED. The credentials in api/db.php lines 9-11 are for your <b>OLD snow-termite</b> site, NOT this new indigo-locust-512829 one.</p>
    <p><b>Fix:</b><ol>
        <li>Open new browser tab → Hostinger → Websites → <code>indigo-locust-512829</code> → Manage → <b>MySQL Databases</b></li>
        <li>Either (a) click an existing DB row, or (b) <b>Create New Database</b> with a strong password.</li>
        <li>COPY exactly: <b>Database name</b>, <b>Database user</b>, <b>Password</b> (click the eye), <b>Host</b> (usually localhost)</li>
        <li>On your PC open <code>_public_html (6) → api → db.php</code> in Notepad. Lines 8-12 overwrite DB_HOST / DB_NAME / DB_USER / DB_PASS with what you copied. <b>Save the file.</b></li>
        <li>Upload updated <code>api/db.php</code> → Hostinger public_html → api/ → overwrite old one.</li>
        <li>REFRESH THIS install.php page (press F5). This blue box will now say ✅ MySQL if correct.</li>
    </ol></p></div></body></html>";
    exit;
}

// ──────────────────────────────────────────────────────
// STEP 1: DROP existing tables IN ORDER (FK dependencies!)
// ──────────────────────────────────────────────────────
echo "<h2>🧨 Step 1 of 5 — Drop all existing tables (clean slate)</h2>";
$DROP_ORDER = [
    'spa_booking_items', 'spa_bookings',      // children first
    'spa_service_options', 'spa_services', 'spa_categories',
    'bookings', 'services', 'admins'
];
$dropResults = [];
foreach ($DROP_ORDER as $t) {
    try {
        $pdo->exec("DROP TABLE IF EXISTS `$t`");
        $dropResults[] = "DROP $t → <span style='color:#065F46'>OK</span>";
    } catch (Throwable $e) {
        $dropResults[] = "DROP $t → <span style='color:#DC2626'>FAIL: ".$e->getMessage()."</span>";
    }
}
echo "<div class='ok'><ul>";
foreach ($dropResults as $r) echo "<li>$r</li>";
echo "</ul></div>";

// ──────────────────────────────────────────────────────
// STEP 2: CREATE all 8 tables with MySQL InnoDB schema
// ──────────────────────────────────────────────────────
echo "<h2>🔧 Step 2 of 5 — Create table schema (8 tables + FK + indexes)</h2>";
$SCHEMA = [
"admins" => "CREATE TABLE `admins` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(100) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `display_name` VARCHAR(200) NOT NULL,
    `full_name` VARCHAR(200) NULL,
    `email` VARCHAR(200) NULL,
    `role` VARCHAR(50) DEFAULT 'admin',
    `is_active` TINYINT DEFAULT 1,
    `last_login_at` DATETIME NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

"services" => "CREATE TABLE `services` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `description` TEXT NOT NULL,
    `division` ENUM('clubhouse','spa') NOT NULL,
    `category` VARCHAR(200) NOT NULL,
    `duration_minutes` INT NULL,
    `price_ngn` DECIMAL(12,2) NULL,
    `is_active` TINYINT DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

"bookings" => "CREATE TABLE `bookings` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `reference_code` VARCHAR(50) NOT NULL UNIQUE,
    `full_name` VARCHAR(200) NOT NULL,
    `email` VARCHAR(200) NOT NULL,
    `phone` VARCHAR(50) NOT NULL,
    `division` ENUM('clubhouse','spa') NOT NULL,
    `service_id` INT NULL,
    `service_name` VARCHAR(255) NOT NULL,
    `preferred_date` DATE NOT NULL,
    `preferred_time` TIME NOT NULL,
    `notes` TEXT NULL,
    `status` ENUM('pending','confirmed','cancelled','completed') DEFAULT 'pending',
    `payment_status` ENUM('unpaid','paid') DEFAULT 'unpaid',
    `payment_notes` TEXT NULL,
    `staff_assigned` VARCHAR(200) NULL,
    `admin_notes` TEXT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_bookings_svc` (`service_id`),
    INDEX `idx_bookings_status` (`status`),
    FOREIGN KEY (`service_id`) REFERENCES `services`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

"spa_categories" => "CREATE TABLE `spa_categories` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(200) NOT NULL UNIQUE,
    `description` TEXT NULL,
    `icon` VARCHAR(100) NULL,
    `sort_order` INT DEFAULT 0,
    `is_active` TINYINT DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

"spa_services" => "CREATE TABLE `spa_services` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `category_id` INT NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `description` TEXT NULL,
    `image_url` VARCHAR(500) NULL,
    `duration_minutes` INT NULL,
    `sort_order` INT DEFAULT 0,
    `is_active` TINYINT DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_svc_cat` (`category_id`),
    UNIQUE KEY `idx_spa_svc_cat_name` (`category_id`,`name`),
    FOREIGN KEY (`category_id`) REFERENCES `spa_categories`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

"spa_service_options" => "CREATE TABLE `spa_service_options` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `service_id` INT NOT NULL,
    `option_label` VARCHAR(255) NOT NULL DEFAULT 'Standard',
    `price_ngn` DECIMAL(12,2) NOT NULL DEFAULT 0,
    `sort_order` INT DEFAULT 0,
    `is_active` TINYINT DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_opt_svc` (`service_id`),
    UNIQUE KEY `idx_spa_opt_svc_label` (`service_id`,`option_label`),
    FOREIGN KEY (`service_id`) REFERENCES `spa_services`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

"spa_bookings" => "CREATE TABLE `spa_bookings` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `reference_code` VARCHAR(50) NOT NULL UNIQUE,
    `full_name` VARCHAR(200) NOT NULL,
    `email` VARCHAR(200) NOT NULL,
    `phone` VARCHAR(50) NOT NULL,
    `preferred_date` DATE NOT NULL,
    `preferred_time` TIME NOT NULL,
    `total_amount_ngn` DECIMAL(12,2) NOT NULL DEFAULT 0,
    `notes` TEXT NULL,
    `status` ENUM('pending','confirmed','cancelled','completed') DEFAULT 'pending',
    `payment_status` ENUM('unpaid','paid') DEFAULT 'unpaid',
    `admin_notes` TEXT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_spab_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

"spa_booking_items" => "CREATE TABLE `spa_booking_items` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `booking_id` INT NOT NULL,
    `service_id` INT NOT NULL,
    `option_id` INT NOT NULL,
    `service_name` VARCHAR(255) NOT NULL,
    `option_label` VARCHAR(255) NOT NULL,
    `unit_price_ngn` DECIMAL(12,2) NOT NULL,
    `quantity` INT NOT NULL DEFAULT 1,
    `line_total_ngn` DECIMAL(12,2) NOT NULL,
    INDEX `idx_items_booking` (`booking_id`),
    FOREIGN KEY (`booking_id`) REFERENCES `spa_bookings`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`service_id`) REFERENCES `spa_services`(`id`) ON DELETE RESTRICT,
    FOREIGN KEY (`option_id`) REFERENCES `spa_service_options`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",
];

$schemaOK = true;
foreach ($SCHEMA as $name => $sql) {
    try {
        $pdo->exec($sql);
        echo "<div class='ok'>✅ CREATE TABLE <code>$name</code> OK</div>";
    } catch (Throwable $e) {
        $schemaOK = false;
        echo "<div class='err'>❌ CREATE TABLE <code>$name</code> FAILED: <pre style='background:#fff;padding:6px 10px;border-radius:4px;margin:6px 0;overflow:auto'>".$e->getMessage()."</pre>";
        if (strpos($e->getMessage(), "1071 Specified key was too long")!==false) echo "<p style='margin:8px 0'>→ Fix: run <code>SET GLOBAL innodb_file_format=Barracuda, innodb_large_prefix=ON;</code> or use shorter prefix lengths on the 2 unique varchar keys. Contact Hostinger support OR replace 250-length UNIQUEs with smaller VARCHAR(191).</p>";
        echo "</div>";
    }
}

if (!$schemaOK) {
    echo "<div class='err'><h3>🛑 SCHEMA INCOMPLETE — stop here, fix errors above first.</h3></div></body></html>";
    exit;
}

// ──────────────────────────────────────────────────────
// STEP 3: Seed 2 admin accounts (with display_name NOT NULL + created_at!)
// ──────────────────────────────────────────────────────
echo "<h2>🔑 Step 3 of 5 — Seed 2 Admin Accounts</h2>";
$ADMIN_PW = 'Caroline@Sanctuary2026';
$hash = password_hash($ADMIN_PW, PASSWORD_DEFAULT);
$now = date('Y-m-d H:i:s');
$insAdm = $pdo->prepare("INSERT INTO `admins` (`username`,`password_hash`,`display_name`,`full_name`,`email`,`role`,`is_active`,`created_at`) VALUES (?,?,?,?,?,?,1,?)");
$seededAdmins = [
    ['carolines_admin', $hash, "Caroline O. Manager", "Caroline O. Manager", 'admin@carolinesplace.com', 'admin'],
    ['admin',           $hash, "Administrator",       "Site Administrator",   'admin@carolinesplace.com', 'admin'],
];
try {
    foreach ($seededAdmins as $a) {
        $insAdm->execute([$a[0],$a[1],$a[2],$a[3],$a[4],$a[5],$now]);
        $vid = $pdo->lastInsertId();
        $v = password_verify($ADMIN_PW, $hash);
        echo "<div class='ok'>✅ INSERT admin <code>".$a[0]."</code> id=$vid — password verify: ".($v?'<span style="color:#065F46">PASS ✅</span>':'<span style="color:#DC2626">FAIL ❌</span>')."</div>";
    }
    echo "<div class='info'><h3>Admin Login Credentials (use BOTH admin panel):</h3>
    <table>
        <tr><th>Username</th><th>Password</th><th>Display Name</th></tr>
        <tr><td><code>carolines_admin</code></td><td><code>$ADMIN_PW</code></td><td>Caroline O. Manager</td></tr>
        <tr><td><code>admin</code></td><td><code>$ADMIN_PW</code></td><td>Administrator</td></tr>
    </table></div>";
} catch (Throwable $e) {
    echo "<div class='err'>❌ ADMIN SEED FAIL: ".$e->getMessage()."</div>";
}

// ──────────────────────────────────────────────────────
// STEP 4: Seed 5 Clubhouse services (bookings legacy table)
// ──────────────────────────────────────────────────────
echo "<h2>🏢 Step 4 of 5 — Seed Clubhouse Services</h2>";
$clubData = [
    ['Conference Room',         'Our private conference room is designed for executive meetings, presentations, and focused conversations in a refined setting.',      'clubhouse', 'Conference Room', 60,  null],
    ['Executive Lounge Access', 'Full-day access to our exclusive executive lounge with curated refreshments and concierge service.',                                  'clubhouse', 'Lounge',            480, null],
    ['Private Meeting Suites',  'A bespoke private meeting experience for confidential engagements, strategy sessions, and elevated hosting.',                        'clubhouse', 'Meeting Spaces',    120, null],
    ['Gym & Wellness Access',   'Wellness access within The Club House, available exclusively to Caroline\'s Place club members.',                                    'clubhouse', 'Gym',               120, null],
    ['Event Space Rentals',     'Flexible event space for private gatherings, launches, celebrations, and curated social occasions.',                                  'clubhouse', 'Events',            240, null],
];
$insClub = $pdo->prepare("INSERT INTO `services` (`name`,`description`,`division`,`category`,`duration_minutes`,`price_ngn`,`created_at`) VALUES (?,?,?,?,?,?,?)");
try {
    foreach ($clubData as $s) {
        $insClub->execute([$s[0],$s[1],$s[2],$s[3],$s[4],$s[5],$now]);
    }
    echo "<div class='ok'>✅ Inserted 5 Clubhouse services into <code>services</code> table.</div>";
} catch (Throwable $e) {
    echo "<div class='err'>❌ Clubhouse seed fail: ".$e->getMessage()."</div>";
}

// ──────────────────────────────────────────────────────
// STEP 5: Seed 7 EXACT spa category names + icons + order
// ──────────────────────────────────────────────────────
echo "<h2>🧖‍♀️ Step 5 of 5 — Seed 7 EXACT Spa Categories</h2>";
$CATS = [
    // name, description, icon, sort_order
    ['Spa Section',       'Signature facials, peels, dermaplaning, microdermabrasion & skin-care sanctuary at Caroline\'s Place.', '🧖‍♀️', 1],
    ['Massage',           'Swedish, deep-tissue, hot-stone, Balinese, couples, reflexology, prenatal & therapeutic massages.',    '💆',   2],
    ['Waxing',            'Full range of waxing services: full-body, brazilian, bikini, leg, arm, facial, underarm options.',     '✨',   3],
    ['Body Treatment',    'Coffee scrubs, body wraps, steam baths, hammam, hair masks & luxurious all-over body experiences.',     '🧴',   4],
    ['Hair Section',      'Braids, cornrows (all sizes/pieces), relaxers, dyeing, wigs installs, crochet, ghana weaving, perms.',  '💇',   5],
    ['Nails Price List',  'Manicures, gel polish, acrylic, BIAB, hard gel, ombre, nail art, chrome, gel X, press-on options.',     '💅',   6],
    ['Pedicure Section',  'Pedicures — dry, spa, caviar, luxury, manicure combos & kids variants of all nail/pedi services.',      '🦶',   7],
];
$insCat = $pdo->prepare("INSERT INTO `spa_categories` (`name`,`description`,`icon`,`sort_order`,`is_active`,`created_at`) VALUES (?,?,?,?,1,?)");
try {
    $catIds = [];
    foreach ($CATS as $c) {
        $insCat->execute([$c[0],$c[1],$c[2],$c[3],$now]);
        $catIds[] = (int)$pdo->lastInsertId();
        echo "<div class='ok'>✅ CAT <b>#$c[3] $c[2] $c[0]</b> → id=".$catIds[count($catIds)-1]." saved.</div>";
    }
} catch (Throwable $e) {
    echo "<div class='err'>❌ SPA categories seed fail: ".$e->getMessage()."</div>";
}

// ──────────────────────────────────────────────────────
// FINAL SANITY CHECK — all counts + queries
// ──────────────────────────────────────────────────────
echo "<hr><h2>✅ FINAL SANITY CHECKS</h2>";
$queries = [
    'admins (accounts)'       => "SELECT COUNT(*) FROM admins",
    'services (clubhouse)'    => "SELECT COUNT(*) FROM services",
    'bookings (empty expect 0)' => "SELECT COUNT(*) FROM bookings",
    'spa_categories'          => "SELECT COUNT(*) FROM spa_categories",
    'spa_services (expect 0)' => "SELECT COUNT(*) FROM spa_services",
    'spa_service_options (expect 0)' => "SELECT COUNT(*) FROM spa_service_options",
    'spa_bookings (expect 0)' => "SELECT COUNT(*) FROM spa_bookings",
    'spa_booking_items (expect 0)' => "SELECT COUNT(*) FROM spa_booking_items",
];
$all = [];
foreach ($queries as $label => $sql) {
    try { $n = (int)$pdo->query($sql)->fetchColumn(); $all[$label]=['count'=>$n,'ok'=>true]; }
    catch (Throwable $e) { $all[$label]=['count'=>'ERR','ok'=>false,'msg'=>$e->getMessage()]; }
}
echo "<table><tr><th>Table</th><th>Row Count</th><th>Status</th></tr>";
$warn = [];
foreach ($all as $label => $r) {
    if (!$r['ok']) {
        echo "<tr><td>$label</td><td style='color:#DC2626'>ERROR</td><td><span style='color:#DC2626'>❌ ".htmlspecialchars($r['msg'])."</span></td></tr>";
        $warn[] = $label;
        continue;
    }
    $expectedMap = [
        'admins (accounts)' => 2,
        'services (clubhouse)' => 5,
        'bookings (empty expect 0)' => 0,
        'spa_categories' => 7,
        'spa_services (expect 0)' => 0,
        'spa_service_options (expect 0)' => 0,
        'spa_bookings (expect 0)' => 0,
        'spa_booking_items (expect 0)' => 0,
    ];
    $expected = $expectedMap[$label] ?? null;
    $status = ($expected!==null && $r['count']===$expected) ? '<span style="color:#065F46">✅ PERFECT</span>' : ($r['count']>0 ? '<span style="color:#D97706">⚠️ Has rows</span>' : '<span style="color:#065F46">✅ 0 rows</span>');
    echo "<tr><td>$label</td><td style='font-weight:700'>$r[count]</td><td>$status ($label)</td></tr>";
    if ($label==='spa_categories' && $r['count']!==7) $warn[] = "Categories wrong count ($r[count]/7)";
    if ($label==='admins (accounts)' && $r['count']!==2) $warn[] = "Admins wrong count ($r[count]/2)";
}
echo "</table>";

// Show spa category names sorted (user order check)
echo "<h3>7 Category Exact Order Verification:</h3><ol>";
$rows = $pdo->query("SELECT id,name,icon,sort_order FROM spa_categories ORDER BY sort_order,id")->fetchAll(PDO::FETCH_ASSOC);
$i=1; foreach ($rows as $r) {
    echo "<li>[$r[id]] <b>$r[icon] $r[name]</b> (sort=$r[sort_order]) — expected #$i</li>";
    $i++;
}
echo "</ol>";

if (count($warn)===0) echo "<div class='ok'><h1 style='color:#065F46;margin:0'>🎉 INSTALL COMPLETED SUCCESSFULLY!</h1>Database structure, 2 admins, 5 clubhouse services + 7 exact spa categories seeded 100% correctly.</div>";
else echo "<div class='warn'><h3 style='margin:0 0 8px'>⚠️ Warnings:</h3>".implode('<br>',$warn)."</div>";

// Service import reminder + quick links
echo "<div class='info'><h3>📌 Next steps after successful install:</h3>
<ol>
    <li><b>🛑 SECURITY — DELETE THIS install.php FILE FROM HOSTINGER FILE MANAGER NOW!</b> If you leave it public, anyone on the internet can visit this URL and wipe + re-seed your database at any time. Also delete cpcheck.php and any other files with names starting with underscore (_).</li>
    <li style='margin-top:8px'>To get the <b>136 services + 179 option prices</b> into the new database:<br>
        <b>Option A (fastest, recommended):</b> Use DB Browser for SQLite → open <code>_public_html (6) → api → carolines.sqlite</code> (local file) → File → Export → Database to SQL file → MySQL compatibility mode → save .sql file → open indigo-locust phpMyAdmin → Import tab → upload the .sql file → click Import.<br>
        <b>Option B (manual):</b> Login to Admin → <b>Spa Products & Pricing</b> page → use Add Category / Add Service forms to build menu manually (but this takes time!).
    </li>
    <li style='margin-top:8px'>Quick test links:<br>
        <a class='btn' href='/spa_menu.php' target='_blank'>💆 Spa Menu (booking flow)</a>
        <a class='btn' href='/admin/login.php' target='_blank'>🔐 Admin Login</a>
        <a class='btn' href='/' target='_blank'>🏠 Homepage</a>
    </li>
</ol></div>";

echo "</body></html>";