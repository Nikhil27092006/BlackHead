<?php
// security_handler.php
session_start();
require_once '../../backend/core/config.php';
require_once '../../backend/core/functions.php';

if (!isset($_SESSION['admin_id'])) {
    exit('Unauthorized');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $admin_id = $_SESSION['admin_id'];

    // CSRF validation
    if (!isset($_POST['csrf_token']) || !validate_csrf_token($_POST['csrf_token'])) {
        $_SESSION['error'] = "Invalid request. Please try again.";
        header("Location: index.php?page=security_settings");
        exit;
    }

    // Fetch current admin data
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE id = ?");
    $stmt->execute([$admin_id]);
    $admin = $stmt->fetch();

    if ($action == 'update_profile') {
        $name = clean($_POST['name']);
        $email = clean($_POST['email']);
        $current_password = $_POST['current_password'];

        // Verify current password
        if (!password_verify($current_password, $admin['password'])) {
            $_SESSION['error'] = "Current password incorrect. Profile update failed.";
            header("Location: index.php?page=security_settings");
            exit;
        }

        // Check if email is already taken by another admin
        $check = $pdo->prepare("SELECT id FROM admins WHERE email = ? AND id != ?");
        $check->execute([$email, $admin_id]);
        if ($check->rowCount() > 0) {
            $_SESSION['error'] = "This email is already in use by another administrator.";
            header("Location: index.php?page=security_settings");
            exit;
        }

        $update = $pdo->prepare("UPDATE admins SET name = ?, email = ? WHERE id = ?");
        try {
            $update->execute([$name, $email, $admin_id]);
            $_SESSION['admin_name'] = $name; // Update session name
            $_SESSION['success'] = "Administrator profile updated successfully!";
        } catch (Exception $e) {
            $_SESSION['error'] = "Update failed: " . $e->getMessage();
        }
    }

    if ($action == 'update_password') {
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        $current_password = $_POST['current_password'];

        // Verify current password
        if (!password_verify($current_password, $admin['password'])) {
            $_SESSION['error'] = "Current password incorrect. Password change failed.";
            header("Location: index.php?page=security_settings");
            exit;
        }

        if ($new_password !== $confirm_password) {
            $_SESSION['error'] = "New passwords do not match.";
            header("Location: index.php?page=security_settings");
            exit;
        }

        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update = $pdo->prepare("UPDATE admins SET password = ? WHERE id = ?");
        try {
            $update->execute([$hashed_password, $admin_id]);
            $_SESSION['success'] = "Administrator password updated successfully!";
        } catch (Exception $e) {
            $_SESSION['error'] = "Update failed: " . $e->getMessage();
        }
    }
}

header("Location: index.php?page=security_settings");
exit;
?>
