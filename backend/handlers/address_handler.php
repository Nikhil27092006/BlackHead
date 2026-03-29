<?php
session_start();
require_once '../core/config.php';
require_once '../core/functions.php';

if (!isLoggedIn()) {
    redirect_to(SITE_URL . "index.php?page=login");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $userId = $_SESSION['user_id'];

    if ($action == 'add_address') {
        $name = clean($_POST['name']);
        $phone = clean($_POST['phone']);
        $address = clean($_POST['address']);
        $city = clean($_POST['city']);
        $state = clean($_POST['state']);
        $pincode = clean($_POST['pincode']);
        $is_default = isset($_POST['is_default']) ? 1 : 0;

        try {
            if ($is_default) {
                $pdo->prepare("UPDATE user_addresses SET is_default = 0 WHERE user_id = ?")->execute([$userId]);
            }

            $stmt = $pdo->prepare("INSERT INTO user_addresses (user_id, name, phone, address, city, state, pincode, is_default, type, country) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'home', 'India')");
            $stmt->execute([$userId, $name, $phone, $address, $city, $state, $pincode, $is_default]);

            $_SESSION['success'] = "Address added successfully.";
            $redirect = $_POST['redirect'] ?? 'address_book';
            header("Location: " . SITE_URL . "index.php?page=" . $redirect);
            exit;
        } catch (PDOException $e) {
            $_SESSION['error'] = "Failed to save address: " . $e->getMessage();
            $redirect = $_POST['redirect'] ?? 'address_book';
            redirect_to(SITE_URL . "index.php?page=" . $redirect);
        }
    }

    if ($action == 'delete_address') {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM user_addresses WHERE id = ? AND user_id = ?");
        $stmt->execute([$id, $userId]);
        
        $_SESSION['success'] = "Address deleted.";
        $redirect = $_POST['redirect'] ?? 'address_book';
        session_write_close();
        redirect_to(SITE_URL . "index.php?page=" . $redirect);
    }
}
redirect_to(SITE_URL . "index.php?page=account");
