<?php
// Shared product form - used by both admin and employee
function renderProductForm($product = null, $action = 'add') {
    $is_edit = ($action == 'edit' && $product);
    ?>
    
    <form method="POST" enctype="multipart/form-data">
        
        <div class="form-group">
            <label>Product Name</label>
            <input type="text" name="product_name"
                   value="<?php echo $is_edit ? $product['product_name'] : ''; ?>"
                   required>
        </div>

        <div class="form-group">
            <label>Description</label>
            <textarea name="product_description" rows="5" required><?php 
                echo $is_edit ? $product['product_description'] : ''; 
            ?></textarea>
        </div>

        <div class="form-group">
            <label>Price</label>
            <input type="number" step="0.01" name="product_price"
                   value="<?php echo $is_edit ? $product['product_price'] : ''; ?>"
                   required>
        </div>

        <div class="form-group">
            <label>Stock</label>
            <input type="number" min="0" name="product_stock"
                   value="<?php echo $is_edit ? $product['product_stock'] : ''; ?>"
                   required>
        </div>

        <!-- IMAGE UPLOAD -->
        <div class="form-group">
            <label>Product Image</label>
            <input type="file" name="product_image" 
                   <?php echo $is_edit ? '' : 'required'; ?>>
        </div>

        <!-- Show existing image in Edit Mode -->
        <?php if ($is_edit && !empty($product['product_image_path'])): ?>
            <div class="form-group">
                <p>Current Image:</p>
                <img src="../<?php echo $product['product_image_path']; ?>"
                     style="width:120px;height:auto;border:1px solid #ccc;">
            </div>
        <?php endif; ?>

        <?php if ($is_edit): ?>
            <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
        <?php endif; ?>

        <button type="submit"
                name="<?php echo $is_edit ? 'update_product' : 'add_product'; ?>"
                class="btn btn-success">
            <?php echo $is_edit ? 'Update' : 'Add'; ?> Product
        </button>

    </form>

<?php } ?>
