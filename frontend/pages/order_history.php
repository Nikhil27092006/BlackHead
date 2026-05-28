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

.status-pending { background: rgba(255, 171, 0, 0.1); color: #ffab00; }
.status-confirmed { background: rgba(0, 184, 217, 0.1); color: #00b8d9; }
.status-shipped { background: rgba(101, 84, 192, 0.1); color: #6554c0; }
.status-delivered { background: rgba(54, 179, 126, 0.1); color: #36b37e; }
.status-cancelled { background: rgba(255, 86, 48, 0.1); color: #ff5630; }
.status-returned { background: rgba(107, 119, 140, 0.1); color: #6b778c; }
.status-exchanged { background: rgba(139, 92, 246, 0.1); color: #8b5cf6; }

.order-card {
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.05);
    border-radius: 16px;
    padding: 24px;
    margin-bottom: 24px;
    transition: all 0.3s ease;
}

.order-card:hover {
    background: rgba(255, 255, 255, 0.05);
    border-color: rgba(139, 92, 246, 0.3);
    transform: translateY(-2px);
}
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
    <div style="margin-bottom: 40px;">
        <h1 style="font-weight: 900; text-transform: uppercase; font-size: 32px; letter-spacing: -1px; margin-bottom: 8px;">My Orders <span style="color: #8b5cf6;">(<?php echo count($orders); ?>)</span></h1>
        <p style="color: rgba(255,255,255,0.5); font-weight: 500;">Track, manage, and view your recent orders.</p>
    </div>

    <?php if(count($orders) > 0): ?>
        <div style="max-width: 900px;">
        <?php foreach($orders as $order): 
            $delivery_date = (!empty($order['expected_delivery'])) ? date('d M, Y', strtotime($order['expected_delivery'])) : date('d M, Y', strtotime($order['created_at']) + (5 * 86400));
        ?>
            <div class="order-card">
                <div class="order-header" style="border: none; margin-bottom: 20px;">
                    <div>
                        <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 1.5px; color: rgba(255,255,255,0.4); margin-bottom: 4px;">Order Number</div>
                        <div style="font-weight: 800; font-family: 'Outfit'; font-size: 16px;">#<?php echo htmlspecialchars($order['order_number'], ENT_QUOTES, 'UTF-8'); ?></div>
                    </div>
                    <div style="text-align: right;">
                        <span class="order-badge status-<?php echo $order['order_status']; ?>" style="border-radius: 6px; padding: 6px 12px;"><?php echo $order['order_status']; ?></span>
                        <div style="color: rgba(255,255,255,0.4); font-size: 11px; margin-top: 6px; font-weight: 600;"><?php echo date('M d, Y', strtotime($order['created_at'])); ?></div>
                    </div>
                </div>
                
                <div style="display: flex; justify-content: space-between; align-items: flex-end; gap: 20px; flex-wrap: wrap;">
                    <div style="flex: 1; min-width: 250px;">
                        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px; padding: 12px; background: rgba(255,255,255,0.02); border-radius: 12px;">
                            <i class="fa-solid fa-truck-fast" style="color: #8b5cf6;"></i>
                            <div style="font-size: 13px;">
                                <span style="color: rgba(255,255,255,0.5);">Expected Delivery:</span> 
                                <span style="font-weight: 700; color: #fff;"><?php echo $delivery_date; ?></span>
                            </div>
                        </div>
                        <div style="display: flex; gap: 40px;">
                            <div>
                                <div style="font-size: 11px; text-transform: uppercase; color: rgba(255,255,255,0.3); margin-bottom: 2px;">Total Amount</div>
                                <div style="font-weight: 800; color: #fff; font-size: 18px;"><?php echo formatPrice($order['final_amount']); ?></div>
                            </div>
                            <div>
                                <div style="font-size: 11px; text-transform: uppercase; color: rgba(255,255,255,0.3); margin-bottom: 2px;">Payment</div>
                                <div style="font-weight: 700; color: rgba(255,255,255,0.8); font-size: 14px; text-transform: uppercase;"><?php echo $order['payment_method']; ?></div>
                            </div>
                        </div>
                    </div>
                    <div style="display: flex; gap: 10px;">
                        <a href="index.php?page=order_detail&id=<?php echo $order['id']; ?>" class="btn" style="background: #fff; color: #000; border-radius: 100px; padding: 10px 24px; font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">View Order</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div style="text-align: center; padding: 100px 0; background: rgba(255,255,255,0.02); border-radius: 24px; border: 1px dashed rgba(255,255,255,0.1);">
            <i class="fa-solid fa-box-open" style="font-size: 64px; background: linear-gradient(135deg, #8b5cf6, #6366f1); -webkit-background-clip: text; -webkit-text-fill-color: transparent; margin-bottom: 24px; opacity: 0.5;"></i>
            <h3 style="font-weight: 800; margin-bottom: 12px;">No Orders Yet</h3>
            <p style="color: rgba(255,255,255,0.4); margin-bottom: 30px;">Seems like you haven't placed any orders with us yet.</p>
            <a href="index.php?page=products" class="btn btn-primary" style="padding: 14px 40px; border-radius: 100px;">Start Shopping</a>
        </div>
    <?php endif; ?>
</div>
