<?php
$id = (int)$_GET['id'];
$category = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
$category->execute([$id]);
$category = $category->fetch();

if (!$category) {
    header("Location: index.php?page=categories");
    exit;
}

// CSRF token for AJAX operations
$csrfToken = generate_csrf_token();
?>

<div class="admin-card-premium">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h3 class="admin-page-title" style="margin: 0; font-size: 24px;">Edit Category</h3>
        <a href="index.php?page=categories" class="btn btn-outline" style="padding: 10px 20px; font-size: 11px; font-weight: 800; border-radius: 8px;">BACK TO CATEGORIES</a>
    </div>

    <form action="category_handler.php" method="POST" enctype="multipart/form-data" class="grid-2" style="background: var(--admin-bg); padding: 30px; border-radius: 15px;">
        <input type="hidden" name="action" value="edit">
        <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
        <input type="hidden" name="id" value="<?php echo $category['id']; ?>">

        <div class="premium-form-group">
            <label>Category Name</label>
            <input type="text" name="name" class="premium-input" value="<?php echo htmlspecialchars($category['name']); ?>" required>
        </div>
        <div class="premium-form-group">
            <label>Parent Category (Optional)</label>
            <select name="parent_id" class="premium-input">
                <option value="">None (Top Level)</option>
                <?php
                $stmt = $pdo->prepare("SELECT * FROM categories WHERE parent_id IS NULL AND id != ?");
                $stmt->execute([$id]);
                $topCats = $stmt->fetchAll();
                foreach($topCats as $tc) {
                    $selected = $tc['id'] == $category['parent_id'] ? 'selected' : '';
                    echo "<option value='" . htmlspecialchars($tc['id']) . "' " . $selected . ">" . htmlspecialchars($tc['name']) . "</option>";
                }
                ?>
            </select>
        </div>
        <div class="premium-form-group">
            <label>Category Banner/Image</label>
            <?php if($category['image']): ?>
                <div id="categoryImageContainer" style="position: relative; margin-bottom: 15px; width: 140px; height: 90px; border-radius: 12px; overflow: hidden; background: #111; border: 1px solid rgba(255,255,255,0.1); box-shadow: 0 5px 15px rgba(0,0,0,0.2);">
                    <img src="../../assets/images/<?php echo $category['image']; ?>" style="width: 100%; height: 100%; object-fit: cover;">
                    <button type="button" onclick="removeCategoryImage(<?php echo $category['id']; ?>)" style="position: absolute; top: 8px; right: 8px; background: rgba(239, 68, 68, 0.9); color: white; border: none; border-radius: 50%; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 10px; transition: all 0.2s;" onmouseover="this.style.background='#ef4444'" onmouseout="this.style.background='rgba(239, 68, 68, 0.9)'">
                        <i class="fa-solid fa-x"></i>
                    </button>
                </div>
            <?php endif; ?>
            <input type="file" name="image" class="premium-input" accept="image/*">
            <p style="font-size: 11px; color: var(--admin-text-muted); margin-top: 5px;">Upload a high-res JPG/PNG/WebP for the homepage card.</p>
        </div>
        <div class="premium-form-group">
            <label>Description (Tagline)</label>
            <input type="text" name="description" class="premium-input" value="<?php echo htmlspecialchars($category['description']); ?>" placeholder="e.g. Elite Essentials">
        </div>
        <button type="submit" class="btn btn-primary" style="width: 200px; border-radius: 10px; grid-column: 1 / -1;">Update Category</button>
    </form>
</div>

<script>
function removeCategoryImage(categoryId) {
    if (confirm('Permanently delete this category banner?')) {
        fetch('ajax_delete_category_image.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ category_id: categoryId, csrf_token: '<?php echo $csrfToken; ?>' })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const container = document.getElementById('categoryImageContainer');
                container.style.transform = 'scale(0.8)';
                container.style.opacity = '0';
                setTimeout(() => container.remove(), 300);
            } else {
                alert('Error: ' + data.error);
            }
        });
    }
}
</script>
