<?php
// Environment Detection
define('SITE_NAME', 'BLACKHEAD');
$is_local = (
    php_sapi_name() === 'cli'
    || (isset($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME'] === 'localhost')
    || (isset($_SERVER['SERVER_ADDR']) && $_SERVER['SERVER_ADDR'] === '127.0.0.1')
);

if ($is_local) {
    // LOCAL SETTINGS (XAMPP)
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'blackhead');
    define('DB_USER', 'root');
    define('DB_PASS', getenv('DB_PASS') ?: '');
    define('SITE_URL', 'http://localhost/BlackHead/');
} else {
    // LIVE SETTINGS (Flexible for Hostinger/InfinityFree)
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
    define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
    define('DB_NAME', getenv('DB_NAME') ?: 'if0_41602905_blackhead');
    define('DB_USER', getenv('DB_USER') ?: 'if0_41602905');
    define('DB_PASS', getenv('DB_PASS') ?: '7A3BzXzQFaDb');
    define('SITE_URL', $protocol . ($_SERVER['HTTP_HOST'] ?? 'localhost') . '/');
}

// Absolute Root Path for server-side operations
define('ROOT_PATH', dirname(dirname(__DIR__)) . '/');
define('UPLOAD_DIR', 'assets/images/');
define('ADMIN_EMAIL', 'admin@blackhead.com');

// Google OAuth Configuration - Require environment variables
if (!$is_local) {
    $google_client_id = getenv('GOOGLE_CLIENT_ID');
    $google_client_secret = getenv('GOOGLE_CLIENT_SECRET');
    if (!$google_client_id || !$google_client_secret) {
        die("Google OAuth credentials not configured. Set GOOGLE_CLIENT_ID and GOOGLE_CLIENT_SECRET environment variables.");
    }
    define('GOOGLE_CLIENT_ID', $google_client_id);
    define('GOOGLE_CLIENT_SECRET', $google_client_secret);
} else {
    define('GOOGLE_CLIENT_ID', getenv('GOOGLE_CLIENT_ID') ?: '');
    define('GOOGLE_CLIENT_SECRET', getenv('GOOGLE_CLIENT_SECRET') ?: '');
}
define('GOOGLE_REDIRECT_URI', SITE_URL . 'backend/handlers/google_callback.php');

// Timezone
date_default_timezone_set('Asia/Kolkata');

// Database connection
try {
    $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4; SET session wait_timeout=28800; SET sql_mode='NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION'",
        PDO::ATTR_TIMEOUT => 5
    ]);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
