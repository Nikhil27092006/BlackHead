<?php
session_start();
require_once 'config.php';
require_once 'functions.php';

if (!isLoggedIn()) {
    header("Location: ../index.php?page=login");
    exit;
}

$orderId = $_POST['order_id'] ?? null;
if (!$orderId) exit("Invalid Request");

// In a real app, we'd verify the card/upi here
// For this simulation, we simulate a "Redirect to Bank"
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bank Authentication</title>
    <style>
        body { font-family: 'Inter', sans-serif; background: #f4f7f6; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
        .bank-card { background: white; padding: 40px; border-radius: 8px; box-shadow: 0 15px 35px rgba(0,0,0,0.1); width: 100%; max-width: 400px; text-align: center; }
        .logo { font-size: 24px; font-weight: 900; margin-bottom: 30px; color: #333; }
        .logo span { color: #007bff; }
        .otp-input { width: 100%; padding: 15px; font-size: 24px; text-align: center; letter-spacing: 10px; border: 1px solid #ddd; margin: 20px 0; border-radius: 4px; }
        .btn { background: #333; color: white; border: none; padding: 15px 30px; width: 100%; font-weight: 700; cursor: pointer; border-radius: 4px; }
        .timer { font-size: 12px; color: #777; margin-top: 15px; }
    </style>
</head>
<body>
    <div class="bank-card">
        <div class="logo">SECURE<span>BANK</span></div>
        <p style="font-size: 14px; color: #555;">An OTP has been sent to your registered mobile number for the transaction of <strong><?php echo $_SESSION['last_total'] ?? '₹...'; ?></strong></p>
        
        <form action="../backend/handlers/payment_handler.php" method="POST">
            <input type="hidden" name="order_id" value="<?php echo $orderId; ?>">
            <input type="text" name="otp" class="otp-input" placeholder="000000" maxlength="6" required>
            <button type="submit" class="btn">AUTHENTICATE</button>
        </form>
        
        <div class="timer">Resend OTP in <span id="secs">60</span>s</div>
    </div>

    <script>
        let s = 60;
        setInterval(() => { if(s > 0) document.getElementById('secs').innerText = --s; }, 1000);
    </script>
</body>
</html>
