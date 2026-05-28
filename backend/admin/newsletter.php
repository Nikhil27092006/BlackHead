<?php
if (!isset($_SESSION['admin_id'])) exit;

// Self-Healing: Create table if not exists
$pdo->exec("CREATE TABLE IF NOT EXISTS subscribers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    status ENUM('active', 'unsubscribed') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// Fetch subscribers
$subscribers = $pdo->query("SELECT * FROM subscribers WHERE status = 'active' ORDER BY created_at DESC")->fetchAll();
?>

<div class="admin-card-premium">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 35px;">
        <div>
            <h1 class="admin-page-title" style="margin:0;">Newsletter Broadcast</h1>
            <p class="admin-page-subtitle">Send updates, offers, and early-access info to your inner circle.</p>
        </div>
        <div style="text-align: right;">
            <div style="font-size: 24px; font-weight: 900; color: #8b5cf6; line-height:1;"><?php echo count($subscribers); ?></div>
            <div style="font-size: 10px; font-weight: 800; color: rgba(255,255,255,0.3); text-transform: uppercase; margin-top: 5px;">Active Members</div>
        </div>
    </div>

    <div class="grid-2-1" style="gap: 30px;">
        <!-- Broadcast Form -->
        <div style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05); padding: 30px; border-radius: 20px;">
            <h4 style="margin-top:0; margin-bottom: 25px; font-weight: 800; font-size: 14px; text-transform: uppercase; letter-spacing: 1px; color: #8b5cf6;">Compose Broadcast</h4>
            
            <form id="broadcastForm">
                <div class="premium-form-group">
                    <label>Email Subject</label>
                    <input type="text" name="subject" class="premium-input" placeholder="e.g. New Experimental Drop #04 Available Now" required>
                </div>

                <div class="premium-form-group">
                    <label>Message Content (HTML allowed)</label>
                    <textarea name="message" class="premium-input" rows="12" placeholder="Tell your subscribers what's new..." required></textarea>
                </div>

                <div style="margin-top: 30px; padding: 20px; background: rgba(139, 92, 246, 0.05); border-radius: 12px; border: 1px dashed rgba(139, 92, 246, 0.2);">
                    <div style="display: flex; align-items: flex-start; gap: 12px;">
                        <i class="fa-solid fa-circle-info" style="color: #8b5cf6; margin-top: 3px;"></i>
                        <p style="font-size: 12px; line-height: 1.5; color: rgba(255,255,255,0.5); margin:0;">This will send a direct email to <strong><?php echo count($subscribers); ?></strong> active subscribers. Use professional language to maintain premium brand identity.</p>
                    </div>
                </div>

                <button type="submit" id="broadcastBtn" class="btn" style="width: 100%; margin-top: 25px; background: #8b5cf6; color: white; padding: 15px; border-radius: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; border:none; cursor:pointer; transition: 0.3s;">
                    <i class="fa-solid fa-paper-plane" style="margin-right: 10px;"></i> Start Broadcast
                </button>
            </form>
            <div id="broadcastStatus" style="margin-top: 20px; display: none;"></div>
        </div>

        <!-- Recent Subscribers -->
        <div style="background: rgba(255,255,255,0.01); border: 1px solid rgba(255,255,255,0.03); padding: 25px; border-radius: 20px;">
            <h4 style="margin-top:0; margin-bottom: 20px; font-weight: 800; font-size: 13px; text-transform: uppercase; letter-spacing: 1px;">Inner Circle Members</h4>
            <div style="max-height: 500px; overflow-y: auto;">
                <?php if(empty($subscribers)): ?>
                    <p style="text-align:center; color: rgba(255,255,255,0.2); font-size: 13px; padding: 40px 0;">No subscribers yet.</p>
                <?php else: ?>
                    <table style="width: 100%; border-collapse: collapse;">
                        <?php foreach($subscribers as $s): ?>
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.03);">
                            <td style="padding: 12px 0;">
                                <div style="font-size: 13px; font-weight: 600; color: #fff;"><?php echo htmlspecialchars($s['email']); ?></div>
                                <div style="font-size: 10px; color: rgba(255,255,255,0.3);">Joined <?php echo date('M d, Y', strtotime($s['created_at'])); ?></div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('broadcastForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    if(!confirm('Send this broadcast to ALL subscribers?')) return;

    const btn = document.getElementById('broadcastBtn');
    const status = document.getElementById('broadcastStatus');
    const formData = new FormData(this);

    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin" style="margin-right: 10px;"></i> SENDING...';
    status.style.display = 'block';
    status.innerHTML = '<div style="color: #8b5cf6; font-size: 13px; font-weight: 600;">Broadcast initiated. Pleast do not close this window.</div>';

    fetch('newsletter_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if(data.success) {
            status.innerHTML = '<div style="padding: 15px; background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.2); color: #10b981; border-radius: 10px; font-size: 13px; font-weight: 600;"><i class="fa-solid fa-circle-check" style="margin-right:8px;"></i> '+data.message+'</div>';
            btn.innerHTML = '<i class="fa-solid fa-check" style="margin-right: 10px;"></i> BROADCAST COMPLETE';
        } else {
            status.innerHTML = '<div style="padding: 15px; background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.2); color: #ef4444; border-radius: 10px; font-size: 13px; font-weight: 600;"><i class="fa-solid fa-circle-xmark" style="margin-right:8px;"></i> '+data.message+'</div>';
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-paper-plane" style="margin-right: 10px;"></i> RETRY BROADCAST';
        }
    });
});
</script>
