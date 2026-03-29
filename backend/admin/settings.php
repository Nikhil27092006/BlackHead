<div class="admin-card-premium">
    <h3 class="admin-page-title" style="font-size: 24px; margin-bottom: 30px;">General Settings</h3>

    <form action="settings_handler.php" method="POST">
        <div class="grid-2" style="gap: 40px;">
            <div style="background: var(--admin-bg); padding: 30px; border-radius: 15px;">
                <h4 style="margin-top: 0; margin-bottom: 20px; font-weight: 800; font-size: 16px; color: var(--admin-accent); text-transform: uppercase; letter-spacing: 1px;">Store Information</h4>
                <div class="premium-form-group">
                    <label>Store Name</label>
                    <input type="text" name="store_name" class="premium-input" value="<?php echo htmlspecialchars(getSetting('store_name', 'BLACKHEAD')); ?>">
                </div>
                <div class="premium-form-group">
                    <label>Support Email</label>
                    <input type="email" name="support_email" class="premium-input" value="<?php echo htmlspecialchars(getSetting('support_email', 'support@blackhead.com')); ?>">
                </div>
                <div class="premium-form-group">
                    <label>Contact Phone</label>
                    <input type="text" name="contact_phone" class="premium-input" value="<?php echo htmlspecialchars(getSetting('contact_phone', '+91 9876543210')); ?>">
                </div>
                <div class="premium-form-group">
                    <label>Store Address</label>
                    <textarea name="store_address" class="premium-input" style="height: 100px;"><?php echo htmlspecialchars(getSetting('store_address', 'Sector 14, Gurgaon, Haryana, India')); ?></textarea>
                </div>
            </div>

            <div style="background: var(--admin-bg); padding: 30px; border-radius: 15px;">
                <h4 style="margin-top: 0; margin-bottom: 20px; font-weight: 800; font-size: 16px; color: var(--admin-accent); text-transform: uppercase; letter-spacing: 1px;">Social Presence</h4>
                <div class="premium-form-group">
                    <label>Instagram URL</label>
                    <input type="text" name="instagram" class="premium-input" value="<?php echo htmlspecialchars(getSetting('instagram', '')); ?>" placeholder="https://instagram.com/blackhead">
                </div>
                <div class="premium-form-group">
                    <label>Facebook URL</label>
                    <input type="text" name="facebook" class="premium-input" value="<?php echo htmlspecialchars(getSetting('facebook', '')); ?>" placeholder="https://facebook.com/blackhead">
                </div>
                <div class="premium-form-group">
                    <label>Twitter/X URL</label>
                    <input type="text" name="twitter" class="premium-input" value="<?php echo htmlspecialchars(getSetting('twitter', '')); ?>" placeholder="https://twitter.com/blackhead">
                </div>
            </div>
        </div>

        <div style="margin-top: 40px; border-top: 1px solid var(--admin-border); padding-top: 30px; display: flex; justify-content: flex-end;">
            <button type="submit" class="btn btn-primary" style="padding: 14px 40px; border-radius: 12px; font-weight: 800; letter-spacing: 1px;">SAVE ALL CONFIGURATIONS</button>
        </div>
    </form>
</div>
