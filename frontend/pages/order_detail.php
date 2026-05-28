<style>
.order-detail-header {
    border-bottom: 2px solid var(--primary-color);
    padding-bottom: var(--spacing-md);
    margin-bottom: var(--spacing-xl);
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    gap: var(--spacing-md);
}

@media (max-width: 480px) {
    .order-detail-header {
        flex-direction: column;
        align-items: flex-start;
    }
    .order-detail-header div:last-child {
        text-align: left !important;
    }
}
.order-badge {
    padding: 6px 15px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    border-radius: 20px;
}
.status-pending { background: rgba(255, 171, 0, 0.1); color: #ffab00; }
.status-confirmed { background: rgba(0, 184, 217, 0.1); color: #00b8d9; }
.status-shipped { background: rgba(101, 84, 192, 0.1); color: #6554c0; }
.status-delivered { background: rgba(54, 179, 126, 0.1); color: #36b37e; }
.status-cancelled { background: rgba(255, 86, 48, 0.1); color: #ff5630; }
.status-returned { background: rgba(107, 119, 140, 0.1); color: #6b778c; }
.status-exchanged { background: rgba(139, 92, 246, 0.1); color: #8b5cf6; }

.action-btn {
    padding: 12px 24px;
    border-radius: 100px;
    font-size: 13px;
    font-weight: 700;
    text-transform: uppercase;
    cursor: pointer;
    transition: all 0.3s;
    border: none;
    display: flex;
    align-items: center;
    gap: 8px;
}

.action-btn.cancel { background: rgba(255, 86, 48, 0.1); color: #ff5630; }
.action-btn.cancel:hover { background: #ff5630; color: #fff; }
.action-btn.return { background: rgba(139, 92, 246, 0.1); color: #8b5cf6; }
.action-btn.return:hover { background: #8b5cf6; color: #fff; }
.action-btn.exchange { background: rgba(0, 184, 217, 0.1); color: #00b8d9; }
.action-btn.exchange:hover { background: #00b8d9; color: #fff; }
</style>

<?php
if (!isLoggedIn()) {
    header("Location: index.php?page=login");
    exit;
}

$orderId = $_GET['id'] ?? null;
$userId = $_SESSION['user_id'];

if (!$orderId) {
    header("Location: index.php?page=order_history");
    exit;
}

// Fetch Order
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->execute([$orderId, $userId]);
$order = $stmt->fetch();

if (!$order) {
    header("Location: index.php?page=404");
    exit;
}

// Fetch Items
$stmt = $pdo->prepare("SELECT oi.*, p.name, p.slug, pi.image as product_image 
                     FROM order_items oi 
                     JOIN products p ON oi.product_id = p.id 
                     LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_main = 1 
                     WHERE oi.order_id = ?");
$stmt->execute([$orderId]);
$items = $stmt->fetchAll();

$address = json_decode($order['shipping_address'], true);
?>

<div class="container" style="padding: 60px 0;">
    <div class="order-detail-header">
        <div>
            <a href="index.php?page=order_history" style="font-size: 12px; color: var(--light-text); text-decoration: underline;"><i class="fa-solid fa-arrow-left"></i> Back to History</a>
            <h1 style="font-weight: 900; text-transform: uppercase; margin-top: 10px;">Order #<?php echo htmlspecialchars($order['order_number'], ENT_QUOTES, 'UTF-8'); ?></h1>
        </div>
        <div style="text-align: right;">
            <span class="order-badge status-<?php echo $order['order_status']; ?>"><?php echo $order['order_status']; ?></span>
            <p style="font-size: 12px; color: var(--light-text); margin-top: 5px;"><?php echo date('d M Y', strtotime($order['created_at'])); ?></p>
        </div>
    </div>

    <div class="grid-2-1">
        <div>
            <div style="background: rgba(255,255,255,0.02); padding: 30px; border-radius: 20px; border: 1px solid rgba(255,255,255,0.05); margin-bottom: 30px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
                    <h3 style="text-transform: uppercase; font-weight: 900; font-size: 16px;">Items Ordered</h3>
                    <div style="font-size: 13px; color: rgba(255,255,255,0.5);">
                        <i class="fa-solid fa-truck-fast" style="color: #8b5cf6; margin-right: 6px;"></i>
                        Expected: <strong><?php echo (!empty($order['expected_delivery'])) ? date('d M, Y', strtotime($order['expected_delivery'])) : date('d M, Y', strtotime($order['created_at']) + (5 * 86400)); ?></strong>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="cart-table">
                        <thead>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                                <th style="text-align: left; padding: 15px 0;">Product</th>
                                <th style="text-align: center; width: 120px;">Price</th>
                                <th style="text-align: center; width: 80px;">Qty</th>
                                <th style="text-align: right; width: 120px;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($items as $item): ?>
                            <tr class="cart-item" style="border-bottom: 1px solid rgba(255,255,255,0.02);">
                                <td style="padding: 20px 0;">
                                    <div style="display: flex; gap: 20px; align-items: center;">
                                        <div style="width: 70px; height: 90px; background: #111; overflow: hidden; border-radius: 12px; border: 1px solid rgba(255,255,255,0.05);">
                                            <img src="assets/images/<?php echo $item['product_image'] ?: 'placeholder.jpg'; ?>" alt="" style="width: 100%; height: 100%; object-fit: cover;">
                                        </div>
                                        <div style="display: flex; flex-direction: column; gap: 4px;">
                                            <span style="font-weight: 800; font-size: 15px;"><?php echo $item['name']; ?></span>
                                            <?php if($item['size']): ?>
                                                <span style="font-size: 11px; color: #8b5cf6; text-transform: uppercase; font-weight: 700; background: rgba(139, 92, 246, 0.1); padding: 2px 8px; border-radius: 4px; align-self: flex-start;">Size: <?php echo $item['size']; ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td style="text-align: center; font-weight: 600;"><?php echo formatPrice($item['price']); ?></td>
                                <td style="text-align: center; font-weight: 600;"><?php echo $item['quantity']; ?></td>
                                <td style="text-align: right; font-weight: 900; color: #fff;"><?php echo formatPrice($item['total']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Order Actions -->
                <div style="margin-top: 40px; padding-top: 30px; border-top: 1px solid rgba(255,255,255,0.05); display: flex; gap: 15px; flex-wrap: wrap;">
                    <?php if(in_array($order['order_status'], ['pending', 'confirmed'])): ?>
                        <button onclick="confirmAction('cancel')" class="action-btn cancel">
                            <i class="fa-solid fa-xmark"></i> Cancel Order
                        </button>
                    <?php elseif($order['order_status'] == 'delivered'): ?>
                        <button onclick="confirmAction('return')" class="action-btn return">
                            <i class="fa-solid fa-rotate-left"></i> Return Order
                        </button>
                        <button onclick="confirmAction('exchange')" class="action-btn exchange">
                            <i class="fa-solid fa-right-left"></i> Exchange Order
                        </button>
                    <?php endif; ?>
                    
                    <?php if($order['order_status'] == 'cancelled'): ?>
                        <div style="background: rgba(255, 86, 48, 0.05); border: 1px solid rgba(255, 86, 48, 0.1); padding: 15px 20px; border-radius: 12px; width: 100%;">
                            <div style="color: #ff5630; font-weight: 700; font-size: 14px; margin-bottom: 4px;">Order Cancelled</div>
                            <div style="font-size: 13px; color: rgba(255,255,255,0.4);"><?php echo $order['cancel_reason'] ?: 'No reason provided.'; ?></div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div>
            <div style="background: rgba(255,255,255,0.03); padding: 30px; border-radius: 20px; border: 1px solid rgba(255,255,255,0.05); margin-bottom: 25px;">
                <h3 class="mb-4" style="font-size: 14px; text-transform: uppercase; letter-spacing: 1px; color: rgba(255,255,255,0.4);">Shipping Address</h3>
                <div style="font-size: 14px; line-height: 1.8; color: rgba(255,255,255,0.8);">
                    <strong style="color: #fff; font-size: 16px; display: block; margin-bottom: 8px;"><?php echo htmlspecialchars($address['name'], ENT_QUOTES, 'UTF-8'); ?></strong>
                    <div style="margin-bottom: 12px;"><?php echo htmlspecialchars($address['address'], ENT_QUOTES, 'UTF-8'); ?><br>
                    <?php echo htmlspecialchars($address['city'], ENT_QUOTES, 'UTF-8'); ?>, <?php echo htmlspecialchars($address['state'], ENT_QUOTES, 'UTF-8'); ?> - <?php echo htmlspecialchars($address['pincode'], ENT_QUOTES, 'UTF-8'); ?></div>
                    <div style="display: flex; align-items: center; gap: 8px; padding-top: 12px; border-top: 1px solid rgba(255,255,255,0.05);">
                        <i class="fa-solid fa-phone" style="font-size: 12px; color: #8b5cf6;"></i>
                        <strong><?php echo htmlspecialchars($address['phone'], ENT_QUOTES, 'UTF-8'); ?></strong>
                    </div>
                </div>
            </div>

            <div style="background: linear-gradient(135deg, rgba(139, 92, 246, 0.05), rgba(99, 102, 241, 0.05)); padding: 30px; border-radius: 20px; border: 1px solid rgba(139, 92, 246, 0.1);">
                <h3 class="mb-4" style="font-size: 14px; text-transform: uppercase; letter-spacing: 1px; color: rgba(255,255,255,0.4);">Order Summary</h3>
                <div style="display: flex; justify-content: space-between; margin-bottom: 15px; font-size: 14px;">
                    <span style="color: rgba(255,255,255,0.5);">Subtotal</span>
                    <span style="font-weight: 600;"><?php echo formatPrice($order['total_amount']); ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 15px; font-size: 14px;">
                    <span style="color: rgba(255,255,255,0.5);">Shipping</span>
                    <span style="font-weight: 600; color: #36b37e;"><?php echo $order['shipping_amount'] == 0 ? 'FREE' : formatPrice($order['shipping_amount']); ?></span>
                </div>
                <?php if($order['discount_amount'] > 0): ?>
                <div style="display: flex; justify-content: space-between; margin-bottom: 15px; font-size: 14px;">
                    <span style="color: #36b37e; font-weight: 700;">Coupon (<?php echo $order['coupon_code']; ?>)</span>
                    <span style="font-weight: 600; color: #36b37e;">-<?php echo formatPrice($order['discount_amount']); ?></span>
                </div>
                <?php endif; ?>
                <div style="display: flex; justify-content: space-between; margin-bottom: 15px; font-size: 14px;">
                    <span style="color: rgba(255,255,255,0.5);">Payment Method</span>
                    <span style="font-weight: 800; text-transform: uppercase; color: #8b5cf6;"><?php echo $order['payment_method']; ?></span>
                </div>
                <?php if(!empty($order['razorpay_payment_id'])): ?>
                <div style="display: flex; justify-content: space-between; margin-bottom: 15px; font-size: 14px;">
                    <span style="color: rgba(255,255,255,0.5);">Transaction ID</span>
                    <span style="font-weight: 600; font-family: monospace; font-size: 12px; color: #8b5cf6;"><?php echo $order['razorpay_payment_id']; ?></span>
                </div>
                <?php endif; ?>
                <div style="display: flex; justify-content: space-between; margin-top: 25px; padding-top: 20px; border-top: 2px dashed rgba(255,255,255,0.1);">
                    <span style="font-weight: 800; font-size: 18px;">Total</span>
                    <span style="font-weight: 900; font-size: 22px; color: #fff;"><?php echo formatPrice($order['final_amount']); ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function confirmAction(type) {
    const messages = {
        'cancel': 'Are you sure you want to cancel this order?',
        'return': 'Are you sure you want to return this order?',
        'exchange': 'Are you sure you want to exchange this order?'
    };
    
    if (confirm(messages[type])) {
        const reason = prompt("Please provide a reason for " + type + ":");
        if (reason !== null) {
            window.location.href = `backend/handlers/order_action.php?id=<?php echo (int)$orderId; ?>&type=${type}&reason=${encodeURIComponent(reason)}`;
        }
    }
}
</script>
