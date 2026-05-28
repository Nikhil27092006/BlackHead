<?php
if (!isset($_SESSION['admin_id'])) exit;

// Self-Healing Table Creation
$pdo->exec("CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(255),
    subject VARCHAR(255),
    message TEXT,
    status ENUM('new', 'replied', 'archived') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// Fetch messages
$messages = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC")->fetchAll();

// Handle status updates
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $newStatus = $_GET['action'] == 'archive' ? 'archived' : 'replied';
    $pdo->prepare("UPDATE contact_messages SET status = ? WHERE id = ?")->execute([$newStatus, $id]);
    $_SESSION['success'] = "Message updated!";
    header("Location: index.php?page=messages");
    exit;
}
?>

<div class="admin-card-premium">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 35px;">
        <div>
            <h1 class="admin-page-title" style="margin:0;">Customer Inquiries</h1>
            <p class="admin-page-subtitle">Manage questions, feedback, and support requests from your store.</p>
        </div>
        <div style="background: rgba(139, 92, 246, 0.1); padding: 12px 20px; border-radius: 12px; border: 1px solid rgba(139, 92, 246, 0.2);">
            <div style="font-size: 11px; font-weight: 800; color: #a78bfa; text-transform: uppercase; letter-spacing: 1px;">Inbox Strategy</div>
            <div style="font-size: 14px; font-weight: 700; color: #fff; margin-top: 4px;">High-Priority Response</div>
        </div>
    </div>

    <div class="dashboard-table-card">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Customer</th>
                    <th>Subject & Message</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($messages)): ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 50px; color: rgba(255,255,255,0.2);">
                            <i class="fa-solid fa-envelope-open" style="font-size: 30px; margin-bottom: 15px; display: block;"></i>
                            Your inbox is currently empty.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($messages as $m): ?>
                        <tr style="<?php echo $m['status'] == 'archived' ? 'opacity: 0.5;' : ''; ?>">
                            <td style="font-size: 12px; font-weight: 600; color: rgba(255,255,255,0.4);">
                                <?php echo date('M d, Y', strtotime($m['created_at'])); ?><br>
                                <span style="font-size: 10px;"><?php echo date('h:i A', strtotime($m['created_at'])); ?></span>
                            </td>
                            <td>
                                <div style="font-weight: 700; color: #fff;"><?php echo htmlspecialchars($m['name']); ?></div>
                                <div style="font-size: 11px; color: #8b5cf6;"><?php echo htmlspecialchars($m['email']); ?></div>
                            </td>
                            <td>
                                <div style="font-weight: 800; font-size: 13px; color: #a78bfa; margin-bottom: 4px;"><?php echo htmlspecialchars($m['subject']); ?></div>
                                <div style="font-size: 13px; line-height: 1.5; color: rgba(255,255,255,0.6); max-width: 400px;">
                                    <?php echo nl2br(htmlspecialchars($m['message'])); ?>
                                </div>
                            </td>
                            <td>
                                <span class="badge" style="background: <?php 
                                    echo $m['status'] == 'new' ? 'rgba(139, 92, 246, 0.15); color: #a78bfa;' : 
                                        ($m['status'] == 'replied' ? 'rgba(16, 185, 129, 0.15); color: #34d399;' : 'rgba(255,255,255,0.05); color: rgba(255,255,255,0.4);'); 
                                ?> padding: 6px 12px; border-radius: 8px; font-size: 10px; font-weight: 800; text-transform: uppercase;">
                                    <?php echo $m['status']; ?>
                                </span>
                            </td>
                            <td>
                                <div style="display: flex; gap: 8px;">
                                    <?php if($m['status'] != 'replied'): ?>
                                    <a href="mailto:<?php echo $m['email']; ?>?subject=Re: <?php echo urlencode($m['subject']); ?>" 
                                       class="action-btn-sleek" style="background: #8b5cf6; padding: 8px 12px; border-radius: 8px; color: #fff; text-decoration:none; font-size: 11px; font-weight: 700;"
                                       onclick="setTimeout(() => window.location.href='index.php?page=messages&action=reply&id=<?php echo $m['id']; ?>', 1000)">
                                        <i class="fa-solid fa-reply"></i> Reply
                                    </a>
                                    <?php endif; ?>
                                    
                                    <?php if($m['status'] != 'archived'): ?>
                                    <a href="index.php?page=messages&action=archive&id=<?php echo $m['id']; ?>" 
                                       class="action-btn-sleek" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); padding: 8px 12px; border-radius: 8px; color: rgba(255,255,255,0.6); text-decoration:none; font-size: 11px; font-weight: 700;">
                                        <i class="fa-solid fa-box-archive"></i>
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
