<?php
// Fetch basic stats
$totalSales    = $pdo->query("SELECT SUM(final_amount) FROM orders WHERE order_status != 'cancelled' AND payment_status = 'paid'")->fetchColumn() ?: 0;
$totalOrders   = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$totalUsers    = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalProducts = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();

// Detailed Revenue Split
$proRevenue    = $pdo->query("SELECT SUM(final_amount) FROM orders WHERE payment_status = 'paid' AND razorpay_payment_id IS NOT NULL")->fetchColumn() ?: 0;
$manualRevenue = $totalSales - $proRevenue;
$pendingRevenue = $pdo->query("SELECT SUM(final_amount) FROM orders WHERE payment_status = 'pending' AND order_status != 'cancelled'")->fetchColumn() ?: 0;

// Recent Orders
$stmt = $pdo->query("SELECT o.*, u.name as user_name FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC LIMIT 5");
$recentOrders = $stmt->fetchAll();
?>

<!-- Revenue Analytics Section -->
<div class="grid-3" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px;">
    <div class="admin-card-premium" style="padding: 20px; background: linear-gradient(135deg, #1e1b4b, #312e81); color: white; border: none; border-radius: 16px;">
        <div style="font-size: 10px; font-weight: 800; opacity: 0.6; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 5px;">Gateway Revenue</div>
        <div style="font-size: 24px; font-family: 'Syne', sans-serif; font-weight: 800;">₹<?php echo number_format($proRevenue, 0); ?></div>
        <div style="margin-top: 10px; font-size: 11px; background: rgba(255,255,255,0.1); padding: 5px 10px; border-radius: 6px; display: inline-block;">
            Verified via Razorpay
        </div>
    </div>
    <div class="admin-card-premium" style="padding: 20px; background: linear-gradient(135deg, #064e3b, #065f46); color: white; border: none; border-radius: 16px;">
        <div style="font-size: 10px; font-weight: 800; opacity: 0.6; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 5px;">Manual Revenue</div>
        <div style="font-size: 24px; font-family: 'Syne', sans-serif; font-weight: 800; color: #fff;">₹<?php echo number_format($manualRevenue, 0); ?></div>
        <div style="margin-top: 10px; font-size: 11px; background: rgba(255,255,255,0.1); padding: 5px 10px; border-radius: 6px; display: inline-block; color: #fff;">
            COD & Manual UPI fallback
        </div>
    </div>
    <div class="admin-card-premium" style="padding: 20px; background: #fff7ed; border: 1px solid #ffedd5; border-radius: 16px;">
        <div style="font-size: 10px; font-weight: 800; color: #c2410c; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 5px;">Pending Payments</div>
        <div style="font-size: 24px; font-family: 'Syne', sans-serif; font-weight: 800; color: #9a3412;">₹<?php echo number_format($pendingRevenue, 0); ?></div>
        <div style="margin-top: 10px; font-size: 11px; color: #f97316; font-weight: 600;">
            Awaiting verification
        </div>
    </div>
</div>

<div class="stats-grid">
    <!-- Total Sales -->
    <div class="admin-card-premium stat-card-modern" style="padding: 28px; display:flex; align-items:center; gap:20px; border-left: 2px solid rgba(99,102,241,0.5);">
        <div style="width:58px; height:58px; border-radius:16px; background:rgba(99,102,241,0.12); border:1px solid rgba(99,102,241,0.2); color:#818cf8; display:flex; align-items:center; justify-content:center; font-size:22px; flex-shrink:0;">
            <i class="fa-solid fa-indian-rupee-sign"></i>
        </div>
        <div>
            <div style="font-size:11px; font-weight:800; color:rgba(255,255,255,0.3); text-transform:uppercase; letter-spacing:2px; margin-bottom:6px;">Total Sales</div>
            <div style="font-family:'Syne',sans-serif; font-size:1.7rem; font-weight:800; color:#fff; letter-spacing:-1px; line-height:1;"><?php echo formatPrice($totalSales); ?></div>
        </div>
    </div>

    <!-- Total Orders -->
    <div class="admin-card-premium stat-card-modern" style="padding: 28px; display:flex; align-items:center; gap:20px; border-left: 2px solid rgba(16,185,129,0.5);">
        <div style="width:58px; height:58px; border-radius:16px; background:rgba(16,185,129,0.1); border:1px solid rgba(16,185,129,0.2); color:#34d399; display:flex; align-items:center; justify-content:center; font-size:22px; flex-shrink:0;">
            <i class="fa-solid fa-bag-shopping"></i>
        </div>
        <div>
            <div style="font-size:11px; font-weight:800; color:rgba(255,255,255,0.3); text-transform:uppercase; letter-spacing:2px; margin-bottom:6px;">Total Orders</div>
            <div style="font-family:'Syne',sans-serif; font-size:1.7rem; font-weight:800; color:#fff; letter-spacing:-1px; line-height:1;"><?php echo $totalOrders; ?></div>
        </div>
    </div>

    <!-- Total Users -->
    <div class="admin-card-premium stat-card-modern" style="padding: 28px; display:flex; align-items:center; gap:20px; border-left: 2px solid rgba(139,92,246,0.5);">
        <div style="width:58px; height:58px; border-radius:16px; background:rgba(139,92,246,0.12); border:1px solid rgba(139,92,246,0.2); color:#a78bfa; display:flex; align-items:center; justify-content:center; font-size:22px; flex-shrink:0;">
            <i class="fa-solid fa-users"></i>
        </div>
        <div>
            <div style="font-size:11px; font-weight:800; color:rgba(255,255,255,0.3); text-transform:uppercase; letter-spacing:2px; margin-bottom:6px;">Total Users</div>
            <div style="font-family:'Syne',sans-serif; font-size:1.7rem; font-weight:800; color:#fff; letter-spacing:-1px; line-height:1;"><?php echo $totalUsers; ?></div>
        </div>
    </div>

    <!-- Products -->
    <div class="admin-card-premium stat-card-modern" style="padding: 28px; display:flex; align-items:center; gap:20px; border-left: 2px solid rgba(245,158,11,0.5);">
        <div style="width:58px; height:58px; border-radius:16px; background:rgba(245,158,11,0.1); border:1px solid rgba(245,158,11,0.2); color:#fbbf24; display:flex; align-items:center; justify-content:center; font-size:22px; flex-shrink:0;">
            <i class="fa-solid fa-box-open"></i>
        </div>
        <div>
            <div style="font-size:11px; font-weight:800; color:rgba(255,255,255,0.3); text-transform:uppercase; letter-spacing:2px; margin-bottom:6px;">Products</div>
            <div style="font-family:'Syne',sans-serif; font-size:1.7rem; font-weight:800; color:#fff; letter-spacing:-1px; line-height:1;"><?php echo $totalProducts; ?></div>
        </div>
    </div>
</div>


<style>
    /* Recent Orders Table Styles */
    .dashboard-table-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.03);
        border: 1px solid rgba(0,0,0,0.05);
        overflow: hidden;
    }
    .dashboard-table-header {
        padding: 25px 30px;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #fafafa;
    }
    .dashboard-table-header h3 {
        margin: 0;
        font-weight: 900;
        text-transform: uppercase;
        font-size: 18px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .modern-table {
        width: 100%;
        border-collapse: collapse;
    }
    .modern-table th {
        text-align: left;
        padding: 15px 30px;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #888;
        font-weight: 700;
        border-bottom: 2px solid #eee;
    }
    .modern-table td {
        padding: 20px 30px;
        border-bottom: 1px solid #f0f0f0;
        font-size: 14px;
        vertical-align: middle;
    }
    .modern-table tbody tr {


<div class="admin-card-premium" style="padding: 0; overflow: hidden;">
    <div style="padding: 25px 35px; border-bottom: 1px solid var(--admin-border); display: flex; justify-content: space-between; align-items: center;">
        <h3 style="margin: 0; font-size: 18px; font-weight: 900; letter-spacing: -0.5px;">Recent Orders</h3>
        <a href="index.php?page=orders" class="btn btn-primary" style="font-size: 11px; padding: 8px 16px; border-radius: 8px;">View All</a>
    </div>
    <div class="table-responsive">
        <table class="modern-table">
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Customer</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($recentOrders as $order): ?>
                <tr>
                    <td><span style="font-weight: 800; color: var(--admin-accent);">#<?php echo $order['order_number']; ?></span></td>
                    <td>
                        <div style="font-weight: 700;"><?php echo $order['user_name']; ?></div>
                    </td>
                    <td style="font-weight: 800;"><?php echo formatPrice($order['final_amount']); ?></td>
                    <td>
                        <span class="status-badge-modern status-<?php echo strtolower($order['order_status']); ?>">
                            <?php echo $order['order_status']; ?>
                        </span>
                    </td>
                    <td style="color: var(--admin-text-muted); font-size: 13px;"><?php echo date('d M, Y', strtotime($order['created_at'])); ?></td>
                    <td>
                        <a href="index.php?page=order_detail&id=<?php echo $order['id']; ?>" class="action-btn-sleek"><i class="fa-solid fa-eye"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($recentOrders)): ?>
                    <tr><td colspan="6" style="text-align: center; padding: 40px; color: var(--admin-text-muted);">No recent orders.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
