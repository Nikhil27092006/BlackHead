<?php
$id = (int)$_GET['id'];
$category = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
$category->execute([$id]);
$category = $category->fetch();

if (!$category) {
    header("Location: index.php?page=categories");
    exit;
}
?>

<div class="admin-card-premium">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h3 class="admin-page-title" style="margin: 0; font-size: 24px;">Edit Category</h3>
        <a href="index.php?page=categories" class="btn btn-outline" style="padding: 10px 20px; font-size: 11px; font-weight: 800; border-radius: 8px;">BACK TO CATEGORIES</a>
    </div>

    <form action="category_handler.php" method="POST" class="grid-2" style="background: var(--admin-bg); padding: 30px; border-radius: 15px;">
        <input type="hidden" name="action" value="edit">
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
                $topCats = $pdo->query("SELECT * FROM categories WHERE parent_id IS NULL AND id != {$category['id']}")->fetchAll();
                foreach($topCats as $tc) {
                    $selected = $tc['id'] == $category['parent_id'] ? 'selected' : '';
                    echo "<option value='{$tc['id']}' {$selected}>{$tc['name']}</option>";
                }
                ?>
            </select>
        </div>
        <div class="premium-form-group" style="grid-column: 1 / -1;">
            <label>Description</label>
            <textarea name="description" class="premium-input" rows="2"><?php echo htmlspecialchars($category['description']); ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary" style="width: 200px; border-radius: 10px;">Update Category</button>
    </form>
</div>
