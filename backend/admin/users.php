<div class="admin-card-premium" style="padding: 0; overflow: hidden;">
    <div style="padding: 25px 35px; border-bottom: 1px solid var(--admin-border); display: flex; justify-content: space-between; align-items: center;">
        <h3 style="margin: 0; font-size: 18px; font-weight: 900; letter-spacing: -0.5px;">User Management</h3>
    </div>

    <div class="table-responsive">
        <table class="modern-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Joined</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();
                foreach($users as $u): 
                ?>
                <tr>
                    <td style="font-weight: 700; color: var(--admin-accent);">#<?php echo $u['id']; ?></td>
                    <td><strong style="color: var(--admin-text-main);"><?php echo $u['name']; ?></strong></td>
                    <td style="color: var(--admin-text-muted);"><?php echo $u['email']; ?></td>
                    <td><span class="status-badge-modern status-paid">active</span></td>
                    <td style="color: var(--admin-text-muted); font-size: 13px;"><?php echo date('d M Y', strtotime($u['created_at'])); ?></td>
                    <td style="text-align: right;">
                        <div style="display: flex; gap: 8px; justify-content: flex-end;">
                            <a href="index.php?page=orders&user_id=<?php echo $u['id']; ?>" class="action-btn-sleek" title="View Orders">
                                <i class="fa-solid fa-shopping-bag"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
