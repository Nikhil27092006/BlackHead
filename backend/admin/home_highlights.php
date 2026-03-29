<div class="admin-card-premium">
    <h3 class="admin-page-title" style="font-size: 24px; margin-bottom: 30px;">Home Page Highlights</h3>

    <form action="home_highlights_handler.php" method="POST">
        <div class="grid-2" style="gap: 40px;">
            <div style="background: var(--admin-bg); padding: 30px; border-radius: 15px; grid-column: 1 / -1;">
                <h4 style="margin-top: 0; margin-bottom: 20px; font-weight: 800; font-size: 16px; color: var(--admin-accent); text-transform: uppercase; letter-spacing: 1px;">Top Announcement Bar</h4>
                <div class="premium-form-group">
                    <label>Announcement Text</label>
                    <input type="text" name="offer_bar_text" class="premium-input" value="<?php echo htmlspecialchars(getSetting('offer_bar_text', 'Join the Blackhead Club & get 20% off your first order | Free shipping over ₹999')); ?>" required>
                </div>
            </div>
            
            <div style="background: var(--admin-bg); padding: 30px; border-radius: 15px;">
                <h4 style="margin-top: 0; margin-bottom: 20px; font-weight: 800; font-size: 16px; color: var(--admin-accent); text-transform: uppercase; letter-spacing: 1px;">Categories Section</h4>
                <div class="premium-form-group">
                    <label>Title Line 1</label>
                    <input type="text" name="categories_title_1" class="premium-input" value="<?php echo htmlspecialchars(getSetting('categories_title_1', 'FIND YOUR')); ?>" required>
                </div>
                <div class="premium-form-group">
                    <label>Title Line 2 (Italic)</label>
                    <input type="text" name="categories_title_2" class="premium-input" value="<?php echo htmlspecialchars(getSetting('categories_title_2', 'STYLE')); ?>" required>
                </div>
                <div class="premium-form-group">
                    <label>Section Subtitle</label>
                    <input type="text" name="categories_subtitle" class="premium-input" value="<?php echo htmlspecialchars(getSetting('categories_subtitle', 'Shop By Category')); ?>">
                </div>
            </div>

            <div style="background: var(--admin-bg); padding: 30px; border-radius: 15px;">
                <h4 style="margin-top: 0; margin-bottom: 20px; font-weight: 800; font-size: 16px; color: var(--admin-accent); text-transform: uppercase; letter-spacing: 1px;">Trending Products Section</h4>
                <div class="premium-form-group">
                    <label>Title Line 1</label>
                    <input type="text" name="trending_title_1" class="premium-input" value="<?php echo htmlspecialchars(getSetting('trending_title_1', 'CURRENTLY')); ?>" required>
                </div>
                <div class="premium-form-group">
                    <label>Title Line 2 (Italic)</label>
                    <input type="text" name="trending_title_2" class="premium-input" value="<?php echo htmlspecialchars(getSetting('trending_title_2', 'HOT')); ?>" required>
                </div>
                <div class="premium-form-group">
                    <label>Section Subtitle</label>
                    <input type="text" name="trending_subtitle" class="premium-input" value="<?php echo htmlspecialchars(getSetting('trending_subtitle', 'Top Trends')); ?>">
                </div>
            </div>
        </div>

        <div style="margin-top: 40px; border-top: 1px solid var(--admin-border); padding-top: 30px; display: flex; justify-content: flex-end;">
            <button type="submit" class="btn btn-primary" style="padding: 14px 40px; border-radius: 12px; font-weight: 800; letter-spacing: 1px;">SAVE HIGHLIGHTS</button>
        </div>
    </form>
</div>
