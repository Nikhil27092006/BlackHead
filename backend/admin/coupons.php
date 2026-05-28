<?php
$coupons = $pdo->query("SELECT * FROM coupons ORDER BY created_at DESC")->fetchAll();
?>

<div class="admin-top-header">
    <div>
        <h1 class="admin-page-title">Discount Coupons</h1>
        <p class="admin-page-subtitle">Manage promotional codes and discounts</p>
    </div>
</div>

<div class="stats-grid">
    <div class="admin-card-premium" style="padding: 30px; grid-column: span 1;">
        <h4 style="margin: 0 0 20px 0; font-family: 'Syne', sans-serif; font-size: 14px; color: var(--admin-accent); text-transform: uppercase; letter-spacing: 1px;">Add New Coupon</h4>
        <form action="coupon_handler.php" method="POST" style="border:none; padding:0; background:transparent;">
            <input type="hidden" name="action" value="add">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            <div class="premium-form-group">
                <label>COUPON CODE</label>
                <input type="text" name="code" class="premium-input" placeholder="e.g. BLACKHEAD10" required style="text-transform: uppercase;">
            </div>
            <div class="premium-form-group">
                <label>DISCOUNT PERCENT (%)</label>
                <input type="number" name="discount" class="premium-input" placeholder="e.g. 10.00" step="0.01" min="0" max="100" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%; height: 48px; border-radius: 12px;">GENERATE COUPON</button>
        </form>
    </div>

    <div class="admin-card-premium" style="padding: 30px; grid-column: span 3;">
        <h4 style="margin: 0 0 25px 0; font-family: 'Syne', sans-serif; font-size: 14px; text-transform: uppercase; letter-spacing: 1px;">Existing Coupons</h4>
        <div class="table-responsive">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Discount (%)</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th style="text-align:right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($coupons)): ?>
                        <tr><td colspan="5" style="text-align:center; padding:40px; color:rgba(255,255,255,0.2);">No coupons generated yet.</td></tr>
                    <?php else: ?>
                        <?php foreach($coupons as $c): ?>
                        <tr>
                            <td><strong style="color:#fff; font-family:var(--font-display);"><?php echo htmlspecialchars($c['code'], ENT_QUOTES, 'UTF-8'); ?></strong></td>
                            <td><span style="color: #10b981; font-weight: 800;"><?php echo number_format($c['discount_percent'], 1); ?>% OFF</span></td>
                            <td><span class="status-badge status-active">ACTIVE</span></td>
                            <td style="font-size: 11px; color: rgba(255,255,255,0.4);"><?php echo date('M d, Y', strtotime($c['created_at'])); ?></td>
                            <td style="text-align:right;">
                                <form action="coupon_handler.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                    <input type="hidden" name="id" value="<?php echo $c['id']; ?>">
                                    <button type="submit" class="action-btn-sleek action-btn-delete" style="width:36px; height:36px; border:none; padding:0; display:inline-flex; align-items:center; justify-content:center; cursor:pointer;" title="Delete Coupon">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
