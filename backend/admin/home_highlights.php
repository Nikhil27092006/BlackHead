<?php
// Fetch currently featured products (GROUP BY prevents duplicates from multi-image JOIN)
$featuredProds = $pdo->query("
    SELECT p.*, pi.image as main_image
    FROM products p
    LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_main = 1
    WHERE p.is_featured = 1
    GROUP BY p.id
    ORDER BY p.name ASC
")->fetchAll();

// Fetch all active products NOT featured for the "Add" dropdown
$availableProds = $pdo->query("SELECT id, name FROM products WHERE status = 'active' AND is_featured = 0 ORDER BY name ASC")->fetchAll();
?>



<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:35px;">
    <div>
        <h3 style="margin:0; font-size:24px; font-weight:900; letter-spacing:-1px; font-family:'Syne',sans-serif; text-transform:uppercase;">Home Page Controls</h3>
        <p style="margin:5px 0 0; font-size:13px; color:var(--admin-text-muted);">Edit every section of the homepage without touching code.</p>
    </div>
</div>

<form action="home_highlights_handler.php" method="POST" style="display:flex; flex-direction:column; gap:30px; border:none; padding:0; background:transparent;">

    <!-- ═══════════════════════════════════════════ 1. ANNOUNCEMENT BAR -->
    <div class="admin-card-premium" style="padding:30px;">
        <h4 style="margin:0 0 20px; font-weight:800; font-size:14px; color:var(--admin-accent); text-transform:uppercase; letter-spacing:1px;">
            <i class="fa-solid fa-bullhorn" style="margin-right:8px;"></i>Announcement Bar
        </h4>
        <div class="premium-form-group" style="margin:0;">
            <label>Bar Text</label>
            <input type="text" name="offer_bar_text" class="premium-input"
                   value="<?php echo htmlspecialchars(getSetting('offer_bar_text', 'Join the Blackhead Club & get 20% off your first order | Free shipping over ₹999')); ?>" required>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════ 2. HERO SECTION -->
    <div class="admin-card-premium" style="padding:30px;">
        <h4 style="margin:0 0 6px; font-weight:800; font-size:14px; color:var(--admin-accent); text-transform:uppercase; letter-spacing:1px;">
            <i class="fa-solid fa-star" style="margin-right:8px;"></i>Hero Section
        </h4>
        <p style="margin:0 0 24px; font-size:12px; color:var(--admin-text-muted);">The big full-screen section at the top of the homepage.</p>
        <div class="grid-2" style="gap:20px;">
            <div class="premium-form-group">
                <label>Eyebrow Label <span style="color:var(--admin-text-muted); font-size:10px;">(small text above title)</span></label>
                <input type="text" name="hero_eyebrow" class="premium-input"
                       value="<?php echo htmlspecialchars(getSetting('hero_eyebrow', 'SS 2025 — New Collection Dropped')); ?>">
            </div>
            <div></div>
            <div class="premium-form-group">
                <label>Title Line 1</label>
                <input type="text" name="hero_title_1" class="premium-input"
                       value="<?php echo htmlspecialchars(getSetting('hero_title_1', 'ENGINEERED')); ?>" required>
            </div>
            <div class="premium-form-group">
                <label>Title Line 2</label>
                <input type="text" name="hero_title_2" class="premium-input"
                       value="<?php echo htmlspecialchars(getSetting('hero_title_2', 'EXCELLENCE')); ?>" required>
            </div>
            <div class="premium-form-group" style="grid-column:1/-1;">
                <label>Subtitle / Tagline</label>
                <input type="text" name="hero_subtitle" class="premium-input"
                       value="<?php echo htmlspecialchars(getSetting('hero_subtitle', 'Experience the intersection of luxury and street culture. Our new collection defines the future of premium apparel.')); ?>">
            </div>
            <div class="premium-form-group">
                <label>CTA Button 1 Text</label>
                <input type="text" name="hero_cta1_text" class="premium-input"
                       value="<?php echo htmlspecialchars(getSetting('hero_cta1_text', 'Explore Collection')); ?>">
            </div>
            <div class="premium-form-group">
                <label>CTA Button 2 Text</label>
                <input type="text" name="hero_cta2_text" class="premium-input"
                       value="<?php echo htmlspecialchars(getSetting('hero_cta2_text', 'Our Story')); ?>">
            </div>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════ 3. MARQUEE TICKER -->
    <div class="admin-card-premium" style="padding:30px;">
        <h4 style="margin:0 0 6px; font-weight:800; font-size:14px; color:var(--admin-accent); text-transform:uppercase; letter-spacing:1px;">
            <i class="fa-solid fa-infinity" style="margin-right:8px;"></i>Scrolling Marquee Ticker
        </h4>
        <p style="margin:0 0 24px; font-size:12px; color:var(--admin-text-muted);">Purple scrolling banner below the hero. Separate each item with a comma.</p>
        <div class="premium-form-group" style="margin:0;">
            <label>Marquee Items <span style="color:var(--admin-text-muted); font-size:10px;">(comma-separated)</span></label>
            <textarea name="marquee_items" class="premium-input" rows="3"><?php echo htmlspecialchars(getSetting('marquee_items', 'Free Shipping Over ₹999,New Drop: SS 2025,Premium Streetwear,Engineered Excellence,Limited Editions,Youth Culture,Elite Athleisure,100% Authentic')); ?></textarea>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════ 4. STATS BAND -->
    <div class="admin-card-premium" style="padding:30px;">
        <h4 style="margin:0 0 6px; font-weight:800; font-size:14px; color:var(--admin-accent); text-transform:uppercase; letter-spacing:1px;">
            <i class="fa-solid fa-chart-bar" style="margin-right:8px;"></i>Stats Band
        </h4>
        <p style="margin:0 0 24px; font-size:12px; color:var(--admin-text-muted);">The four counters strip (12000+, 500+, etc.). Number &amp; Suffix animate on scroll.</p>
        <div class="grid-2" style="gap:20px;">
            <?php
            $defaults = [
                1 => ['12000', '+', 'Happy Customers'],
                2 => ['500',   '+', 'Products Available'],
                3 => ['98',    '%', 'Satisfaction Rate'],
                4 => ['4',     '+', 'Years Of Excellence'],
            ];
            foreach ($defaults as $i => $d): ?>
            <div style="background:var(--admin-bg); padding:20px; border-radius:14px; border:1px solid var(--admin-border);">
                <p style="margin:0 0 14px; font-size:10px; font-weight:800; color:var(--admin-accent); text-transform:uppercase; letter-spacing:1px;">Stat <?php echo $i; ?></p>
                <div style="display:grid; grid-template-columns:1fr 80px; gap:10px; margin-bottom:10px;">
                    <div class="premium-form-group" style="margin:0;">
                        <label style="font-size:10px;">Number</label>
                        <input type="text" name="stat<?php echo $i; ?>_number" class="premium-input"
                               value="<?php echo htmlspecialchars(getSetting('stat'.$i.'_number', $d[0])); ?>"
                               style="padding:10px; font-weight:800; font-size:16px; color:var(--admin-accent);">
                    </div>
                    <div class="premium-form-group" style="margin:0;">
                        <label style="font-size:10px;">Suffix</label>
                        <input type="text" name="stat<?php echo $i; ?>_suffix" class="premium-input"
                               value="<?php echo htmlspecialchars(getSetting('stat'.$i.'_suffix', $d[1])); ?>"
                               style="padding:10px; text-align:center;">
                    </div>
                </div>
                <div class="premium-form-group" style="margin:0;">
                    <label style="font-size:10px;">Label</label>
                    <input type="text" name="stat<?php echo $i; ?>_label" class="premium-input"
                           value="<?php echo htmlspecialchars(getSetting('stat'.$i.'_label', $d[2])); ?>"
                           style="padding:10px;">
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════ 5. CATEGORIES SECTION -->
    <div class="admin-card-premium" style="padding:30px;">
        <h4 style="margin:0 0 6px; font-weight:800; font-size:14px; color:var(--admin-accent); text-transform:uppercase; letter-spacing:1px;">
            <i class="fa-solid fa-layer-group" style="margin-right:8px;"></i>Categories Section
        </h4>
        <p style="margin:0 0 24px; font-size:12px; color:var(--admin-text-muted);">"Find Your Style" section heading text.</p>
        <div class="grid-2" style="gap:20px;">
            <div class="premium-form-group">
                <label>Title Line 1</label>
                <input type="text" name="categories_title_1" class="premium-input"
                       value="<?php echo htmlspecialchars(getSetting('categories_title_1', 'FIND YOUR')); ?>" required>
            </div>
            <div class="premium-form-group">
                <label>Title Line 2 <span style="color:var(--admin-text-muted); font-size:10px;">(displays italic)</span></label>
                <input type="text" name="categories_title_2" class="premium-input"
                       value="<?php echo htmlspecialchars(getSetting('categories_title_2', 'STYLE')); ?>" required>
            </div>
            <div class="premium-form-group">
                <label>Section Eyebrow</label>
                <input type="text" name="categories_subtitle" class="premium-input"
                       value="<?php echo htmlspecialchars(getSetting('categories_subtitle', 'Shop By Category')); ?>">
            </div>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════ 6. TRENDING SECTION HEADER -->
    <div class="admin-card-premium" style="padding:30px;">
        <h4 style="margin:0 0 6px; font-weight:800; font-size:14px; color:var(--admin-accent); text-transform:uppercase; letter-spacing:1px;">
            <i class="fa-solid fa-fire" style="margin-right:8px;"></i>Trending Products Section
        </h4>
        <p style="margin:0 0 24px; font-size:12px; color:var(--admin-text-muted);">"Currently Hot" section heading text.</p>
        <div class="grid-2" style="gap:20px;">
            <div class="premium-form-group">
                <label>Title Line 1</label>
                <input type="text" name="trending_title_1" class="premium-input"
                       value="<?php echo htmlspecialchars(getSetting('trending_title_1', 'CURRENTLY')); ?>" required>
            </div>
            <div class="premium-form-group">
                <label>Title Line 2 <span style="color:var(--admin-text-muted); font-size:10px;">(displays italic)</span></label>
                <input type="text" name="trending_title_2" class="premium-input"
                       value="<?php echo htmlspecialchars(getSetting('trending_title_2', 'HOT')); ?>" required>
            </div>
            <div class="premium-form-group">
                <label>Section Eyebrow</label>
                <input type="text" name="trending_subtitle" class="premium-input"
                       value="<?php echo htmlspecialchars(getSetting('trending_subtitle', 'Top Trends')); ?>">
            </div>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════ 7. CURRENTLY HOT PRODUCT MANAGER -->
    <div id="hot-manager" class="admin-card-premium" style="padding:30px; border: 1px solid rgba(139,92,246,0.25);">
        <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:25px;">
            <div>
                <h4 style="margin:0; font-weight:800; font-size:14px; color:var(--admin-accent); text-transform:uppercase; letter-spacing:1px;">
                    <i class="fa-solid fa-bolt" style="margin-right:8px;"></i>Currently Hot Product Manager
                </h4>
                <p style="margin:5px 0 0; font-size:12px; color:var(--admin-text-muted);">Manage high-visibility items and their unique lab descriptions.</p>
            </div>
        </div>

        <!-- Add Feature -->
        <div style="background: rgba(139,92,246,0.03); border:1px dashed rgba(139,92,246,0.2); padding:20px; border-radius:15px; margin-bottom:30px;">
            <label style="font-size:11px; font-weight:800; color:var(--admin-accent); text-transform:uppercase; margin-bottom:12px; display:block;">Add Product to Hot Section</label>
            <div style="display:flex; gap:12px;">
                <select name="new_hot_product" class="premium-input" style="flex:1;">
                    <option value="">-- Select Product to Feature --</option>
                    <?php foreach($availableProds as $ap): ?>
                        <option value="<?php echo $ap['id']; ?>"><?php echo htmlspecialchars($ap['name']); ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" name="action_type" value="add_hot" class="btn btn-primary" style="padding:0 25px; font-size:11px;">ADD ITEM</button>
            </div>
        </div>

        <!-- Featured List -->
        <div class="table-responsive">
            <table class="modern-table" style="font-size:13px;">
                <thead>
                    <tr>
                        <th style="width:60px;">Pic</th>
                        <th>Product Info</th>
                        <th>Lab DNA Labels (Tech & Fit)</th>
                        <th style="text-align:right;">Remove</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($featuredProds)): ?>
                        <tr><td colspan="4" style="text-align:center; padding:40px; color:var(--admin-text-muted);">No products currently featured in hot section.</td></tr>
                    <?php endif; ?>
                    <?php foreach($featuredProds as $fp): ?>
                    <tr>
                        <td>
                            <img src="../../assets/images/<?php echo $fp['main_image'] ?: 'placeholder.jpg'; ?>" 
                                 style="width:48px; height:48px; border-radius:8px; object-fit:cover; border:1px solid var(--admin-border);">
                        </td>
                        <td>
                            <div style="font-weight:800; color:#fff;"><?php echo htmlspecialchars($fp['name']); ?></div>
                            <div style="font-size:10px; color:var(--admin-text-muted);">SKU: <?php echo $fp['sku']; ?></div>
                        </td>
                        <td>
                            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:15px;">
                                <div class="premium-form-group" style="margin:0;">
                                    <label style="font-size:9px; color: var(--admin-accent);">TECH SPEC</label>
                                    <input type="text" name="dna_tech[<?php echo $fp['id']; ?>]" class="premium-input" 
                                           value="<?php echo htmlspecialchars($fp['dna_tech']); ?>" style="padding:8px; font-size:11px;">
                                </div>
                                <div class="premium-form-group" style="margin:0;">
                                    <label style="font-size:9px; color: var(--admin-accent);">FIT SPEC</label>
                                    <input type="text" name="dna_fit[<?php echo $fp['id']; ?>]" class="premium-input" 
                                           value="<?php echo htmlspecialchars($fp['dna_fit']); ?>" style="padding:8px; font-size:11px;">
                                </div>
                            </div>
                        </td>
                        <td style="text-align:right; white-space:nowrap;">
                            <label class="toggle-switch toggle-danger">
                                <input type="checkbox" name="remove_hot[]" value="<?php echo $fp['id']; ?>">
                                <span class="toggle-slider"></span>
                            </label>
                            <div style="font-size:9px; color:var(--admin-text-muted); margin-top:5px; font-weight:700;">CHECK TO REMOVE</div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <!-- ═══════════════════════════════════════════ 8. CUSTOM COLLECTIONS BUILDER -->
    <div id="custom-collections" class="admin-card-premium" style="padding:30px; border: 1px solid rgba(139,92,246,0.25); margin-top:30px;">
        <h4 style="margin:0 0 6px; font-weight:800; font-size:14px; color:var(--admin-accent); text-transform:uppercase; letter-spacing:1px;">
            <i class="fa-solid fa-puzzle-piece" style="margin-right:8px;"></i>Dynamic Collection Sections
        </h4>
        <p style="margin:0 0 24px; font-size:12px; color:var(--admin-text-muted);">Create entirely new rows of products on your homepage with custom titles.</p>

        <!-- Create New Section -->
        <div style="background: rgba(139,92,246,0.03); border:1px dashed rgba(139,92,246,0.2); padding:25px; border-radius:15px; margin-bottom:30px;">
            <label style="font-size:11px; font-weight:800; color:var(--admin-accent); text-transform:uppercase; margin-bottom:12px; display:block;">Create a New Collection Row</label>
            <div style="display:grid; grid-template-columns: 1fr 1fr 1fr auto; gap:12px;">
                <input type="text" name="new_sec_eyebrow" class="premium-input" placeholder="Eyebrow (e.g. LIMITED EDITION)">
                <input type="text" name="new_sec_title1" class="premium-input" placeholder="Title Line 1">
                <input type="text" name="new_sec_title2" class="premium-input" placeholder="Title Line 2 (Italic)">
                <button type="submit" name="action_type" value="create_section" class="btn btn-primary" style="padding:0 25px; font-size:11px;">CREATE SECTION</button>
            </div>
        </div>

        <?php
        // Fetch existing custom sections
        $customSections = [];
        try {
            $customSections = $pdo->query("SELECT * FROM homepage_custom_sections ORDER BY sort_order ASC")->fetchAll();
        } catch(Exception $e) { /* Table might not exist yet */ }

        foreach($customSections as $section): 
            $secId = $section['id'];
            // Fetch products for this section
            $secProdsStmt = $pdo->prepare("
                SELECT p.*, pi.image as main_image 
                FROM products p 
                JOIN homepage_custom_section_products hcp ON p.id = hcp.product_id
                LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_main = 1
                WHERE hcp.section_id = ?
                ORDER BY hcp.sort_order ASC
            ");
            $secProdsStmt->execute([$secId]);
            $secProds = $secProdsStmt->fetchAll();
        ?>
        <div style="background: rgba(255,255,255,0.02); border: 1px solid var(--admin-border); border-radius:20px; padding:25px; margin-bottom:25px; position:relative;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                <div>
                    <span style="font-size:10px; font-weight:800; color:var(--admin-accent); text-transform:uppercase;"><?php echo htmlspecialchars($section['eyebrow']); ?></span>
                    <h3 style="margin:5px 0 0; font-size:18px; font-weight:900; color:#fff;"><?php echo htmlspecialchars($section['title_1']); ?> <em><?php echo htmlspecialchars($section['title_2']); ?></em></h3>
                </div>
                <button type="submit" name="delete_section" value="<?php echo $secId; ?>" class="btn btn-danger" style="background:rgba(239,68,68,0.1); color:#ef4444; border:1px solid rgba(239,68,68,0.2); padding:8px 15px; font-size:10px;" onclick="return confirm('Delete this entire section?')">DELETE SECTION</button>
            </div>

            <!-- Add Product to this section -->
            <div style="display:flex; gap:10px; margin-bottom:20px; padding-bottom:20px; border-bottom: 1px solid rgba(255,255,255,0.05);">
                <select name="add_to_sec[<?php echo $secId; ?>]" class="premium-input" style="flex:1; font-size:12px;">
                    <option value="">-- Add Product to this Collection --</option>
                    <?php foreach($availableProds as $ap): ?>
                        <option value="<?php echo $ap['id']; ?>"><?php echo htmlspecialchars($ap['name']); ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" name="action_type" value="add_to_section" class="btn btn-primary" style="padding:0 20px; font-size:10px;">ADD</button>
            </div>

            <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); gap:15px;">
                <?php foreach($secProds as $sp): ?>
                <div style="position:relative; group">
                    <img src="../../assets/images/<?php echo $sp['main_image'] ?: 'placeholder.jpg'; ?>" style="width:100%; aspect-ratio:1; object-fit:cover; border-radius:12px; border:1px solid var(--admin-border);">
                    <button type="submit" name="remove_from_sec" value="<?php echo $secId.'-'.$sp['id']; ?>" style="position:absolute; top:-5px; right:-5px; background:#ef4444; color:#fff; border:none; width:22px; height:22px; border-radius:50%; font-size:10px; cursor:pointer;"><i class="fa-solid fa-xmark"></i></button>
                    <div style="font-size:9px; color:rgba(255,255,255,0.5); margin-top:5px; text-align:center; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;"><?php echo htmlspecialchars($sp['name']); ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Save -->
    <div style="display:flex; justify-content:flex-end; padding-bottom:40px; position:sticky; bottom:20px; z-index:100;">
        <button type="submit" class="btn btn-primary" style="padding:18px 60px; border-radius:14px; font-weight:800; letter-spacing:1.5px; font-size:14px; box-shadow: 0 10px 30px rgba(139,92,246,0.35);">
            <i class="fa-solid fa-floppy-disk" style="margin-right:10px;"></i>SAVE ALL HOMEPAGE UPDATES
        </button>
    </div>

</form>
