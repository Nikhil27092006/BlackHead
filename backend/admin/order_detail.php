<?php
$id = (int)$_GET['id'];
$order = $pdo->prepare("
    SELECT o.*, u.name as user_name, u.email, u.phone
    FROM orders o
    JOIN users u ON o.user_id = u.id
    WHERE o.id = ?
");
$order->execute([$id]);
$order = $order->fetch();

if (!$order) {
    header("Location: index.php?page=orders");
    exit;
}

// Get order items
$items = $pdo->prepare("
    SELECT oi.*, p.name as product_name, p.sku, pv.size, pv.color
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    LEFT JOIN product_variants pv ON oi.variant_id = pv.id
    WHERE oi.order_id = ?
");
$items->execute([$id]);
$items = $items->fetchAll();
?>

<div class="admin-card-premium">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h3 class="admin-page-title" style="margin: 0; font-size: 24px;">Order Details - #<?php echo $order['order_number']; ?></h3>
        <a href="index.php?page=orders" class="btn btn-outline" style="padding: 10px 20px; font-size: 11px; font-weight: 800; border-radius: 8px;">BACK TO ORDERS</a>
    </div>

    <div class="grid-2" style="gap: 30px;">
        <!-- Order Info -->
        <div style="background: var(--admin-bg); padding: 30px; border-radius: 15px;">
            <h4 style="margin-top: 0; margin-bottom: 20px; font-weight: 800; font-size: 16px; color: var(--admin-accent); text-transform: uppercase; letter-spacing: 1px;">Order Information</h4>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div>
                    <span style="font-size: 11px; font-weight: 800; color: var(--admin-text-muted); text-transform: uppercase;">Order Number</span>
                    <div style="font-weight: 700; color: var(--admin-text-main);"><?php echo $order['order_number']; ?></div>
                </div>
                <div>
                    <span style="font-size: 11px; font-weight: 800; color: var(--admin-text-muted); text-transform: uppercase;">Date</span>
                    <div style="font-weight: 700; color: var(--admin-text-main);"><?php echo date('d M Y H:i', strtotime($order['created_at'])); ?></div>
                </div>
                <div>
                    <span style="font-size: 11px; font-weight: 800; color: var(--admin-text-muted); text-transform: uppercase;">Payment Method</span>
                    <div style="font-weight: 700; color: var(--admin-text-main);"><?php echo ucfirst($order['payment_method']); ?></div>
                </div>
                <div>
                    <span style="font-size: 11px; font-weight: 800; color: var(--admin-text-muted); text-transform: uppercase;">Status</span>
                    <div>
                        <span class="status-badge-modern status-<?php echo strtolower($order['order_status']); ?>">
                            <?php echo $order['order_status']; ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer Info -->
        <div style="background: var(--admin-bg); padding: 30px; border-radius: 15px;">
            <h4 style="margin-top: 0; margin-bottom: 20px; font-weight: 800; font-size: 16px; color: var(--admin-accent); text-transform: uppercase; letter-spacing: 1px;">Customer Information</h4>
            <div style="margin-bottom: 20px;">
                <span style="font-size: 11px; font-weight: 800; color: var(--admin-text-muted); text-transform: uppercase;">Name</span>
                <div style="font-weight: 700; color: var(--admin-text-main);"><?php echo htmlspecialchars($order['user_name']); ?></div>
            </div>
            <div style="margin-bottom: 20px;">
                <span style="font-size: 11px; font-weight: 800; color: var(--admin-text-muted); text-transform: uppercase;">Email</span>
                <div style="font-weight: 700; color: var(--admin-text-main);"><?php echo htmlspecialchars($order['email']); ?></div>
            </div>
            <div style="margin-bottom: 20px;">
                <span style="font-size: 11px; font-weight: 800; color: var(--admin-text-muted); text-transform: uppercase;">Phone</span>
                <div style="font-weight: 700; color: var(--admin-text-main);"><?php echo htmlspecialchars($order['phone']); ?></div>
            </div>
        </div>
    </div>

    <!-- Shipping Address -->
    <div style="margin-top: 30px; background: var(--admin-bg); padding: 30px; border-radius: 15px;">
        <h4 style="margin-top: 0; margin-bottom: 20px; font-weight: 800; font-size: 16px; color: var(--admin-accent); text-transform: uppercase; letter-spacing: 1px;">Shipping Address</h4>
        <div style="font-weight: 600; color: var(--admin-text-main); line-height: 1.6;">
            <?php
            $addressData = json_decode($order['shipping_address'], true);
            if ($addressData && !empty($addressData['address'])) {
                echo htmlspecialchars($addressData['address']) . '<br>';
                echo htmlspecialchars($addressData['city']) . ', ' . htmlspecialchars($addressData['state']) . ' - ' . htmlspecialchars($addressData['pincode']) . '<br>';
                if (!empty($addressData['country'])) echo htmlspecialchars($addressData['country']);
            } else {
                echo 'No address provided';
            }
            ?>
        </div>
    </div>

    <!-- Order Items -->
    <div style="margin-top: 30px; background: var(--admin-bg); padding: 30px; border-radius: 15px;">
        <h4 style="margin-top: 0; margin-bottom: 20px; font-weight: 800; font-size: 16px; color: var(--admin-accent); text-transform: uppercase; letter-spacing: 1px;">Order Items</h4>

        <div class="table-responsive">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Variant</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($items as $item): ?>
                    <tr>
                        <td>
                            <div style="font-weight: 700; color: var(--admin-text-main);"><?php echo htmlspecialchars($item['product_name']); ?></div>
                            <div style="font-size: 10px; color: var(--admin-text-muted); text-transform: uppercase; font-weight: 800;">SKU: <?php echo htmlspecialchars($item['sku']); ?></div>
                        </td>
                        <td>
                            <?php
                            $variant = [];
                            if ($item['size']) $variant[] = 'Size: ' . $item['size'];
                            if ($item['color']) $variant[] = 'Color: ' . $item['color'];
                            echo implode(', ', $variant);
                            if (empty($variant)) echo '-';
                            ?>
                        </td>
                        <td style="font-weight: 800;"><?php echo $item['quantity']; ?></td>
                        <td style="font-weight: 800; color: var(--admin-accent);">₹<?php echo number_format($item['price'], 2); ?></td>
                        <td style="font-weight: 800; color: var(--admin-accent);">₹<?php echo number_format($item['total'], 2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid var(--admin-border); display: flex; justify-content: flex-end;">
            <div style="text-align: right;">
                <div style="margin-bottom: 10px; font-size: 14px; color: var(--admin-text-muted);">Subtotal: <span style="font-weight: 700; color: var(--admin-text-main);">₹<?php echo number_format($order['total_amount'] ?? 0, 2); ?></span></div>
                <?php if (($order['discount_amount'] ?? 0) > 0): ?>
                <div style="margin-bottom: 10px; font-size: 14px; color: var(--admin-text-muted);">Discount: <span style="font-weight: 700; color: #ef4444;">-₹<?php echo number_format($order['discount_amount'], 2); ?></span></div>
                <?php endif; ?>
                <div style="margin-bottom: 10px; font-size: 14px; color: var(--admin-text-muted);">Shipping: <span style="font-weight: 700; color: var(--admin-text-main);">₹<?php echo number_format($order['shipping_amount'], 2); ?></span></div>
                <div style="font-size: 18px; font-weight: 800; color: var(--admin-accent);">Total: ₹<?php echo number_format($order['final_amount'], 2); ?></div>
            </div>
        </div>
    </div>
</div>
