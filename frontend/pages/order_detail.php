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
.status-pending { background: #fff5e6; color: #cc7a00; }
.status-confirmed { background: #e6f3ff; color: #0056b3; }
.status-delivered { background: #e6ffed; color: #008033; }
.status-cancelled { background: #fff5f5; color: #cc0000; }
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
            <h1 style="font-weight: 900; text-transform: uppercase; margin-top: 10px;">Order #<?php echo $order['order_number']; ?></h1>
        </div>
        <div style="text-align: right;">
            <span class="order-badge status-<?php echo $order['order_status']; ?>"><?php echo $order['order_status']; ?></span>
            <p style="font-size: 12px; color: var(--light-text); margin-top: 5px;"><?php echo date('d M Y', strtotime($order['created_at'])); ?></p>
        </div>
    </div>

    <div class="grid-2-1">
        <div>
            <h3 class="mb-4" style="text-transform: uppercase; font-weight: 900;">Items</h3>
            <div class="table-responsive">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th style="width: 100px;">Price</th>
                            <th style="width: 80px;">Qty</th>
                            <th style="width: 100px;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($items as $item): ?>
                        <tr class="cart-item">
                            <td>
                                <div style="display: flex; gap: 15px; align-items: center;">
                                    <div style="width: 60px; height: 60px; background: var(--light-bg); overflow: hidden;">
                                        <img src="assets/images/<?php echo $item['product_image'] ?: 'placeholder.jpg'; ?>" alt="" style="width: 100%; height: 100%; object-fit: contain;">
                                    </div>
                                    <div style="display: flex; flex-direction: column;">
                                        <span style="font-weight: 700; font-size: 14px;"><?php echo $item['name']; ?></span>
                                        <?php if($item['size']): ?>
                                            <span style="font-size: 11px; color: var(--light-text); text-transform: uppercase;">Size: <?php echo $item['size']; ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td><?php echo formatPrice($item['price']); ?></td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td style="font-weight: 700;"><?php echo formatPrice($item['total']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div>
            <div style="background: var(--light-bg); padding: 25px; margin-bottom: 25px;">
                <h3 class="mb-3" style="font-size: 14px; text-transform: uppercase;">Shipping Address</h3>
                <p style="font-size: 14px; line-height: 1.6;">
                    <strong><?php echo $address['name']; ?></strong><br>
                    <?php echo $address['address']; ?><br>
                    <?php echo $address['city']; ?>, <?php echo $address['state']; ?> - <?php echo $address['pincode']; ?><br>
                    Phone: <?php echo $address['phone']; ?>
                </p>
            </div>

            <div style="background: var(--light-bg); padding: 25px;">
                <h3 class="mb-3" style="font-size: 14px; text-transform: uppercase;">Payment Details</h3>
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 14px;">
                    <span>Method</span>
                    <span style="text-transform: uppercase; font-weight: 700;"><?php echo $order['payment_method']; ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 14px;">
                    <span>Subtotal</span>
                    <span><?php echo formatPrice($order['total_amount']); ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 14px;">
                    <span>Shipping</span>
                    <span><?php echo $order['shipping_amount'] == 0 ? 'FREE' : formatPrice($order['shipping_amount']); ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-top: 15px; pt-15; border-top: 1px solid #ddd; font-weight: 900; font-size: 18px;">
                    <span>Total</span>
                    <span><?php echo formatPrice($order['final_amount']); ?></span>
                </div>
            </div>
        </div>
    </div>
</div>
