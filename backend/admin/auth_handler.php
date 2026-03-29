<?php
session_start();
require_once '../../backend/core/config.php';
require_once '../../backend/core/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = clean($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = ? AND status = 'active'");
    $stmt->execute([$email]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_name'] = $admin['name'];
        $_SESSION['admin_role'] = $admin['role'];

        header("Location: index.php?page=dashboard");
        exit;
    } else {
        $_SESSION['error'] = "Invalid admin credentials.";
        header("Location: index.php?page=login");
        exit;
    }
} else {
    header("Location: index.php");
    exit;
}
?>
