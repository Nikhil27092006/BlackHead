<?php
session_start();
require_once '../../backend/core/config.php';
require_once '../../backend/core/functions.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $upi_id = clean($_POST['upi_id']);
    $account_holder = clean($_POST['account_holder']);
    $bank_name = clean($_POST['bank_name']);
    $account_number = clean($_POST['account_number']);
    $ifsc_code = clean($_POST['ifsc_code']);
    $payment_instructions = clean($_POST['payment_instructions']);

    // Google Pay
    $gpay_api_key = clean($_POST['gpay_api_key'] ?? '');
    $gpay_merchant_id = clean($_POST['gpay_merchant_id'] ?? '');
    $gpay_active = isset($_POST['gpay_active']) ? 1 : 0;

    // Paytm
    $paytm_merchant_id = clean($_POST['paytm_merchant_id'] ?? '');
    $paytm_merchant_key = clean($_POST['paytm_merchant_key'] ?? '');
    $paytm_active = isset($_POST['paytm_active']) ? 1 : 0;

    // PhonePe
    $phonepe_merchant_id = clean($_POST['phonepe_merchant_id'] ?? '');
    $phonepe_salt_key = clean($_POST['phonepe_salt_key'] ?? '');
    $phonepe_salt_index = clean($_POST['phonepe_salt_index'] ?? '');
    $phonepe_active = isset($_POST['phonepe_active']) ? 1 : 0;

    // Stripe
    $stripe_public_key = clean($_POST['stripe_public_key'] ?? '');
    $stripe_secret_key = clean($_POST['stripe_secret_key'] ?? '');
    $stripe_active = isset($_POST['stripe_active']) ? 1 : 0;

    // Get current settings to preserve existing QR code if no new one is uploaded
    $stmt = $pdo->query("SELECT qr_code FROM payment_settings LIMIT 1");
    $currentSettings = $stmt->fetch();
    $qr_code_path = $currentSettings['qr_code'] ?? null;

    // Handle Custom QR Upload
    if (isset($_FILES['custom_qr']) && $_FILES['custom_qr']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $filename = $_FILES['custom_qr']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            // Ensure uploads directory exists
            $uploadDir = '../../uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $newFileName = 'qr_' . time() . '.' . $ext;
            $destination = $uploadDir . $newFileName;
            
            if (move_uploaded_file($_FILES['custom_qr']['tmp_name'], $destination)) {
                $qr_code_path = 'uploads/' . $newFileName;
            } else {
                $_SESSION['error'] = "Failed to move uploaded file.";
                header("Location: index.php?page=payment_settings");
                exit;
            }
        } else {
            $_SESSION['error'] = "Invalid image format. Only JPG, PNG, and WEBP are allowed.";
            header("Location: index.php?page=payment_settings");
            exit;
        }
    }

    try {
        // Since we only have one row, we update based on the first ID or just update all if it's singleton
        $stmt = $pdo->prepare("UPDATE payment_settings SET 
            upi_id = ?, 
            account_holder = ?, 
            bank_name = ?, 
            account_number = ?, 
            ifsc_code = ?, 
            qr_code = ?,
            payment_instructions = ?,
            gpay_api_key = ?,
            gpay_merchant_id = ?,
            gpay_active = ?,
            paytm_merchant_id = ?,
            paytm_merchant_key = ?,
            paytm_active = ?,
            phonepe_merchant_id = ?,
            phonepe_salt_key = ?,
            phonepe_salt_index = ?,
            phonepe_active = ?,
            stripe_public_key = ?,
            stripe_secret_key = ?,
            stripe_active = ?
            LIMIT 1");
        
        $stmt->execute([
            $upi_id,
            $account_holder,
            $bank_name,
            $account_number,
            $ifsc_code,
            $qr_code_path,
            $payment_instructions,
            $gpay_api_key,
            $gpay_merchant_id,
            $gpay_active,
            $paytm_merchant_id,
            $paytm_merchant_key,
            $paytm_active,
            $phonepe_merchant_id,
            $phonepe_salt_key,
            $phonepe_salt_index,
            $phonepe_active,
            $stripe_public_key,
            $stripe_secret_key,
            $stripe_active
        ]);

        $_SESSION['success'] = "Payment settings updated successfully!";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error updating settings: " . $e->getMessage();
    }

    header("Location: index.php?page=payment_settings");
    exit;
} else {
    header("Location: index.php?page=dashboard");
    exit;
}
?>
