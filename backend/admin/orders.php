<div class="admin-card-premium" style="padding: 0; overflow: hidden;">
    <div style="padding: 25px 35px; border-bottom: 1px solid var(--admin-border); display: flex; justify-content: space-between; align-items: center;">
        <h3 style="margin: 0; font-size: 18px; font-weight: 900; letter-spacing: -0.5px;">All Orders</h3>
    </div>

    <div class="table-responsive">
        <table class="modern-table">
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Customer</th>
                    <th>Amount</th>
                    <th>Payment</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th style="text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $orders = $pdo->query("SELECT o.*, u.name as user_name FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC")->fetchAll();
                foreach($orders as $o): 
                ?>
                <tr>
                    <td><strong style="color: var(--admin-accent);">#<?php echo $o['order_number']; ?></strong></td>
                    <td>
                        <div style="font-weight: 700;"><?php echo $o['user_name']; ?></div>
                    </td>
                    <td style="font-weight: 800;"><?php echo formatPrice($o['final_amount']); ?></td>
                    <td><span style="text-transform: uppercase; font-size: 10px; font-weight: 800; color: var(--admin-text-muted);"><?php echo $o['payment_method']; ?></span></td>
                    <td>
                        <span class="status-badge-modern status-<?php echo strtolower($o['order_status']); ?>">
                            <?php echo $o['order_status']; ?>
                        </span>
                    </td>
                    <td style="color: var(--admin-text-muted); font-size: 13px;"><?php echo date('d M Y', strtotime($o['created_at'])); ?></td>
                    <td style="text-align: right;">
                        <div style="display: flex; gap: 10px; justify-content: flex-end; align-items: center;">
                            <select onchange="updateOrderStatus(<?php echo $o['id']; ?>, this.value)" class="premium-input" style="padding: 6px 10px; font-size: 11px; width: auto; font-weight: 700;">
                                <option value="pending" <?php echo $o['order_status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="confirmed" <?php echo $o['order_status'] == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                <option value="shipped" <?php echo $o['order_status'] == 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                <option value="delivered" <?php echo $o['order_status'] == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                <option value="cancelled" <?php echo $o['order_status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                            </select>
                            <a href="index.php?page=order_detail&id=<?php echo $o['id']; ?>" class="action-btn-sleek"><i class="fa-solid fa-eye"></i></a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function updateOrderStatus(orderId, status) {
    if(confirm('Update order status to ' + status + '?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'order_handler.php';
        
        const idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = 'order_id';
        idInput.value = orderId;
        
        const statusInput = document.createElement('input');
        statusInput.type = 'hidden';
        statusInput.name = 'status';
        statusInput.value = status;
        
        form.appendChild(idInput);
        form.appendChild(statusInput);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
