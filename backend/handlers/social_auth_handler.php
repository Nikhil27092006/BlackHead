<?php
session_start();
require_once __DIR__ . '/../core/config.php';
require_once __DIR__ . '/../core/functions.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$page = $_GET['page'] ?? '';

// CSRF validation for POST actions
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_GET['method'])) {
    if (!isset($_POST['csrf_token']) || !validate_csrf_token($_POST['csrf_token'])) {
        $_SESSION['error'] = "Invalid request. Please try again.";
        header("Location: " . SITE_URL . "index.php?page=home");
        exit;
    }
}

// Real Google OAuth Redirection
if (isset($_GET['method']) && $_GET['method'] == 'google') {
    if (GOOGLE_CLIENT_ID == 'YOUR_GOOGLE_CLIENT_ID_HERE') {
        die("Please configure your GOOGLE_CLIENT_ID in backend/core/config.php");
    }

    $params = [
        'response_type' => 'code',
        'client_id'     => GOOGLE_CLIENT_ID,
        'redirect_uri'  => GOOGLE_REDIRECT_URI,
        'scope'         => 'openid email profile',
        'state'         => bin2hex(random_bytes(16)),
        'access_type'   => 'offline',
        'prompt'        => 'select_account'
    ];

    $_SESSION['oauth_state'] = $params['state'];
    $authUrl = "https://accounts.google.com/o/oauth2/v2/auth?" . http_build_query($params);
    
    header("Location: " . $authUrl);
    exit;
}

// Real Phone Auth - No OTP as requested
if ($action == 'phone_login') {
    $phone = clean($_POST['phone']);
    $name  = clean($_POST['name']);
    
    // Check if user exists by phone
    $stmt = $pdo->prepare("SELECT * FROM users WHERE phone = ?");
    $stmt->execute([$phone]);
    $user = $stmt->fetch();

    if (!$user) {
        // Create new user
        $stmt = $pdo->prepare("INSERT INTO users (name, phone, status) VALUES (?, ?, 'active')");
        $stmt->execute([$name, $phone]);
        $user_id = $pdo->lastInsertId();
    } else {
        $user_id = $user['id'];
        $name = $user['name']; // Keep existing name
    }

    // Set session
    $_SESSION['user_id'] = $user_id;
    $_SESSION['user_name'] = $name;
    
    // Migrate cart
    migrateCart($pdo, session_id(), $user_id);
    
    $_SESSION['success'] = "Logged in successfully!";
    
    $redirect_url = (isset($_POST['redirect']) && !empty($_POST['redirect'])) ? clean($_POST['redirect']) : SITE_URL . "index.php?page=account";
    redirect_to($redirect_url);
}

redirect_to(SITE_URL . "index.php");
?>
