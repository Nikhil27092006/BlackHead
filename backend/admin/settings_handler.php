<?php
session_start();
require_once '../../backend/core/config.php';
require_once '../../backend/core/functions.php';

// Check if user is logged in as admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php?page=login");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect all POST variables
    $store_name = clean($_POST['store_name'] ?? '');
    $support_email = clean($_POST['support_email'] ?? '');
    $contact_phone = clean($_POST['contact_phone'] ?? '');
    $store_address = clean($_POST['store_address'] ?? '');
    $instagram = clean($_POST['instagram'] ?? '');
    $facebook = clean($_POST['facebook'] ?? '');
    $twitter = clean($_POST['twitter'] ?? '');
    
    try {
        $pdo->beginTransaction();

        $settings_to_update = [
            'store_name' => $store_name,
            'support_email' => $support_email,
            'contact_phone' => $contact_phone,
            'store_address' => $store_address,
            'instagram' => $instagram,
            'facebook' => $facebook,
            'twitter' => $twitter
        ];

        $stmt = $pdo->prepare("INSERT INTO settings (key_name, key_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE key_value = ?");
        
        foreach ($settings_to_update as $key => $value) {
            $stmt->execute([$key, $value, $value]);
        }

        $pdo->commit();
        $_SESSION['success'] = "Store settings updated successfully.";
    } catch (PDOException $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Error updating settings: " . $e->getMessage();
    }

    header("Location: index.php?page=settings");
    exit;
} else {
    header("Location: index.php?page=settings");
    exit;
}
?>
