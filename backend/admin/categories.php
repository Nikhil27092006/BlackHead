

<div class="admin-card-premium">
    <h2 class="admin-page-title" style="font-size: 24px; margin-bottom: 30px;">Manage Categories</h2>

    <!-- Add Category Form -->
    <form action="category_handler.php" method="POST" enctype="multipart/form-data" class="grid-2" style="margin-bottom: 40px; background: var(--admin-bg); padding: 30px; border-radius: 15px;">
        <input type="hidden" name="action" value="add">
        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
        <div class="premium-form-group">
            <label>Category Name</label>
            <input type="text" name="name" class="premium-input" required placeholder="e.g. Footwear">
        </div>
        <div class="premium-form-group">
            <label>Parent Category (Optional)</label>
            <select name="parent_id" class="premium-input">
                <option value="">None (Top Level)</option>
                <?php
                $stmt = $pdo->prepare("SELECT * FROM categories WHERE parent_id IS NULL");
                $stmt->execute();
                $topCats = $stmt->fetchAll();
                foreach($topCats as $tc) echo "<option value='" . htmlspecialchars($tc['id']) . "'>" . htmlspecialchars($tc['name']) . "</option>";
                ?>
            </select>
        </div>
        <div class="premium-form-group">
            <label>Category Banner/Image</label>
            <input type="file" name="image" class="premium-input" accept="image/*">
        </div>
        <div class="premium-form-group">
            <label>Description (Used as tag, e.g. "Premium Essentials")</label>
            <input type="text" name="description" class="premium-input" placeholder="Short tagline...">
        </div>
        <button type="submit" class="btn btn-primary" style="width: 200px; border-radius: 10px; grid-column: 1 / -1;">Add Category</button>
    </form>

    <!-- Category List -->
    <div class="table-responsive">
        <table class="modern-table">
            <thead>
                <tr>
                    <th>Visual</th>
                    <th>Name</th>
                    <th>Slug</th>
                    <th>Parent</th>
                    <th>Status</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stmt = $pdo->prepare("SELECT c1.*, c2.name as parent_name FROM categories c1 LEFT JOIN categories c2 ON c1.parent_id = c2.id ORDER BY c1.parent_id, c1.sort_order");
                $stmt->execute();
                $cats = $stmt->fetchAll();
                foreach($cats as $c):
                    $img = $c['image'] ? "../../assets/images/".$c['image'] : "../../assets/images/placeholder.jpg";
                ?>
                <tr>
                    <td style="width: 80px;">
                        <div style="width: 60px; height: 60px; border-radius: 10px; overflow: hidden; background: #111; border: 1px solid rgba(255,255,255,0.05);">
                            <img src="<?php echo $img; ?>" style="width: 100%; height: 100%; object-fit: cover;" alt="">
                        </div>
                    </td>
                    <td><strong style="color: var(--admin-text-main);"><?php echo htmlspecialchars($c['name']); ?></strong></td>
                    <td style="color: var(--admin-text-muted); font-size: 13px;"><?php echo htmlspecialchars($c['slug']); ?></td>
                    <td><span style="font-weight: 600;"><?php echo $c['parent_name'] ? htmlspecialchars($c['parent_name'], ENT_QUOTES, 'UTF-8') : '-'; ?></span></td>
                    <td>
                        <span class="status-badge-modern status-<?php echo $c['status'] == 'active' ? 'paid' : 'cancelled'; ?>">
                            <?php echo $c['status']; ?>
                        </span>
                    </td>
                    <td style="text-align: right;">
                        <div style="display: flex; gap: 8px; justify-content: flex-end;">
                            <a href="index.php?page=edit_category&id=<?php echo $c['id']; ?>" class="action-btn-sleek"><i class="fa-solid fa-pen"></i></a>
                            <form action="category_handler.php" method="POST" onsubmit="return confirm('Delete this category?');" style="display:inline;">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                <input type="hidden" name="id" value="<?php echo $c['id']; ?>">
                                <button type="submit" class="action-btn-sleek action-btn-delete"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
