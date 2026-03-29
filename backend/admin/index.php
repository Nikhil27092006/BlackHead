<?php
session_start();
require_once '../../backend/core/config.php';
require_once '../../backend/core/functions.php';

// Simple Admin Router
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

// Check Admin Logged In
if ($page != 'login' && !isset($_SESSION['admin_id'])) {
    header("Location: index.php?page=login");
    exit;
}

// Map Admin Pages
$pages = [
    'login'      => 'login.php',
    'dashboard'  => 'dashboard_content.php',
    'products'    => 'products.php',
    'add_product' => 'add_product.php',
    'edit_product' => 'edit_product.php',
    'categories'  => 'categories.php',
    'orders'     => 'orders.php',
    'order_detail' => 'order_detail.php',
    'users'      => 'users.php',
    'settings'   => 'settings.php',
    'home_highlights' => 'home_highlights.php',
    'payment_settings' => 'payment_settings.php',
];

if (array_key_exists($page, $pages)) {
    $page_file = $pages[$page];
} else {
    $page_file = 'dashboard_content.php';
}

// Side-bar and Header for Admin Panel
if ($page != 'login') {
    include 'includes/header.php';
    include $page_file;
    include 'includes/footer.php';
} else {
    include 'login.php';
}
?>
