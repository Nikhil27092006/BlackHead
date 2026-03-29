<?php
session_start();
require_once 'backend/core/config.php';
require_once 'backend/core/functions.php';

// Simple Router
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

// Handle common actions before routing
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    // Handle logout
    if ($action == 'logout') {
        session_unset();
        session_destroy();
        header("Location: index.php");
        exit;
    }
}

// Map page names to file paths
$pages = [
    'home'             => 'frontend/pages/home.php',
    'products'         => 'frontend/pages/products.php',
    'product'          => 'frontend/pages/product_detail.php',
    'cart'             => 'frontend/pages/cart.php',
    'checkout'         => 'frontend/pages/checkout.php',
    'order_success'    => 'frontend/pages/order_success.php',
    'login'            => 'frontend/pages/login.php',
    'register'         => 'frontend/pages/register.php',
    'account'          => 'frontend/pages/account.php',
    'wishlist'         => 'frontend/pages/wishlist.php',
    'order_history'    => 'frontend/pages/order_history.php',
    'order_detail'     => 'frontend/pages/order_detail.php',
    'contact'          => 'frontend/pages/contact.php',
    'about'            => 'frontend/pages/about.php',
    'privacy'          => 'frontend/pages/privacy.php',
    'terms'            => 'frontend/pages/terms.php',
    'shipping'         => 'frontend/pages/shipping.php',
    'size-guide'       => 'frontend/pages/size_guide.php',
    'faqs'             => 'frontend/pages/faqs.php',
    'careers'          => 'frontend/pages/careers.php',
    'profile'          => 'frontend/pages/profile.php',
    'address_book'     => 'frontend/pages/address_book.php',
    'payment'          => 'frontend/pages/payment.php',
    'phone_login'      => 'frontend/pages/phone_login.php',
    'verify_otp'       => 'frontend/pages/verify_otp.php',
    'social_auth'      => 'backend/handlers/social_auth_handler.php',
];

// Check if page exists
if (array_key_exists($page, $pages)) {
    $page_file = $pages[$page];
} else {
    $page_file = 'frontend/pages/404.php';
}

// Start Output Buffering for layout support
ob_start();
include $page_file;
$content = ob_get_clean();

// Basic Layout wrapper
include 'frontend/components/header.php';
echo $content;
include 'frontend/components/footer.php';
?>
