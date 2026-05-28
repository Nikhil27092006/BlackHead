<?php
session_start();
require_once '../core/config.php';
require_once '../core/functions.php';

header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject = clean($_POST['subject'] ?? '');
    $message = $_POST['message'] ?? '';

    if (empty($subject) || empty($message)) {
        echo json_encode(['success' => false, 'message' => 'Subject and Message are required.']);
        exit;
    }

    // Fetch all active subscribers
    $stmt = $pdo->query("SELECT email FROM subscribers WHERE status = 'active'");
    $subscribers = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (empty($subscribers)) {
        echo json_encode(['success' => false, 'message' => 'No active subscribers found.']);
        exit;
    }

    $successCount = 0;
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= 'From: BLACKHEAD <' . getSetting('support_email', 'noreply@blackhead.in') . '>' . "\r\n";

    // Format the email message with a simple premium template
    $emailBody = "
    <html>
    <body style='font-family: sans-serif; background: #f9f9f9; padding: 40px;'>
        <div style='max-width: 600px; margin: 0 auto; background: #fff; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.05);'>
            <div style='background: #000; padding: 40px; text-align: center;'>
                <h1 style='color: #fff; margin:0; letter-spacing: 5px;'>BLACKHEAD</h1>
                <p style='color: #8b5cf6; margin-top: 10px; font-weight: 800; font-size: 12px; text-transform: uppercase;'>The Inner Circle</p>
            </div>
            <div style='padding: 50px; line-height: 1.8; color: #333;'>
                $message
            </div>
            <div style='background: #fcfcfc; padding: 40px; text-align: center; border-top: 1px solid #eee;'>
                <p style='font-size: 12px; color: #999;'>Defining the future of premium apparel.</p>
                <p style='font-size: 10px; color: #ccc; margin-top: 20px;'>You are receiving this as a member of the BLACKHEAD Inner Circle. To unsubscribe, please contact support.</p>
            </div>
        </div>
    </body>
    </html>";

    foreach ($subscribers as $email) {
        if (mail($email, $subject, $emailBody, $headers)) {
            $successCount++;
        }
    }

    echo json_encode([
        'success' => true, 
        'message' => "Broadcast successfully delivered to $successCount members.",
        'count' => $successCount
    ]);
}
?>
