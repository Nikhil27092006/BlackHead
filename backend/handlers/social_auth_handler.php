<?php
session_start();
require_once '../core/config.php';
require_once '../core/functions.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$page = $_GET['page'] ?? '';

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

// Simulation: Phone Auth
if ($action == 'phone_login') {
    $phone = clean($_POST['phone']);
    // Simulate sending OTP
    $_SESSION['phone_for_otp'] = $phone;
    $_SESSION['success'] = "SIMULATION: OTP for $phone is 123456"; 
    
    $redirect = isset($_POST['redirect']) ? '&redirect=' . clean($_POST['redirect']) : '';
    redirect_to(SITE_URL . "index.php?page=verify_otp&method=phone" . $redirect);
}

redirect_to(SITE_URL . "index.php");
?>
