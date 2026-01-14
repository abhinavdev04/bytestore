<?php
// -------------------------------------------------------------
// Public homepage
// - Shows featured products
// - Navigation changes if a customer or staff is logged in
// - Added "Top Ordered Products" fade banner
// - Added Categories Horizontal Scroll Section
// -------------------------------------------------------------

session_start();
require 'config/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ByteStore - Tech E-Commerce</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* Categories Section Styles */
        .categories-section {
            margin: 40px 0;
            background: #f8f9fa;
            padding: 30px 20px;
            border-radius: 8px;
        }

        .categories-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .categories-header h2 {
            margin: 0;
            font-size: 1.8rem;
            color: #333;
        }

        .categories-nav {
            display: flex;
            gap: 10px;
        }

        .categories-nav button {
            background: #007bff;
            color: white;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            font-size: 1.2rem;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .categories-nav button:hover {
            background: #0056b3;
            transform: scale(1.1);
        }

        .categories-nav button:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: scale(1);
        }

        .categories-scroll-container {
            position: relative;
            overflow: hidden;
        }

        .categories-scroll {
            display: flex;
            gap: 20px;
            overflow-x: auto;
            scroll-behavior: smooth;
            padding: 10px 0;
            scrollbar-width: none; /* Firefox */
            -ms-overflow-style: none; /* IE and Edge */
        }

        .categories-scroll::-webkit-scrollbar {
            display: none; /* Chrome, Safari, Opera */
        }

        .category-card {
            min-width: 180px;
            background: white;
            border-radius: 12px;
            padding: 25px 15px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            text-decoration: none;
            color: inherit;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
        }

        .category-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 16px rgba(0,0,0,0.15);
        }

        .category-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: white;
            margin-bottom: 5px;
        }

        .category-card h3 {
            margin: 0;
            font-size: 1rem;
            color: #333;
            font-weight: 600;
        }

        .category-card p {
            margin: 0;
            font-size: 0.85rem;
            color: #666;
        }

        /* Gradient overlays for scroll indication */
        .categories-scroll-container::before,
        .categories-scroll-container::after {
            content: '';
            position: absolute;
            top: 0;
            bottom: 0;
            width: 50px;
            pointer-events: none;
            z-index: 1;
        }

        .categories-scroll-container::before {
            left: 0;
            background: linear-gradient(to right, #f8f9fa, transparent);
        }

        .categories-scroll-container::after {
            right: 0;
            background: linear-gradient(to left, #f8f9fa, transparent);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .category-card {
                min-width: 150px;
            }

            .categories-header h2 {
                font-size: 1.4rem;
            }
        }

        /* Category Products Horizontal Scroll */
        .category-products-section {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .category-products-scroll-container {
            position: relative;
            overflow: hidden;
            margin-top: 20px;
        }

        .category-products-scroll {
            display: flex;
            gap: 20px;
            overflow-x: auto;
            scroll-behavior: smooth;
            padding: 10px 0;
            scrollbar-width: thin;
            scrollbar-color: #007bff #f1f1f1;
        }

        .category-products-scroll::-webkit-scrollbar {
            height: 8px;
        }

        .category-products-scroll::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .category-products-scroll::-webkit-scrollbar-thumb {
            background: #007bff;
            border-radius: 10px;
        }

        .category-products-scroll::-webkit-scrollbar-thumb:hover {
            background: #0056b3;
        }

        .product-card-horizontal {
            min-width: 280px;
            max-width: 280px;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: all 0.3s;
            display: flex;
            flex-direction: column;
        }

        .product-card-horizontal:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 16px rgba(0,0,0,0.15);
        }

        .product-card-horizontal img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .product-card-horizontal .product-card-body {
            padding: 15px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .product-card-horizontal h3 {
            font-size: 1rem;
            margin: 0 0 10px 0;
            color: #333;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            min-height: 2.5em;
        }

        .product-card-horizontal .price {
            font-size: 1.2rem;
            color: #007bff;
            font-weight: bold;
            margin: 5px 0;
        }

        .product-card-horizontal .stock {
            font-size: 0.9rem;
            color: #666;
            margin: 5px 0 15px 0;
        }

        .product-card-horizontal .btn {
            margin-top: auto;
        }
    </style>
</head>
<body>

<header>
    <nav>
        <!-- Logo that refreshes the homepage -->
        <div class="logo" onclick="window.location.href='index.php'">ByteStore</div>
        <ul>
            <!-- Home is always active on this page -->
            <li><a href="index.php" class="active">Home</a></li>

            <?php if (isset($_SESSION['customer_id'])): ?>
                <!-- Links for logged-in customer -->
                <li><a href="customer/shop.php">Shop</a></li>
                <li><a href="customer/cart.php">Cart</a></li>
                <li><a href="customer/logout.php">Logout (<?php echo $_SESSION['customer_name']; ?>)</a></li>

            <?php elseif (isset($_SESSION['employee_id'])): ?>
                <!-- Links for logged-in staff -->
                <li><a href="employee/dashboard.php">Staff Dashboard</a></li>
               <li><a href="employee/logout.php">Logout</a></li>

            <?php else: ?>
                <!-- Links for visitors (not logged in) -->
                <li><a href="customer/login.php">Customer Login</a></li>
                <li><a href="customer/register.php">Register</a></li>
                <li><a href="employee/login.php">Staff Login</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>


<!-- Main page content container -->
<div class="container">
    <div class="card text-center">
        <h1>Welcome to ByteStore</h1>
        <p style="font-size: 1.2rem; margin: 20px 0;">Your One-Stop Shop for Tech Products</p>

        <?php
        /*
         * Fetch Top Ordered Products for Banner
         */
        $top_sql = "
            SELECT p.product_id, p.product_name, p.product_image_path, p.product_price,
                   SUM(oi.quantity) as total_sold
            FROM order_items oi
            JOIN product p ON oi.product_id = p.product_id
            GROUP BY p.product_id
            ORDER BY total_sold DESC
            LIMIT 6
        ";
        $top_result = mysqli_query($conn, $top_sql);

        $top_products = [];
        if ($top_result && mysqli_num_rows($top_result) > 0) {
            while ($row = mysqli_fetch_assoc($top_result)) {
                $top_products[] = $row;
            }
        }

        if (count($top_products) > 0):
        ?>

        <h2>Top Ordered Products</h2>

        <div class="fade-banner" id="fadeBanner">
            <?php foreach ($top_products as $i => $top): ?>
                <div class="fade-slide <?php echo $i === 0 ? 'active' : ''; ?>">
                    <!-- LEFT IMAGE -->
                    <div class="fade-image">
                        <img src="<?php echo $top['product_image_path']; ?>"
                             onerror="this.src='assets/images/placeholder.jpg'">
                    </div>

                    <!-- RIGHT INFO -->
                    <div class="fade-info">
                        <h2><?php echo $top['product_name']; ?></h2>
                        <p>Sold: <?php echo $top['total_sold']; ?></p>
                        <p>Price: Rs. <?php echo number_format($top['product_price'], 2); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>

            <!-- Navigation Arrows -->
            <button class="fade-prev" onclick="prevSlide()">❮</button>
            <button class="fade-next" onclick="nextSlide()">❯</button>
        </div>

        <?php endif; ?>


        <?php
        /*
         * Fetch Categories with Product Count
         */
        $cat_sql = "
            SELECT c.category_id, c.category_name, 
                   (SELECT COUNT(*) FROM product) as product_count
            FROM category c
            ORDER BY c.category_name ASC
        ";
        $cat_result = mysqli_query($conn, $cat_sql);
        
        if (!$cat_result) {
            // If query fails, set empty result
            $cat_result = false;
            echo "<!-- Category query error: " . mysqli_error($conn) . " -->";
        }

        // Category icon mapping
        $category_icons = [
            'Laptops' => '💻',
            'Desktops' => '🖥️',
            'Gaming' => '🎮',
            'Phones' => '📱',
            'Tablets' => '📱',
            'Wearables' => '⌚',
            'Audio' => '🎧',
            'Monitors' => '🖥️',
            'Storage' => '💾',
            'Processors' => '⚙️',
            'Graphics Cards' => '🎨',
            'Motherboards' => '🔧',
            'Memory' => '🧠',
            'Peripherals' => '🖱️',
            'Networking' => '🌐',
            'Printers' => '🖨️',
            'Software' => '💿',
        ];
        ?>

        <?php if ($cat_result && mysqli_num_rows($cat_result) > 0): ?>
        <!-- Categories Section -->
        <div class="categories-section">
            <div class="categories-header">
                <h2>Shop by Category</h2>
                <div class="categories-nav">
                    <button id="scrollLeft" onclick="scrollCategories('left')">❮</button>
                    <button id="scrollRight" onclick="scrollCategories('right')">❯</button>
                </div>
            </div>

            <div class="categories-scroll-container">
                <div class="categories-scroll" id="categoriesScroll">
                    <?php while ($category = mysqli_fetch_assoc($cat_result)): 
                        $cat_name = $category['category_name'];
                        $icon = '📦'; // Default icon
                        
                        // Find matching icon
                        foreach ($category_icons as $key => $emoji) {
                            if (stripos($cat_name, $key) !== false) {
                                $icon = $emoji;
                                break;
                            }
                        }
                    ?>
                        <a href="customer/shop.php?category=<?php echo $category['category_id']; ?>" class="category-card">
                            <div class="category-icon"><?php echo $icon; ?></div>
                            <h3><?php echo $cat_name; ?></h3>
                            <p><?php echo $category['product_count']; ?> products</p>
                        </a>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>


        <?php
        /*
         * Fetch latest products for Latest Arrivals Section
         */
        $sql = "SELECT * FROM product ORDER BY created_at DESC LIMIT 8";
        $result = mysqli_query($conn, $sql);
        ?>

        <h2 style="margin-top: 30px;background:rgba(88, 89, 92, 0.575);border: 1px solid #ddd;">Latest Arrivals</h2><br><br>
        <div class="product-grid">
            <?php while ($product = mysqli_fetch_assoc($result)): ?>
                <div class="product-card">
                    <img src="<?php echo $product['product_image_path']; ?>"
                         alt="<?php echo $product['product_name']; ?>"
                         onerror="this.src='assets/images/placeholder.jpg'">

                    <div class="product-card-body">
                        <h3><?php echo $product['product_name']; ?></h3>
                        <p class="price">Rs. <?php echo number_format($product['product_price'], 2); ?></p>
                        <p class="stock">Stock: <?php echo $product['product_stock']; ?></p>

                        <?php if (isset($_SESSION['customer_id'])): ?>
                            <a href="customer/shop.php" class="btn btn-primary">View Details</a>
                        <?php else: ?>
                            <a href="customer/login.php" class="btn btn-primary">Login to Shop</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>


        <?php
        /*
         * Fetch Products by Category with Horizontal Scroll
         * Show latest 12 products for each category
         */
        
        // Reset the categories result pointer
        mysqli_data_seek($cat_result, 0);
        
        while ($category = mysqli_fetch_assoc($cat_result)):
            $cat_name = $category['category_name'];
            $cat_id = $category['category_id'];
            
            // Fetch products for this category
            // Since products don't have category_id yet, we'll use product_name matching
            // This is a temporary solution until you add category_id to products table
            $products_sql = "
                SELECT * FROM product 
                WHERE product_name LIKE '%" . mysqli_real_escape_string($conn, $cat_name) . "%'
                   OR product_description LIKE '%" . mysqli_real_escape_string($conn, $cat_name) . "%'
                ORDER BY created_at DESC 
                LIMIT 12
            ";
            $products_result = mysqli_query($conn, $products_sql);
            
            // Skip if no products found
            if (!$products_result || mysqli_num_rows($products_result) == 0) {
                continue;
            }
            
            $unique_id = 'category-' . $cat_id;
        ?>
        
        <!-- Category Products Section -->
        <div class="category-products-section" style="margin-top: 40px;">
            <div class="categories-header">
                <h2><?php echo htmlspecialchars($cat_name); ?></h2>
                <div class="categories-nav">
                    <button onclick="scrollCategoryProducts('<?php echo $unique_id; ?>', 'left')">❮</button>
                    <button onclick="scrollCategoryProducts('<?php echo $unique_id; ?>', 'right')">❯</button>
                </div>
            </div>

            <div class="category-products-scroll-container">
                <div class="category-products-scroll" id="<?php echo $unique_id; ?>">
                    <?php while ($product = mysqli_fetch_assoc($products_result)): ?>
                        <div class="product-card-horizontal">
                            <img src="<?php echo $product['product_image_path']; ?>"
                                 alt="<?php echo htmlspecialchars($product['product_name']); ?>"
                                 onerror="this.src='assets/images/placeholder.jpg'">

                            <div class="product-card-body">
                                <h3><?php echo htmlspecialchars($product['product_name']); ?></h3>
                                <p class="price">Rs. <?php echo number_format($product['product_price'], 2); ?></p>
                                <p class="stock">Stock: <?php echo $product['product_stock']; ?></p>

                                <?php if (isset($_SESSION['customer_id'])): ?>
                                    <a href="customer/shop.php?product_id=<?php echo $product['product_id']; ?>" class="btn btn-primary">View Details</a>
                                <?php else: ?>
                                    <a href="customer/login.php" class="btn btn-primary">Login to Shop</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>

        <?php endwhile; ?>

        <div style="margin-top: 40px;">
            <?php if (!isset($_SESSION['customer_id']) && !isset($_SESSION['employee_id'])): ?>
                <a href="customer/register.php" class="btn btn-primary">Get Started - Register Now</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>


<!-- Scripts -->
<script>
// ==================== Banner Fade Script ====================
let index = 0;
const slides = document.querySelectorAll(".fade-slide");
const banner = document.querySelector(".fade-banner");

if (banner) {
    function showSlide(i) {
        slides.forEach(slide => slide.classList.remove("active"));
        slides[i].classList.add("active");
    }

    function nextSlide() {
        index = (index + 1) % slides.length;
        showSlide(index);
    }

    function prevSlide() {
        index = (index - 1 + slides.length) % slides.length;
        showSlide(index);
    }

    // Auto Fade
    let slideInterval = setInterval(nextSlide, 2000);

    // Pause on hover
    banner.addEventListener("mouseenter", () => {
        clearInterval(slideInterval);
    });

    banner.addEventListener("mouseleave", () => {
        slideInterval = setInterval(nextSlide, 2000);
    });
}


// ==================== Categories Scroll Script ====================
const categoriesScroll = document.getElementById('categoriesScroll');
const scrollLeftBtn = document.getElementById('scrollLeft');
const scrollRightBtn = document.getElementById('scrollRight');

if (categoriesScroll) {
    function scrollCategories(direction) {
        const scrollAmount = 400;
        if (direction === 'left') {
            categoriesScroll.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
        } else {
            categoriesScroll.scrollBy({ left: scrollAmount, behavior: 'smooth' });
        }
    }

    // Update button states based on scroll position
    function updateScrollButtons() {
        const isAtStart = categoriesScroll.scrollLeft <= 0;
        const isAtEnd = categoriesScroll.scrollLeft + categoriesScroll.clientWidth >= categoriesScroll.scrollWidth - 1;
        
        scrollLeftBtn.disabled = isAtStart;
        scrollRightBtn.disabled = isAtEnd;
    }

    // Check initial state
    updateScrollButtons();

    // Update on scroll
    categoriesScroll.addEventListener('scroll', updateScrollButtons);
}


// ==================== Category Products Scroll Script ====================
function scrollCategoryProducts(categoryId, direction) {
    const container = document.getElementById(categoryId);
    if (!container) return;
    
    const scrollAmount = 320; // Width of card + gap
    if (direction === 'left') {
        container.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
    } else {
        container.scrollBy({ left: scrollAmount, behavior: 'smooth' });
    }
}
</script>

</body>
</html>