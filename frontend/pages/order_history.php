<style>
.order-card {
    border: 1px solid var(--border-color);
    margin-bottom: var(--spacing-lg);
    padding: var(--spacing-lg);
}

.order-header {
    display: flex;
    justify-content: space-between;
    padding-bottom: var(--spacing-md);
    border-bottom: 1px solid var(--border-color);
    margin-bottom: var(--spacing-md);
    font-size: 14px;
}

.order-badge {
    padding: 4px 12px;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
}

.status-pending { background: #fff5e6; color: #cc7a00; }
.status-confirmed { background: #e6f3ff; color: #0056b3; }
.status-delivered { background: #e6ffed; color: #008033; }
.status-cancelled { background: #fff5f5; color: #cc0000; }
</style>

<?php
if (!isLoggedIn()) {
    echo "<script>window.location.href='index.php?page=login';</script>";
    exit;
}

$userId = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$userId]);
$orders = $stmt->fetchAll();
?>

<div class="container" style="padding: 60px 0;">
    <h1 style="font-weight: 900; text-transform: uppercase;">Order History (<?php echo count($orders); ?>)</h1>
    <p class="mb-5" style="color: var(--light-text);">Track and manage your recent orders.</p>

    <?php if(count($orders) > 0): ?>
        <?php foreach($orders as $order): ?>
            <div class="order-card">
                <div class="order-header">
                    <div>
                        <div style="font-weight: 700;">Order #<?php echo $order['order_number']; ?></div>
                        <div style="color: var(--light-text); font-size: 12px;"><?php echo date('d M Y, h:i A', strtotime($order['created_at'])); ?></div>
                    </div>
                    <div>
                        <span class="order-badge status-<?php echo $order['order_status']; ?>"><?php echo $order['order_status']; ?></span>
                    </div>
                </div>
                
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <p style="font-size: 14px; color: var(--light-text);">Payment: <span style="text-transform: uppercase; font-weight: 700; color: var(--text-color);"><?php echo $order['payment_method']; ?></span></p>
                        <p style="font-size: 14px; color: var(--light-text);">Total Amount: <span style="font-weight: 700; color: var(--text-color);"><?php echo formatPrice($order['final_amount']); ?></span></p>
                    </div>
                    <div>
                        <a href="index.php?page=order_detail&id=<?php echo $order['id']; ?>" class="btn btn-outline" style="font-size: 12px; padding: 8px 16px;">View Details</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div style="text-align: center; padding: 100px 0;">
            <i class="fa-solid fa-box-archive" style="font-size: 48px; color: #ccc; margin-bottom: 20px;"></i>
            <p>You haven't placed any orders yet.</p>
            <a href="index.php?page=products" class="btn btn-primary mt-3">Start Shopping</a>
        </div>
    <?php endif; ?>
</div>
