<?php
// security_settings.php
$admin_id = $_SESSION['admin_id'];
$stmt = $pdo->prepare("SELECT * FROM admins WHERE id = ?");
$stmt->execute([$admin_id]);
$admin = $stmt->fetch();

if (!$admin) {
    echo "Admin not found.";
    exit;
}
?>

<div class="admin-card-premium">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 35px;">
        <div>
            <h3 style="margin: 0; font-size: 24px; font-weight: 900; letter-spacing: -1px; font-family: 'Syne', sans-serif; text-transform: uppercase;">Security Settings</h3>
            <p style="margin: 5px 0 0 0; font-size: 13px; color: var(--admin-text-muted);">Manage your administrator credentials and login security</p>
        </div>
    </div>

    <div class="grid-2">
        <!-- Profile Details -->
        <div class="admin-card-sleek" style="background: rgba(255,255,255,0.02); padding: 30px; border-radius: 20px; border: 1px solid rgba(255,255,255,0.05);">
            <h4 style="margin: 0 0 25px 0; font-size: 16px; font-weight: 800; color: #fff;">Account Profile</h4>
            <form action="security_handler.php" method="POST">
                <input type="hidden" name="action" value="update_profile">
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                
                <div class="premium-form-group">
                    <label>DISPLAY NAME</label>
                    <input type="text" name="name" class="premium-input" value="<?php echo htmlspecialchars($admin['name']); ?>" required>
                </div>
                
                <div class="premium-form-group">
                    <label>EMAIL ADDRESS (LOGIN ID)</label>
                    <input type="email" name="email" class="premium-input" value="<?php echo htmlspecialchars($admin['email']); ?>" required>
                </div>

                <div class="premium-form-group">
                    <label>CURRENT PASSWORD (To verify changes)</label>
                    <input type="password" name="current_password" class="premium-input" required>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; border-radius: 12px; height: 50px; font-weight: 800;">SAVE PROFILE CHANGES</button>
            </form>
        </div>

        <!-- Password Change -->
        <div class="admin-card-sleek" style="background: rgba(255,255,255,0.02); padding: 30px; border-radius: 20px; border: 1px solid rgba(255,255,255,0.05);">
            <h4 style="margin: 0 0 25px 0; font-size: 16px; font-weight: 800; color: #fff;">Change Password</h4>
            <form action="security_handler.php" method="POST">
                <input type="hidden" name="action" value="update_password">
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

                <div class="premium-form-group">
                    <label>NEW PASSWORD</label>
                    <input type="password" name="new_password" class="premium-input" required minlength="6">
                </div>

                <div class="premium-form-group">
                    <label>CONFIRM NEW PASSWORD</label>
                    <input type="password" name="confirm_password" class="premium-input" required minlength="6">
                </div>

                <div class="premium-form-group">
                    <label>CURRENT PASSWORD (To verify changes)</label>
                    <input type="password" name="current_password" class="premium-input" required>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; border-radius: 12px; height: 50px; font-weight: 800; background: #8b5cf6;">UPDATE PASSWORD</button>
            </form>
        </div>
    </div>

    <div style="margin-top: 40px; padding: 25px; background: rgba(139,92,246,0.05); border: 1px solid rgba(139,92,246,0.1); border-radius: 20px;">
        <h4 style="margin: 0 0 10px 0; font-size: 14px; color: #a78bfa; display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-shield-halved"></i> Security Recommendation
        </h4>
        <p style="margin: 0; font-size: 13px; color: rgba(255,255,255,0.5); line-height: 1.6;">Always use a strong, unique password for your administrator account. Changing your login email or password periodically helps prevent unauthorized access to the BlackHead Laboratory core systems.</p>
    </div>
</div>
