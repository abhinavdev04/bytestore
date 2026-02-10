<?php
session_start();
require '../config/config.php';
require '../includes/auth.php';

checkEmployeeLogin();

// Handle product update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_product'])) {
    $product_id = intval($_POST['product_id']);
    $product_name = mysqli_real_escape_string($conn, $_POST['product_name']);
    $product_description = mysqli_real_escape_string($conn, $_POST['product_description']);
    $product_price = floatval($_POST['product_price']);
    $product_stock = intval($_POST['product_stock']);
    $category_id = !empty($_POST['category_id']) ? intval($_POST['category_id']) : NULL;
    
    $sql = "UPDATE product SET 
            product_name = '$product_name',
            product_description = '$product_description',
            product_price = $product_price,
            product_stock = $product_stock,
            category_id = " . ($category_id ? $category_id : 'NULL') . "
            WHERE product_id = $product_id";
    
    if (mysqli_query($conn, $sql)) {
        $success = "Product updated successfully!";
    } else {
        $error = "Error updating product: " . mysqli_error($conn);
    }
}

// Handle product deletion
if (isset($_GET['delete'])) {
    $product_id = intval($_GET['delete']);
    $sql = "DELETE FROM product WHERE product_id = $product_id";
    
    if (mysqli_query($conn, $sql)) {
        $success = "Product deleted successfully!";
    } else {
        $error = "Error deleting product: " . mysqli_error($conn);
    }
}

// Fetch categories for dropdown
$categories_sql = "SELECT * FROM category ORDER BY category_name ASC";
$categories_result = mysqli_query($conn, $categories_sql);
$categories = [];
while ($cat = mysqli_fetch_assoc($categories_result)) {
    $categories[] = $cat;
}

// Search and filter
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$filter_category = isset($_GET['category']) ? intval($_GET['category']) : 0;

$where_clauses = [];
if ($search) {
    $where_clauses[] = "(product_name LIKE '%$search%' OR product_description LIKE '%$search%')";
}
if ($filter_category > 0) {
    $where_clauses[] = "category_id = $filter_category";
}

$where_sql = !empty($where_clauses) ? "WHERE " . implode(" AND ", $where_clauses) : "";

// Pagination
$per_page = 10;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $per_page;

$count_sql = "SELECT COUNT(*) as total FROM product $where_sql";
$count_result = mysqli_query($conn, $count_sql);
$total_products = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_products / $per_page);

$sql = "SELECT p.*, c.category_name
        FROM product p
        LEFT JOIN category c ON p.category_id = c.category_id
        $where_sql
        ORDER BY p.product_id DESC
        LIMIT $offset, $per_page";

$products_result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Products - ByteStore</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .products-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        .search-filter-bar {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            align-items: center;
        }

        .search-filter-bar input,
        .search-filter-bar select {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }

        .search-filter-bar input[type="text"] {
            flex: 1;
            min-width: 250px;
        }

        .search-filter-bar select {
            min-width: 200px;
        }

        .products-grid {
            display: grid;
            gap: 20px;
        }

        .product-card-edit {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            overflow: hidden;
            display: grid;
            grid-template-columns: 200px 1fr auto;
            gap: 20px;
            padding: 20px;
            transition: all 0.3s;
        }

        .product-card-edit:hover {
            box-shadow: 0 4px 16px rgba(0,0,0,0.15);
        }

        .product-image-section img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 5px;
        }

        .product-form-section {
            display: grid;
            gap: 15px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 150px 1fr;
            align-items: center;
            gap: 10px;
        }

        .form-row label {
            font-weight: 600;
            color: #333;
        }

        .form-row input,
        .form-row textarea,
        .form-row select {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            width: 100%;
        }

        .form-row textarea {
            resize: vertical;
            min-height: 60px;
        }

        .product-actions {
            display: flex;
            flex-direction: column;
            gap: 10px;
            justify-content: center;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.95rem;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            transition: all 0.3s;
        }

        .btn-success {
            background: #28a745;
            color: white;
        }

        .btn-success:hover {
            background: #218838;
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background: #c82333;
        }

        .btn-primary {
            background: #007bff;
            color: white;
        }

        .btn-primary:hover {
            background: #0056b3;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }

        .category-badge {
            display: inline-block;
            padding: 4px 12px;
            background: #e7f3ff;
            color: #0056b3;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 30px;
            flex-wrap: wrap;
        }

        .pagination a,
        .pagination span {
            padding: 8px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            text-decoration: none;
            color: #007bff;
        }

        .pagination span {
            background: #007bff;
            color: white;
            border-color: #007bff;
        }

        .pagination a:hover {
            background: #e7f3ff;
        }

        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .stats-bar {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .stat-card {
            background: white;
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            flex: 1;
            min-width: 200px;
        }

        .stat-card h3 {
            margin: 0 0 5px 0;
            font-size: 2rem;
            color: #007bff;
        }

        .stat-card p {
            margin: 0;
            color: #666;
            font-size: 0.9rem;
        }

        @media (max-width: 1024px) {
            .product-card-edit {
                grid-template-columns: 150px 1fr;
            }

            .product-actions {
                grid-column: 1 / -1;
                flex-direction: row;
            }
        }

        @media (max-width: 768px) {
            .product-card-edit {
                grid-template-columns: 1fr;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .search-filter-bar {
                flex-direction: column;
            }

            .search-filter-bar input,
            .search-filter-bar select {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="products-container">
        <div class="card">
            <h2>Edit Products</h2>
            
            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <!-- Stats -->
            <div class="stats-bar">
                <div class="stat-card">
                    <h3><?php echo $total_products; ?></h3>
                    <p>Total Products</p>
                </div>
                <div class="stat-card">
                    <h3><?php echo count($categories); ?></h3>
                    <p>Categories</p>
                </div>
            </div>
            
            <a href="dashboard.php" class="btn btn-secondary" style="margin-bottom: 20px;">← Back to Dashboard</a>
            
            <!-- Search and Filter -->
            <form method="GET" class="search-filter-bar">
                <input type="text" name="search" placeholder="Search products..." value="<?php echo htmlspecialchars($search); ?>">
                
                <select name="category">
                    <option value="0">All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['category_id']; ?>" 
                                <?php echo $filter_category == $cat['category_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['category_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="edit_products.php" class="btn btn-secondary">Clear</a>
            </form>
            
            <!-- Products Grid -->
            <div class="products-grid">
                <?php if ($products_result && mysqli_num_rows($products_result) > 0): ?>
                    <?php while ($product = mysqli_fetch_assoc($products_result)): ?>
                        <div class="product-card-edit">
                            <!-- Product Image -->
                            <div class="product-image-section">
                                <img src="../<?php echo $product['product_image_path']; ?>" 
                                     alt="<?php echo htmlspecialchars($product['product_name']); ?>"
                                     onerror="this.src='../assets/images/placeholder.jpg'">
                                <?php if ($product['category_name']): ?>
                                    <div style="margin-top: 10px;">
                                        <span class="category-badge"><?php echo htmlspecialchars($product['category_name']); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Product Form -->
                            <form method="POST" class="product-form-section">
                                <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                                
                                <div class="form-row">
                                    <label>Name:</label>
                                    <input type="text" name="product_name" value="<?php echo htmlspecialchars($product['product_name']); ?>" required>
                                </div>
                                
                                <div class="form-row">
                                    <label>Description:</label>
                                    <textarea name="product_description" required><?php echo htmlspecialchars($product['product_description']); ?></textarea>
                                </div>
                                
                                <div class="form-row">
                                    <label>Price (Rs.):</label>
                                    <input type="number" name="product_price" step="0.01" value="<?php echo $product['product_price']; ?>" required>
                                </div>
                                
                                <div class="form-row">
                                    <label>Stock:</label>
                                    <input type="number" name="product_stock" value="<?php echo $product['product_stock']; ?>" required>
                                </div>
                                
                                <div class="form-row">
                                    <label>Category:</label>
                                    <select name="category_id">
                                        <option value="">No Category</option>
                                        <?php foreach ($categories as $cat): ?>
                                            <option value="<?php echo $cat['category_id']; ?>" 
                                                    <?php echo $product['category_id'] == $cat['category_id'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($cat['category_name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="product-actions">
                                    <button type="submit" name="update_product" class="btn btn-success"> Update</button>
                                </div>
                            </form>

                            <!-- Delete Action -->
                            <div class="product-actions">
                                <a href="?delete=<?php echo $product['product_id']; ?>" 
                                   class="btn btn-danger" 
                                   onclick="return confirm('Are you sure you want to delete this product?')"> Delete</a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p style="text-align: center; padding: 40px; color: #666;">No products found.</p>
                <?php endif; ?>
            </div>
            
            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo $filter_category; ?>">« Previous</a>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <?php if ($i == $page): ?>
                            <span><?php echo $i; ?></span>
                        <?php else: ?>
                            <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo $filter_category; ?>"><?php echo $i; ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>
                    
                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo $filter_category; ?>">Next »</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>

