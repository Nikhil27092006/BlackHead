<?php
$id = (int)$_GET['id'];
$product = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$product->execute([$id]);
$product = $product->fetch();

if (!$product) {
    header("Location: index.php?page=products");
    exit;
}

// Get existing variants
$variants = $pdo->prepare("SELECT * FROM product_variants WHERE product_id = ?");
$variants->execute([$id]);
$variants = $variants->fetchAll();

// Get existing images
$images = $pdo->prepare("SELECT * FROM product_images WHERE product_id = ? ORDER BY is_main DESC");
$images->execute([$id]);
$images = $images->fetchAll();

// CSRF token for AJAX operations
$csrfToken = generate_csrf_token();
?>

<div class="admin-card-premium">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h3 class="admin-page-title" style="margin: 0; font-size: 24px;">Edit Product</h3>
        <a href="index.php?page=products" class="btn btn-outline" style="padding: 10px 20px; font-size: 11px; font-weight: 800; border-radius: 8px;">BACK TO CATALOG</a>
    </div>

    <form action="product_handler.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action" value="edit">
        <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
        <input type="hidden" name="id" value="<?php echo $product['id']; ?>">

        <div class="grid-2-1" style="gap: 40px;">
            <!-- Main Details -->
            <div style="background: var(--admin-bg); padding: 35px; border-radius: 20px; border: 1px solid var(--admin-border);">
                <h4 style="margin-top: 0; margin-bottom: 25px; font-weight: 800; font-size: 15px; color: var(--admin-accent); text-transform: uppercase; letter-spacing: 1px;">Product Basics</h4>

                <div class="premium-form-group">
                    <label>Product Name</label>
                    <input type="text" name="name" class="premium-input" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                </div>
                <div class="premium-form-group">
                    <label>Description</label>
                    <textarea name="description" class="premium-input" rows="8"><?php echo htmlspecialchars($product['description']); ?></textarea>
                </div>

                <div class="grid-2" style="gap: 20px; margin-top: 10px;">
                    <div class="premium-form-group">
                        <label>Base Price (₹)</label>
                        <input type="number" name="price" class="premium-input" value="<?php echo $product['price']; ?>" required step="0.01" style="font-weight: 800; font-size: 18px; color: var(--admin-accent);">
                    </div>
                    <div class="premium-form-group">
                        <label>Discount Price (₹)</label>
                        <input type="number" name="discount_price" class="premium-input" value="<?php echo $product['discount_price']; ?>" step="0.01" style="font-weight: 700;">
                    </div>
                </div>

                <!-- Inventory & Variants -->
                <div style="margin-top: 40px; border-top: 1px solid var(--admin-border); padding-top: 30px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; flex-wrap: wrap; gap: 15px;">
                        <h4 style="margin: 0; font-weight: 800; font-size: 15px; color: var(--admin-text-main); text-transform: uppercase; letter-spacing: 1px;">Inventory & Variants</h4>
                        <div style="display: flex; gap: 10px;">
                            <button type="button" class="btn btn-outline" onclick="toggleBulkTool()" style="padding: 8px 16px; font-size: 10px; border-radius: 8px; border-color: var(--admin-accent); color: var(--admin-accent);">⚡ Bulk Generator</button>
                            <button type="button" class="btn btn-primary" onclick="addVariant()" style="padding: 8px 16px; font-size: 10px; border-radius: 8px;">+ Add Variant</button>
                        </div>
                    </div>

                    <!-- Bulk Variant Tool (Hidden by default) -->
                    <div id="bulk-variant-tool" style="display: none; background: rgba(139, 92, 246, 0.05); border: 1px dashed var(--admin-accent); padding: 25px; border-radius: 15px; margin-bottom: 25px; animation: slideDown 0.3s ease;">
                        <h5 style="margin: 0 0 15px 0; font-size: 12px; font-weight: 800; text-transform: uppercase; color: var(--admin-accent);">⚡ Intelligent Bulk Generator</h5>
                        <div class="grid-2" style="gap: 15px;">
                            <div class="premium-form-group" style="margin-bottom: 0;">
                                <label style="font-size: 10px;">Sizes (Comma separated)</label>
                                <input type="text" id="bulk-sizes" class="premium-input" placeholder="e.g. S, M, L, XL" style="padding: 10px; background: white;">
                            </div>
                            <div class="premium-form-group" style="margin-bottom: 0;">
                                <label style="font-size: 10px;">Colors (Comma separated)</label>
                                <input type="text" id="bulk-colors" class="premium-input" placeholder="e.g. Black, White, Navy" style="padding: 10px; background: white;">
                            </div>
                        </div>
                        <div style="margin-top: 15px; display: flex; justify-content: flex-end; gap: 10px;">
                            <button type="button" onclick="toggleBulkTool()" class="btn" style="padding: 8px 15px; font-size: 10px; background: #eee; color: #666;">Cancel</button>
                            <button type="button" onclick="generateBulkVariants()" class="btn btn-primary" style="padding: 8px 20px; font-size: 10px;">Generate All Combinations</button>
                        </div>
                    </div>

                    <div id="variants-container">
                        <?php foreach($variants as $index => $variant): ?>
                        <div class="variant-row" style="display: grid; grid-template-columns: 2fr 2fr 3fr 2fr 50px; gap: 12px; align-items: flex-end; background: white; padding: 20px; border-radius: 12px; border: 1px solid var(--admin-border); margin-bottom: 12px;">
                            <div class="premium-form-group" style="margin-bottom: 0;">
                                <label style="font-size: 10px; font-weight: 800;">Size</label>
                                <input type="text" name="variants[<?php echo $index; ?>][size]" class="premium-input" value="<?php echo htmlspecialchars($variant['size']); ?>" style="padding: 10px;">
                            </div>
                            <div class="premium-form-group" style="margin-bottom: 0;">
                                <label style="font-size: 10px; font-weight: 800;">Color</label>
                                <input type="text" name="variants[<?php echo $index; ?>][color]" class="premium-input" value="<?php echo htmlspecialchars($variant['color']); ?>" style="padding: 10px;">
                            </div>
                            <div class="premium-form-group" style="margin-bottom: 0;">
                                <label style="font-size: 10px; font-weight: 800;">SKU</label>
                                <input type="text" name="variants[<?php echo $index; ?>][sku]" class="premium-input" value="<?php echo htmlspecialchars($variant['sku']); ?>" style="padding: 10px;">
                            </div>
                            <div class="premium-form-group" style="margin-bottom: 0;">
                                <label style="font-size: 10px; font-weight: 800;">Stock</label>
                                <input type="number" name="variants[<?php echo $index; ?>][stock]" class="premium-input" value="<?php echo $variant['stock_quantity'] ?? $variant['stock'] ?? 0; ?>" style="padding: 10px; font-weight: 800;">
                            </div>
                            <button type="button" class="action-btn-sleek action-btn-delete" onclick="removeVariant(this)" style="height: 42px; display: flex; align-items: center; justify-content: center;"><i class="fa-solid fa-trash"></i></button>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Organization & Media -->
            <div style="display: flex; flex-direction: column; gap: 25px;">
                <div style="background: white; padding: 30px; border-radius: 18px; border: 1px solid var(--admin-border); box-shadow: var(--shadow-sm);">
                    <h4 style="margin-top: 0; margin-bottom: 20px; font-weight: 800; font-size: 14px; text-transform: uppercase;">Organization</h4>
                    <div class="premium-form-group">
                        <label>Category</label>
                        <select name="category_id" id="category_select" class="premium-input" required onchange="loadSubcategories(this.value)">
                            <option value="">Select Category</option>
                            <?php
                            $cats = $pdo->query("SELECT * FROM categories WHERE parent_id IS NULL ORDER BY name")->fetchAll();
                            foreach($cats as $cat) {
                                $selected = $cat['id'] == $product['category_id'] ? 'selected' : '';
                                echo "<option value='{$cat['id']}' {$selected}>" . htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8') . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <?php
                    // Pre-fetch subcategories if category is already selected
                    $subcats = [];
                    if ($product['category_id']) {
                        $subStmt = $pdo->prepare("SELECT * FROM categories WHERE parent_id = ? AND status = 'active' ORDER BY name");
                        $subStmt->execute([$product['category_id']]);
                        $subcats = $subStmt->fetchAll();
                    }
                    ?>
                    <div class="premium-form-group" id="subcategory_wrapper" style="<?php echo empty($subcats) ? 'display:none;' : ''; ?>">
                        <label>Subcategory <span style="color:#8b5cf6; font-size:11px;">(Optional)</span></label>
                        <select name="subcategory_id" id="subcategory_select" class="premium-input">
                            <option value="">-- Select Subcategory --</option>
                            <?php foreach($subcats as $sc): ?>
                                <option value="<?php echo $sc['id']; ?>" <?php echo ($sc['id'] == $product['subcategory_id']) ? 'selected' : ''; ?>>
                                    <?php echo $sc['name']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="premium-form-group">
                        <label>Brand</label>
                        <input type="text" name="brand" class="premium-input" value="<?php echo htmlspecialchars($product['brand']); ?>">
                    </div>
                    <div class="premium-form-group">
                        <label>Master SKU</label>
                        <input type="text" name="sku" class="premium-input" value="<?php echo htmlspecialchars($product['sku']); ?>">
                    </div>
                </div>

                <div style="background: white; padding: 30px; border-radius: 18px; border: 1px solid var(--admin-border); box-shadow: var(--shadow-sm);">
                    <h4 style="margin-top: 0; margin-bottom: 20px; font-weight: 800; font-size: 14px; text-transform: uppercase;">Media Assets</h4>

                    <!-- Existing Images -->
                    <?php if(!empty($images)): ?>
                    <div style="margin-bottom: 20px;">
                        <label style="font-size: 12px; font-weight: 800; color: var(--admin-text-main); text-transform: uppercase;">Current Images</label>
                        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-top: 10px;">
                            <?php foreach($images as $img): ?>
                            <div style="position: relative; border-radius: 8px; overflow: hidden; border: 1px solid var(--admin-border);">
                                <img src="../../assets/images/<?php echo $img['image'] ?? $img['image_path'] ?? 'placeholder.jpg'; ?>" style="width: 100%; height: 80px; object-fit: cover;">
                                <button type="button" onclick="removeImage(<?php echo $img['id']; ?>)" style="position: absolute; top: 5px; right: 5px; background: rgba(239,68,68,0.9); color: white; border: none; border-radius: 50%; width: 20px; height: 20px; font-size: 10px; cursor: pointer;"><i class="fa-solid fa-x"></i></button>
                                <?php if($img['is_main']): ?>
                                <span style="position: absolute; bottom: 5px; left: 5px; background: var(--admin-accent); color: white; font-size: 8px; padding: 2px 6px; border-radius: 4px; text-transform: uppercase; font-weight: 800;">Main</span>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="premium-form-group">
                        <label>Add New Images</label>
                        <div id="dropzone" style="border: 2px dashed #e2e8f0; padding: 30px; border-radius: 15px; text-align: center; cursor: pointer; transition: all 0.3s ease; position: relative;" onmouseover="this.style.borderColor='var(--admin-accent)'" onmouseout="this.style.borderColor='#e2e8f0'">
                            <i class="fa-solid fa-cloud-arrow-up" style="font-size: 30px; color: var(--admin-accent); margin-bottom: 10px;"></i>
                            <input type="file" name="images[]" multiple class="premium-input" style="opacity: 0; position: absolute; inset: 0; width: 100%; height: 100%; cursor: pointer;" id="imageUpload" onchange="previewImages(this)">
                            <div id="uploadText">
                                <span style="font-weight: 800; font-size: 13px; display: block;">Drop images here or click to upload</span>
                                <span style="display: block; font-size: 11px; color: var(--admin-text-muted); margin-top: 5px;">High-quality JPG/PNG/WebP</span>
                            </div>
                        </div>
                        <div id="imagePreviewContainer" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(80px, 1fr)); gap: 10px; margin-top: 15px;"></div>
                    </div>
                </div>

                <!-- Visibility & Flags -->
                <div style="background: var(--admin-bg); padding: 30px; border-radius: 18px; border: 1px solid var(--admin-border);">
                    <h4 style="margin-top: 0; margin-bottom: 6px; font-weight: 800; font-size: 14px; text-transform: uppercase; color: var(--admin-accent);">Visibility &amp; Flags</h4>
                    <p style="margin: 0 0 20px; font-size: 11px; color: var(--admin-text-muted);">Control where this product appears on the homepage.</p>

                    <div class="flag-row">
                        <div class="flag-row-text">
                            <div class="flag-title">⭐ Currently Hot</div>
                            <div class="flag-desc">Show in homepage &ldquo;Currently Hot&rdquo; section</div>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" name="is_featured" value="1" <?php echo !empty($product['is_featured']) ? 'checked' : ''; ?>>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    <div class="flag-row">
                        <div class="flag-row-text">
                            <div class="flag-title">🆕 New Drop Badge</div>
                            <div class="flag-desc">Display &ldquo;New Drop&rdquo; badge on product card</div>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" name="is_new" value="1" <?php echo !empty($product['is_new']) ? 'checked' : ''; ?>>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    <div class="flag-row">
                        <div class="flag-row-text">
                            <div class="flag-title">🔥 Pinned Hot Pick</div>
                            <div class="flag-desc">Extra manual &ldquo;Currently Hot&rdquo; pin (overrides featured filter)</div>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" name="is_currently_hot" value="1" <?php echo !empty($product['is_currently_hot']) ? 'checked' : ''; ?>>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    <!-- DNA Specs -->
                    <div style="margin-top:20px; padding-top:20px; border-top:1px solid var(--admin-border);">
                        <label style="font-size:10px; font-weight:800; color:var(--admin-accent); text-transform:uppercase; margin-bottom:15px; display:block;">Laboratory DNA Labels</label>
                        <div class="premium-form-group">
                            <label style="font-size:11px;">Tech Spec (e.g. Luxury Fabric)</label>
                            <input type="text" name="dna_tech" class="premium-input" value="<?php echo htmlspecialchars($product['dna_tech'] ?? 'High-Density Breathable Luxury Fabric'); ?>" style="font-size:12px; padding:10px;">
                        </div>
                        <div class="premium-form-group" style="margin-bottom:0;">
                            <label style="font-size:11px;">Fit Spec (e.g. Modern Silhouette)</label>
                            <input type="text" name="dna_fit" class="premium-input" value="<?php echo htmlspecialchars($product['dna_fit'] ?? 'Precision Engineered Modern Silhouette'); ?>" style="font-size:12px; padding:10px;">
                        </div>
                    </div>
                </div>

            </div><!-- /right-column -->
        </div><!-- /grid-2-1 -->

        <div style="margin-top: 40px; border-top: 1px solid var(--admin-border); padding-top: 30px; display: flex; justify-content: flex-end;">
            <button type="submit" class="btn btn-primary" style="padding: 14px 40px; border-radius: 12px; font-weight: 800; letter-spacing: 1px;">UPDATE PRODUCT</button>
        </div>
    </form>
</div>

<script>
function previewImages(input) {
    const container = document.getElementById('imagePreviewContainer');
    container.innerHTML = '';
    if (input.files) {
        Array.from(input.files).forEach(file => {
            const reader = new FileReader();
            reader.onload = (e) => {
                const div = document.createElement('div');
                div.style.cssText = 'width: 80px; height: 80px; border-radius: 8px; overflow: hidden; border: 1px solid #e2e8f0; position: relative;';
                div.innerHTML = `<img src="${e.target.result}" style="width: 100%; height: 100%; object-fit: cover;">`;
                container.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
    }
}

function loadSubcategories(parentId) {
    const wrapper = document.getElementById('subcategory_wrapper');
    const select = document.getElementById('subcategory_select');
    if (!parentId) { wrapper.style.display = 'none'; return; }
    fetch(`../../backend/handlers/ajax_subcategories.php?parent_id=${parentId}`)
        .then(r => r.json())
        .then(data => {
            select.innerHTML = '<option value="">-- Select Subcategory --</option>';
            if (data.length > 0) {
                data.forEach(s => {
                    select.innerHTML += `<option value="${s.id}">${s.name}</option>`;
                });
                wrapper.style.display = 'block';
            } else {
                wrapper.style.display = 'none';
            }
        });
}
let variantIndex = <?php echo count($variants); ?>;

function showToast(message) {
    const toast = document.createElement('div');
    toast.style.cssText = 'position: fixed; bottom: 20px; right: 20px; background: #10b981; color: white; padding: 12px 24px; border-radius: 8px; font-weight: 700; font-size: 14px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); z-index: 9999; transform: translateY(100px); opacity: 0; transition: all 0.3s ease;';
    toast.innerHTML = '<i class="fa-solid fa-circle-check" style="margin-right: 8px;"></i> ' + message;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.transform = 'translateY(0)';
        toast.style.opacity = '1';
    }, 10);
    
    setTimeout(() => {
        toast.style.transform = 'translateY(100px)';
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

function addVariant() {
    const container = document.getElementById('variants-container');
    const row = document.createElement('div');
    row.className = 'variant-row';
    row.style.cssText = 'display: grid; grid-template-columns: 2fr 2fr 3fr 2fr 50px; gap: 12px; align-items: flex-end; background: white; padding: 20px; border-radius: 12px; border: 1px solid var(--admin-border); margin-bottom: 12px;';

    row.innerHTML = `
        <div class="premium-form-group" style="margin-bottom: 0;">
            <label style="font-size: 10px; font-weight: 800;">Size</label>
            <input type="text" name="variants[${variantIndex}][size]" class="premium-input" placeholder="S, M, L" style="padding: 10px;">
        </div>
        <div class="premium-form-group" style="margin-bottom: 0;">
            <label style="font-size: 10px; font-weight: 800;">Color</label>
            <input type="text" name="variants[${variantIndex}][color]" class="premium-input" placeholder="Black" style="padding: 10px;">
        </div>
        <div class="premium-form-group" style="margin-bottom: 0;">
            <label style="font-size: 10px; font-weight: 800;">SKU</label>
            <input type="text" name="variants[${variantIndex}][sku]" class="premium-input" placeholder="BH-C1" style="padding: 10px;">
        </div>
        <div class="premium-form-group" style="margin-bottom: 0;">
            <label style="font-size: 10px; font-weight: 800;">Stock</label>
            <input type="number" name="variants[${variantIndex}][stock]" class="premium-input" value="0" style="padding: 10px; font-weight: 800;">
        </div>
        <button type="button" class="action-btn-sleek action-btn-delete" onclick="removeVariant(this)" style="height: 42px; display: flex; align-items: center; justify-content: center;"><i class="fa-solid fa-trash"></i></button>
    `;

    container.appendChild(row);
    variantIndex++;
    showToast('Variant added successfully!');
}

function removeVariant(button) {
    const row = button.closest('.variant-row');
    if (row) row.remove();
}

function toggleBulkTool() {
    const tool = document.getElementById('bulk-variant-tool');
    tool.style.display = tool.style.display === 'none' ? 'block' : 'none';
}

function generateBulkVariants() {
    const sizeStr = document.getElementById('bulk-sizes').value.trim();
    const colorStr = document.getElementById('bulk-colors').value.trim();
    
    if (!sizeStr && !colorStr) {
        alert('Please enter at least one size or color.');
        return;
    }
    
    const sizes = sizeStr ? sizeStr.split(',').map(s => s.trim()) : ['Default'];
    const colors = colorStr ? colorStr.split(',').map(c => c.trim()) : ['Default'];
    
    let count = 0;
    sizes.forEach(size => {
        colors.forEach(color => {
            const container = document.getElementById('variants-container');
            const row = document.createElement('div');
            row.className = 'variant-row';
            row.style.cssText = 'display: grid; grid-template-columns: 2fr 2fr 3fr 2fr 50px; gap: 12px; align-items: flex-end; background: white; padding: 20px; border-radius: 12px; border: 1px solid var(--admin-border); margin-bottom: 12px;';

            // Generate a random SKU suffix
            const randomSuffix = Math.random().toString(36).substring(7).toUpperCase();

            row.innerHTML = `
                <div class="premium-form-group" style="margin-bottom: 0;">
                    <label style="font-size: 10px; font-weight: 800;">Size</label>
                    <input type="text" name="variants[${variantIndex}][size]" class="premium-input" value="${size === 'Default' ? '' : size}" placeholder="S, M, L" style="padding: 10px;">
                </div>
                <div class="premium-form-group" style="margin-bottom: 0;">
                    <label style="font-size: 10px; font-weight: 800;">Color</label>
                    <input type="text" name="variants[${variantIndex}][color]" class="premium-input" value="${color === 'Default' ? '' : color}" placeholder="Black" style="padding: 10px;">
                </div>
                <div class="premium-form-group" style="margin-bottom: 0;">
                    <label style="font-size: 10px; font-weight: 800;">SKU</label>
                    <input type="text" name="variants[${variantIndex}][sku]" class="premium-input" value="BH-${randomSuffix}" placeholder="BH-C1" style="padding: 10px;">
                </div>
                <div class="premium-form-group" style="margin-bottom: 0;">
                    <label style="font-size: 10px; font-weight: 800;">Stock</label>
                    <input type="number" name="variants[${variantIndex}][stock]" class="premium-input" value="10" style="padding: 10px; font-weight: 800;">
                </div>
                <button type="button" class="action-btn-sleek action-btn-delete" onclick="removeVariant(this)" style="height: 42px; display: flex; align-items: center; justify-content: center;"><i class="fa-solid fa-trash"></i></button>
            `;

            container.appendChild(row);
            variantIndex++;
            count++;
        });
    });
    
    toggleBulkTool();
    showToast(`Successfully generated ${count} variant combinations!`);
}

function removeImage(imageId) {
    if (confirm('Permanently delete this image from gallery?')) {
        fetch('ajax_delete_image.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ image_id: imageId, csrf_token: '<?php echo $csrfToken; ?>' })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const target = event.target;
                const imgCard = target.closest('div[style*="position: relative"]');
                if (imgCard) {
                    imgCard.style.transform = 'scale(0.8)';
                    imgCard.style.opacity = '0';
                    setTimeout(() => imgCard.remove(), 300);
                }
                showToast('Image removed from gallery');
            } else {
                alert('Error: ' + data.error);
            }
        });
    }
}
</script>
