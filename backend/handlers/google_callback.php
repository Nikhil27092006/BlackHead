<?php
session_start();
require_once '../core/config.php';
require_once '../core/functions.php';

if (isset($_GET['code'])) {
    // Verify state to prevent CSRF
    if (!isset($_GET['state']) || $_GET['state'] !== $_SESSION['oauth_state']) {
        $_SESSION['error'] = "Invalid OAuth state. Please try again.";
        redirect_to(SITE_URL . "index.php?page=login");
    }
    unset($_SESSION['oauth_state']);

    $code = $_GET['code'];

    // Exchange code for access token
    $token_url = "https://oauth2.googleapis.com/token";
    $post_data = [
        'code'          => $code,
        'client_id'     => GOOGLE_CLIENT_ID,
        'client_secret' => GOOGLE_CLIENT_SECRET,
        'redirect_uri'  => GOOGLE_REDIRECT_URI,
        'grant_type'    => 'authorization_code'
    ];

    $ch = curl_init($token_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
    $response = curl_exec($ch);
    $data = json_decode($response, true);
    curl_close($ch);

    if (isset($data['access_token'])) {
        $access_token = $data['access_token'];

        // Get user info
        $user_info_url = "https://www.googleapis.com/oauth2/v2/userinfo?access_token=" . $access_token;
        $user_info_json = file_get_contents($user_info_url);
        $user_info = json_decode($user_info_json, true);

        if (isset($user_info['email'])) {
            $email = $user_info['email'];
            $name = $user_info['name'] ?? 'Google User';
            $google_id = $user_info['id'];

            // Check if user exists
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if (!$user) {
                // Register new user
                $stmt = $pdo->prepare("INSERT INTO users (name, email, password, status) VALUES (?, ?, ?, 'active')");
                $stmt->execute([$name, $email, password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT)]);
                $user_id = $pdo->lastInsertId();
            } else {
                $user_id = $user['id'];
                $name = $user['name'];
            }

            // Set session
            $_SESSION['user_id'] = $user_id;
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            
            // Migrate cart
            migrateCart($pdo, session_id(), $user_id);
            
            $_SESSION['success'] = "Successfully logged in with Google!";
            
            session_write_close();
            redirect_to(SITE_URL . "index.php?page=account");
        }
    }

    $_SESSION['error'] = "Failed to authenticate with Google. " . ($data['error_description'] ?? '');
    redirect_to(SITE_URL . "index.php?page=login");
} else if (isset($_GET['error'])) {
    $_SESSION['error'] = "Google Login Error: " . $_GET['error'];
    redirect_to(SITE_URL . "index.php?page=login");
} else {
    redirect_to(SITE_URL . "index.php");
}
