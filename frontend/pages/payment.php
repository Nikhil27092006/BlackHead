<?php
if (!isLoggedIn()) {
    header("Location: index.php?page=login");
    exit;
}

$orderId = $_GET['order_id'] ?? null;
if (!$orderId) {
    header("Location: index.php?page=account");
    exit;
}

// Fetch order details
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ? AND payment_status = 'pending'");
$stmt->execute([$orderId, $_SESSION['user_id']]);
$order = $stmt->fetch();

if (!$order) {
    header("Location: index.php?page=order_history");
    exit;
}

$method = $order['payment_method'];

// Fetch payment settings for dynamic UPI redirection
$p_settings = $pdo->query("SELECT upi_id, account_holder FROM payment_settings LIMIT 1")->fetch();
$upi_id = $p_settings['upi_id'] ?? 'shop@upi';
$merchant_name = $p_settings['account_holder'] ?? 'BLACKHEAD';
$merchant_name_encoded = urlencode($merchant_name);
?>

<?php
$amount = number_format($order['final_amount'], 2, '.', '');
// Use %20 for spaces as it's more compatible with UPI apps than '+'
$merchant_name_fixed = str_replace('+', '%20', urlencode($merchant_name));
$common_params = "pa=$upi_id&pn=$merchant_name_fixed&am=$amount&cu=INR&tn=Order_" . $order['order_number'];

// Universal UPI URI
$upi_uri = "upi://pay?" . $common_params;
// QR Code URI for desktop users
$qr_uri = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($upi_uri);
?>

<div class="container" style="padding: 60px 0; min-height: 70vh; display: flex; align-items: center; justify-content: center;">
    <div style="max-width: 500px; width: 100%; border: 1px solid var(--border-color); padding: 40px; background: #fff; box-shadow: 0 10px 30px rgba(0,0,0,0.05);">
        
        <div style="text-align: center; margin-bottom: 30px;">
            <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 2px; color: var(--light-text); margin-bottom: 10px;">Safe & Secure Payment</div>
            <h2 style="font-weight: 900; text-transform: uppercase; font-size: 28px;"><?php echo formatPrice($order['final_amount']); ?></h2>
            <p style="font-size: 14px; color: var(--light-text);">Order ID: #<?php echo $order['order_number']; ?></p>
        </div>

        <form action="backend/handlers/payment_handler.php" method="POST" id="payment-form">
            <input type="hidden" name="order_id" value="<?php echo $orderId; ?>">
            
            <?php if (in_array($method, ['upi', 'googlepay', 'paytm', 'phonepe', 'card'])): ?>
                <div style="text-align: center;">
                    
                    <?php if ($method == 'card'): ?>
                        <!-- Credit / Debit Card Form -->
                        <div style="margin-bottom: 30px; padding: 30px; background: #fff; border-radius: 12px; border: 1px solid #ddd; text-align: left; box-shadow: 0 4px 15px rgba(0,0,0,0.02);">
                            <h3 style="margin-bottom: 20px; font-weight: 800; display: flex; align-items: center; gap: 10px;">
                                <i class="fa-regular fa-credit-card"></i> Pay by Card
                            </h3>
                            
                            <div class="form-group mb-3">
                                <label style="font-size: 12px; font-weight: 700; text-transform: uppercase; display: block; margin-bottom: 8px;">Card Number</label>
                                <div style="position: relative;">
                                    <input type="text" placeholder="XXXX XXXX XXXX XXXX" class="form-control" style="padding-left: 40px;" maxlength="19" required>
                                    <i class="fa-brands fa-cc-visa" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #bbb; font-size: 18px;"></i>
                                </div>
                            </div>
                            
                            <div class="grid-2">
                                <div class="form-group">
                                    <label style="font-size: 12px; font-weight: 700; text-transform: uppercase; display: block; margin-bottom: 8px;">Expiry Date</label>
                                    <input type="text" placeholder="MM/YY" class="form-control" maxlength="5" required>
                                </div>
                                <div class="form-group">
                                    <label style="font-size: 12px; font-weight: 700; text-transform: uppercase; display: block; margin-bottom: 8px;">CVV</label>
                                    <input type="password" placeholder="***" class="form-control" maxlength="4" required>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label style="font-size: 12px; font-weight: 700; text-transform: uppercase; display: block; margin-bottom: 8px;">Name on Card</label>
                                <input type="text" placeholder="John Doe" class="form-control" required>
                            </div>
                            
                            <p style="font-size: 11px; color: #888; text-align: center; margin-top: 15px;">
                                <i class="fa-solid fa-lock" style="color: #27ae60;"></i> Your card details are securely encrypted.
                            </p>
                        </div>
                    <?php elseif ($method == 'upi'): ?>
                        <!-- QR Code for Desktop (Generic UPI only) -->
                        <div class="desktop-only" style="margin-bottom: 30px; padding: 20px; background: #f9f9f9; border-radius: 12px; border: 1px dashed #ddd;">
                            <p style="font-size: 13px; font-weight: 600; margin-bottom: 15px;">Scan QR with any UPI App</p>
                            <?php if(!empty($p_settings['qr_code'])): ?>
                                <img src="<?php echo htmlspecialchars($p_settings['qr_code']); ?>" alt="Scan to Pay" style="width: 180px; height: 180px; display: block; margin: 0 auto; border: 5px solid white; object-fit: contain; background: white;">
                            <?php else: ?>
                                <img src="<?php echo $qr_uri; ?>" alt="Scan to Pay" style="width: 180px; height: 180px; display: block; margin: 0 auto; border: 5px solid white; background: white;">
                            <?php endif; ?>
                            <p style="font-size: 11px; color: #777; margin-top: 15px;">Works with GPay, PhonePe, Paytm & BHIM</p>
                        </div>
                        
                        <div style="margin-bottom: 25px;">
                            <p style="font-size: 14px; color: var(--light-text); margin-bottom: 15px; font-weight: 600;">Or Pay via UPI Apps directly</p>
                            
                            <!-- Universal App Links -->
                            <div class="grid-3">
                                <a href="<?php echo $upi_uri; ?>" class="upi-box">
                                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/c7/Google_Pay_Logo_%282020%29.svg/512px-Google_Pay_Logo_%282020%29.svg.png" alt="GPay">
                                    <span>GPay</span>
                                </a>
                                <a href="<?php echo $upi_uri; ?>" class="upi-box">
                                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/7/71/PhonePe_Logo.svg/512px-PhonePe_Logo.svg.png" alt="PhonePe">
                                    <span>PhonePe</span>
                                </a>
                                <a href="<?php echo $upi_uri; ?>" class="upi-box">
                                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/24/Paytm_Logo_%28standalone%29.svg/512px-Paytm_Logo_%28standalone%29.svg.png" alt="Paytm">
                                    <span>Paytm</span>
                                </a>
                            </div>

                            <div style="margin-top: 20px; padding-top: 15px; border-top: 1px solid #eee;">
                                <p style="font-size: 11px; color: #999; margin-bottom: 5px;">UPI ID: <strong id="upi-id" style="color: #333;"><?php echo $upi_id; ?></strong></p>
                                <button type="button" onclick="copyUpiId()" style="background: var(--primary-color); color: white; border: none; padding: 5px 12px; border-radius: 4px; font-size: 11px; cursor: pointer; font-weight: 600;">COPY UPI ID</button>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Specific App Redirect (Gpay, Paytm, PhonePe) -->
                        <div style="margin-bottom: 30px; padding: 30px 20px; background: #f9f9f9; border-radius: 12px; border: 1px solid #eee;">
                            <?php 
                                $gateway_name = '';
                                $gateway_logo = '';
                                $app_uri = $upi_uri; // Default to generic
                                
                                if ($method == 'googlepay') {
                                    $gateway_name = 'Google Pay';
                                    $gateway_logo = '<i class="fa-brands fa-google-pay" style="font-size: 48px; color: #5f6368; display: block; margin-bottom: 15px;"></i>';
                                    $app_uri = "tez://upi/pay?" . $common_params;
                                } elseif ($method == 'paytm') {
                                    $gateway_name = 'Paytm';
                                    $gateway_logo = '<img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/24/Paytm_Logo_%28standalone%29.svg/512px-Paytm_Logo_%28standalone%29.svg.png" alt="Paytm" style="height: 30px; margin-bottom: 15px;">';
                                    $app_uri = "paytmmp://pay?" . $common_params;
                                } elseif ($method == 'phonepe') {
                                    $gateway_name = 'PhonePe';
                                    $gateway_logo = '<img src="https://upload.wikimedia.org/wikipedia/commons/thumb/7/71/PhonePe_Logo.svg/512px-PhonePe_Logo.svg.png" alt="PhonePe" style="height: 30px; margin-bottom: 15px;">';
                                    $app_uri = "phonepe://pay?" . $common_params;
                                }
                            ?>
                            <?php echo $gateway_logo; ?>
                            <p style="font-size: 16px; font-weight: 700; margin-bottom: 15px;">Pay via <?php echo $gateway_name; ?></p>
                            <p style="font-size: 12px; color: #666; margin-bottom: 20px;">You are being redirected to <?php echo $gateway_name; ?>'s secure payment gateway for <strong><?php echo formatPrice($order['final_amount']); ?></strong>.<br>Please do not refresh the page.</p>
                            
                            <!-- Direct App Linking -->
                            <a href="<?php echo $app_uri; ?>" class="btn btn-outline" style="display: inline-flex; align-items: center; justify-content: center; gap: 10px; padding: 12px 25px; text-decoration: none; border-radius: 8px;">
                                <i class="fa-solid fa-lock"></i> OPEN <?php echo strtoupper($gateway_name); ?> APP
                            </a>

                            <div style="margin-top: 15px; font-size: 12px; color: var(--light-text);">
                                <i class="fa-solid fa-circle-notch fa-spin"></i> Redirecting automatically...
                            </div>
                            
                            <script>
                                // Automatically redirect the user to the app-specific URI
                                setTimeout(function() {
                                    window.location.href = "<?php echo $app_uri; ?>";
                                }, 1500);
                            </script>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($method != 'card'): ?>
                    <div style="background: <?php echo ($method == 'upi') ? '#fff9e6' : '#f0f9ff'; ?>; border-left: 4px solid <?php echo ($method == 'upi') ? '#ffcc00' : '#0ea5e9'; ?>; padding: 15px; text-align: left; margin-bottom: 30px; border-radius: 0 8px 8px 0;">
                        <p style="font-size: 12px; color: <?php echo ($method == 'upi') ? '#856404' : '#0369a1'; ?>; line-height: 1.5; margin: 0;">
                            <strong>Instructions:</strong><br>
                            1. Complete the payment of <strong><?php echo formatPrice($order['final_amount']); ?></strong> using your preferred app.<br>
                            2. <strong>Crucial:</strong> Come back here and click the button below to finalize your order.
                        </p>
                    </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>


            <button type="submit" id="confirm-btn" class="btn btn-primary" style="width: 100%; padding: 18px; font-weight: 900; letter-spacing: 1px; border-radius: 8px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                I HAVE COMPLETED THE PAYMENT
            </button>
            <div id="loader" style="display: none; text-align: center; margin-top: 20px;">
                <i class="fa-solid fa-circle-notch fa-spin" style="font-size: 24px; color: var(--primary-color);"></i>
                <p style="font-size: 12px; margin-top: 10px; color: var(--light-text);">Finalizing your order...</p>
            </div>

            <p style="text-align: center; font-size: 10px; color: #999; margin-top: 25px; text-transform: uppercase; letter-spacing: 1px; font-weight: 600;">
                <i class="fa-solid fa-shield-halved" style="color: #10b981;"></i> Secured by BLACKHEAD Payment Gateway
            </p>
        </form>
    </div>
</div>

<style>
.upi-box {
    text-decoration: none;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 15px 10px;
    border: 1px solid #eee;
    border-radius: 12px;
    transition: all 0.2s;
}
.upi-box:hover {
    border-color: var(--primary-color);
    background: #fcfcfc;
}
.upi-box img {
    height: 24px;
    margin-bottom: 8px;
}
.upi-box span {
    font-size: 11px;
    color: #555;
    font-weight: 700;
}
@media (max-width: 768px) {
    .desktop-only { display: none; }
}
@media (min-width: 769px) {
    .upi-box { pointer-events: none; opacity: 0.6; grayscale(1); }
}
</style>

<script>
function copyUpiId() {
    const upiId = document.getElementById('upi-id').innerText;
    navigator.clipboard.writeText(upiId).then(() => {
        alert("UPI ID copied! You can now paste it in your payment app.");
    });
}

document.getElementById('payment-form').addEventListener('submit', function(e) {
    document.getElementById('confirm-btn').style.display = 'none';
    document.getElementById('loader').style.display = 'block';
});
</script>
