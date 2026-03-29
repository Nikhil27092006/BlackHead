<?php
session_start();
require_once '../core/config.php';
require_once '../core/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = clean($_POST['name']);
    $email = clean($_POST['email']);
    $subject = clean($_POST['subject']);
    $message = clean($_POST['message']);
    
    // In a real app, you'd send an email here.
    // For now, we'll just simulate success.
    
    $_SESSION['success'] = "Thank you, $name. Your message has been sent!";
    header("Location: " . SITE_URL . "index.php?page=contact");
    exit;
} else {
    header("Location: " . SITE_URL . "index.php");
    exit;
}
?>
