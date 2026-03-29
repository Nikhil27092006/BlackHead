<?php
// Fetch current payment settings
$stmt = $pdo->query("SELECT * FROM payment_settings LIMIT 1");
$settings = $stmt->fetch();

// If no settings exist, create a default row
if (!$settings) {
    $pdo->query("INSERT INTO payment_settings (upi_id, account_holder) VALUES ('shop@upi', 'BLACKHEAD STORE')");
    $settings = $pdo->query("SELECT * FROM payment_settings LIMIT 1")->fetch();
}
?>

<div class="admin-card-premium">
    <h3 class="admin-page-title" style="font-size: 24px; margin-bottom: 20px;">Payment Gateway Settings</h3>
    <p style="color: var(--admin-text-muted); margin-bottom: 30px; font-weight: 600;">Manage your primary UPI and Bank details. These configurations directly affect the checkout experience and payment redirection.</p>

    <form action="payment_settings_handler.php" method="POST" enctype="multipart/form-data">
        <div class="grid-2" style="gap: 40px; margin-bottom: 40px;">
            <!-- UPI Settings -->
            <div style="background: var(--admin-bg); padding: 30px; border-radius: 15px;">
                <h4 style="margin-top: 0; margin-bottom: 20px; font-weight: 800; font-size: 16px; color: var(--admin-accent); text-transform: uppercase; letter-spacing: 1px;"><i class="fa-solid fa-mobile-screen-button" style="margin-right: 10px;"></i> Master UPI Details</h4>
                <div class="premium-form-group">
                    <label>UPI ID (VPA)</label>
                    <input type="text" name="upi_id" class="premium-input" value="<?php echo htmlspecialchars($settings['upi_id'] ?? ''); ?>" placeholder="example@oksbi" required>
                    <small style="font-size: 11px; color: var(--admin-text-muted); font-weight: 600;">The fallback ID used for generic UPI redirection.</small>
                </div>
                <div class="premium-form-group" style="margin-top: 20px;">
                    <label>Custom QR Code Image (Optional)</label>
                    <?php if(!empty($settings['qr_code'])): ?>
                        <div style="margin-bottom: 15px; background: white; padding: 15px; border-radius: 12px; border: 1px solid var(--admin-border); display: inline-block;">
                            <img src="../<?php echo htmlspecialchars($settings['qr_code']); ?>" alt="Current QR Code" style="max-width: 140px; display: block; margin: 0 auto;">
                            <div style="text-align: center; margin-top: 8px; font-size: 10px; font-weight: 800; color: var(--admin-accent); text-transform: uppercase;">Current Live QR</div>
                        </div>
                    <?php endif; ?>
                    <input type="file" name="custom_qr" class="premium-input" accept="image/jpeg, image/png, image/webp" style="padding-top: 10px;">
                </div>
                <div class="premium-form-group" style="margin-top: 20px;">
                    <label>Merchant / Account Name</label>
                    <input type="text" name="account_holder" class="premium-input" value="<?php echo htmlspecialchars($settings['account_holder'] ?? ''); ?>" placeholder="Business Name">
                </div>
            </div>

            <!-- Bank Transfer Details -->
            <div style="background: var(--admin-bg); padding: 30px; border-radius: 15px;">
                <h4 style="margin-top: 0; margin-bottom: 20px; font-weight: 800; font-size: 16px; color: var(--admin-accent); text-transform: uppercase; letter-spacing: 1px;"><i class="fa-solid fa-building-columns" style="margin-right: 10px;"></i> Bank Details</h4>
                <div class="premium-form-group">
                    <label>Bank Name</label>
                    <input type="text" name="bank_name" class="premium-input" value="<?php echo htmlspecialchars($settings['bank_name'] ?? ''); ?>" placeholder="State Bank of India">
                </div>
                <div class="premium-form-group">
                    <label>Account Number</label>
                    <input type="text" name="account_number" class="premium-input" value="<?php echo htmlspecialchars($settings['account_number'] ?? ''); ?>" placeholder="XXXXXXXXXXXX">
                </div>
                <div class="premium-form-group">
                    <label>IFSC Code</label>
                    <input type="text" name="ifsc_code" class="premium-input" value="<?php echo htmlspecialchars($settings['ifsc_code'] ?? ''); ?>" placeholder="SBIN000XXXX">
                </div>
            </div>
        </div>

        <h3 style="margin-bottom: 25px; font-size: 18px; font-weight: 900; letter-spacing: -0.5px; border-left: 4px solid var(--admin-accent); padding-left: 15px;"><i class="fa-solid fa-plug" style="margin-right: 10px; color: var(--admin-accent);"></i> Gateway Integrations</h3>
        
        <div class="grid-3" style="gap: 25px; margin-bottom: 40px;">
            <!-- Google Pay -->
            <div style="background: white; border: 1px solid var(--admin-border); padding: 25px; border-radius: 18px; box-shadow: var(--shadow-sm); transition: all 0.3s ease;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h4 style="margin: 0; font-weight: 800; font-size: 15px;"><i class="fa-brands fa-google-pay" style="font-size: 28px; vertical-align: middle; color: #4285F4;"></i> GPay</h4>
                    <label class="switch">
                        <input type="checkbox" name="gpay_active" value="1" <?php echo ($settings['gpay_active'] ?? 0) ? 'checked' : ''; ?>>
                        <span class="slider round"></span>
                    </label>
                </div>
                <div class="premium-form-group">
                    <label>Merchant ID</label>
                    <input type="text" name="gpay_merchant_id" class="premium-input" value="<?php echo htmlspecialchars($settings['gpay_merchant_id'] ?? ''); ?>" placeholder="BCR2DN...">
                </div>
            </div>

            <!-- Paytm -->
            <div style="background: white; border: 1px solid var(--admin-border); padding: 25px; border-radius: 18px; box-shadow: var(--shadow-sm); transition: all 0.3s ease;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h4 style="margin: 0; color: #002e6e; font-weight: 800; font-size: 15px;">Paytm</h4>
                    <label class="switch">
                        <input type="checkbox" name="paytm_active" value="1" <?php echo ($settings['paytm_active'] ?? 0) ? 'checked' : ''; ?>>
                        <span class="slider round"></span>
                    </label>
                </div>
                <div class="premium-form-group">
                    <label>Merchant ID (MID)</label>
                    <input type="text" name="paytm_merchant_id" class="premium-input" value="<?php echo htmlspecialchars($settings['paytm_merchant_id'] ?? ''); ?>" placeholder="DIY1234567890">
                </div>
            </div>

            <!-- PhonePe -->
            <div style="background: white; border: 1px solid var(--admin-border); padding: 25px; border-radius: 18px; box-shadow: var(--shadow-sm); transition: all 0.3s ease;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h4 style="margin: 0; color: #5f259f; font-weight: 800; font-size: 15px;">PhonePe</h4>
                    <label class="switch">
                        <input type="checkbox" name="phonepe_active" value="1" <?php echo ($settings['phonepe_active'] ?? 0) ? 'checked' : ''; ?>>
                        <span class="slider round"></span>
                    </label>
                </div>
                <div class="premium-form-group">
                    <label>Merchant ID</label>
                    <input type="text" name="phonepe_merchant_id" class="premium-input" value="<?php echo htmlspecialchars($settings['phonepe_merchant_id'] ?? ''); ?>" placeholder="M2306160...">
                </div>
            </div>
        </div>

        <div class="premium-form-group">
            <label>Master Payment Instructions (Publicly Visible)</label>
            <textarea name="payment_instructions" class="premium-input" rows="4" placeholder="Enter instructions for bank transfer, COD or other manual methods..."><?php echo htmlspecialchars($settings['payment_instructions'] ?? ''); ?></textarea>
        </div>

        <div style="margin-top: 40px; border-top: 1px solid var(--admin-border); padding-top: 30px; display: flex; justify-content: flex-end;">
            <button type="submit" class="btn btn-primary" style="padding: 14px 40px; border-radius: 12px; font-weight: 800; letter-spacing: 1px;">SAVE GATEWAY CONFIGURATIONS</button>
        </div>
    </form>
</div>

<!-- Recent Payments Section -->
<div class="admin-card-premium" style="margin-top: 40px; padding: 0; overflow: hidden;">
    <div style="padding: 25px 35px; border-bottom: 1px solid var(--admin-border);">
        <h3 style="margin: 0; font-size: 18px; font-weight: 900; letter-spacing: -0.5px;">Recent Transactions</h3>
    </div>
    
    <div class="table-responsive">
        <table class="modern-table">
            <thead>
                <tr>
                    <th>Txn ID</th>
                    <th>Order #</th>
                    <th>Method</th>
                    <th>Gateway</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stmt = $pdo->query("
                    SELECT p.*, o.order_number 
                    FROM payments p 
                    LEFT JOIN orders o ON p.order_id = o.id 
                    ORDER BY p.created_at DESC 
                    LIMIT 10
                ");
                $recent_payments = $stmt->fetchAll();
                
                if (count($recent_payments) > 0) {
                    foreach ($recent_payments as $payment) {
                        $method_label = strtoupper(str_replace('_', ' ', $payment['payment_method']));
                        
                        $status_key = ($payment['status'] == 'success') ? 'paid' : ($payment['status'] == 'failed' ? 'cancelled' : $payment['status']);
                        
                        echo "<tr>";
                        echo "<td><strong style='color: var(--admin-accent);'>{$payment['transaction_id']}</strong></td>";
                        echo "<td><span style='font-weight: 700;'>#{$payment['order_number']}</span></td>";
                        echo "<td style='font-size: 11px; font-weight: 800; color: var(--admin-text-muted);'>{$method_label}</td>";
                        echo "<td>{$payment['gateway']}</td>";
                        echo "<td style='font-weight: 800;'>" . formatPrice($payment['amount']) . "</td>";
                        echo "<td><span class='status-badge-modern status-{$status_key}'>" . ucfirst($payment['status']) . "</span></td>";
                        echo "<td style='font-size: 13px; color: var(--admin-text-muted);'>" . date('M j, Y', strtotime($payment['created_at'])) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7' style='text-align: center; padding: 40px; color: var(--admin-text-muted);'>No transactions recorded yet.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
