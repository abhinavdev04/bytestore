<?php
// -------------------------------------------------------------
// Shared product handler
// - Used by employee add/edit product pages
// - Processes add/update product form submissions
// - Supports file upload for product images
// -------------------------------------------------------------
function handleProductAction($conn, $user_type, $user_id) {
    $success = '';
    $error   = '';

    $uploadDir = '../assets/uploads/'; // Folder where files will be stored

    // Ensure the folder exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Handle "Add Product" form submit
    if (isset($_POST['add_product'])) {
        $name        = mysqli_real_escape_string($conn, $_POST['product_name']);
        $description = mysqli_real_escape_string($conn, $_POST['product_description']);
        $price       = mysqli_real_escape_string($conn, $_POST['product_price']);
        $stock       = (int)$_POST['product_stock'];
        $image_path  = '';

        // Handle file upload
        if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
            $tmpName = $_FILES['product_image']['tmp_name'];
            $fileName = time() . '_' . basename($_FILES['product_image']['name']);
            $targetPath = $uploadDir . $fileName;

            if (move_uploaded_file($tmpName, $targetPath)) {
                $image_path = 'assets/uploads/' . $fileName;
            } else {
                $error = "Failed to upload image.";
                return ['success' => $success, 'error' => $error];
            }
        } else {
            $error = "Image is required.";
            return ['success' => $success, 'error' => $error];
        }

        // Insert into database
        $sql = "INSERT INTO product (product_name, product_description, product_price, product_image_path, product_stock) 
                VALUES ('$name', '$description', $price, '$image_path', $stock)";

        if (mysqli_query($conn, $sql)) {
            $success = "Product added successfully!";
        } else {
            $error = "Failed to add product.";
        }
    }

    // Handle "Update Product" form submit
    if (isset($_POST['update_product'])) {
        $product_id  = (int)$_POST['product_id'];
        $name        = mysqli_real_escape_string($conn, $_POST['product_name']);
        $description = mysqli_real_escape_string($conn, $_POST['product_description']);
        $price       = mysqli_real_escape_string($conn, $_POST['product_price']);
        $stock       = (int)$_POST['product_stock'];

        // Get existing image path
        $result = mysqli_query($conn, "SELECT product_image_path FROM product WHERE product_id=$product_id");
        $product = mysqli_fetch_assoc($result);
        $image_path = $product['product_image_path'];

        // Handle new image upload (optional)
        if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
            $tmpName = $_FILES['product_image']['tmp_name'];
            $fileName = time() . '_' . basename($_FILES['product_image']['name']);
            $targetPath = $uploadDir . $fileName;

            if (move_uploaded_file($tmpName, $targetPath)) {
                // Delete old image if exists
                if ($image_path && file_exists('../' . $image_path)) {
                    unlink('../' . $image_path);
                }
                $image_path = 'assets/uploads/' . $fileName;
            } else {
                $error = "Failed to upload new image.";
                return ['success' => $success, 'error' => $error];
            }
        }

        // Update database
        $update = "UPDATE product 
                   SET product_name='$name', 
                       product_description='$description', 
                       product_price=$price, 
                       product_stock=$stock, 
                       product_image_path='$image_path' 
                   WHERE product_id=$product_id";

        if (mysqli_query($conn, $update)) {
            $success = "Product updated successfully!";
        } else {
            $error = "Failed to update product.";
        }
    }

    return ['success' => $success, 'error' => $error];
}
?>
