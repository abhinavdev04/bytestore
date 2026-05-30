<?php

session_start();

require '../config/config.php';

require '../includes/auth.php';

require '../includes/functions.php';



checkEmployeeLogin();



$total_products  = (int)mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM product"))['c'];

$total_customers = (int)mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM customer"))['c'];

$total_orders    = (int)mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM orders"))['c'];

$total_revenue   = (float)(mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(total_amount),0) as c FROM orders WHERE payment_status IN ('Paid','Pending') AND order_status != 'Cancelled'"))['c'] ?? 0);

$pending_orders  = (int)mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM orders WHERE order_status IN ('Pending','Processing')"))['c'];

$low_stock       = (int)mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM product WHERE product_stock <= 5 AND product_stock > 0"))['c'];

$out_of_stock    = (int)mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM product WHERE product_stock = 0"))['c'];



$labels = [];

$revenue_data = [];

$orders_data = [];

$monthly = mysqli_query($conn, "SELECT DATE_FORMAT(order_date, '%Y-%m') as month, SUM(total_amount) as revenue, COUNT(*) as orders 

    FROM orders WHERE order_status != 'Cancelled' AND order_date >= DATE_SUB(NOW(), INTERVAL 6 MONTH) 

    GROUP BY month ORDER BY month ASC");

if ($monthly) {

    while ($row = mysqli_fetch_assoc($monthly)) {

        $labels[] = date('M Y', strtotime($row['month'] . '-01'));

        $revenue_data[] = round((float)$row['revenue'], 2);

        $orders_data[] = (int)$row['orders'];

    }

}



$cat_labels = [];

$cat_revenue = [];

$cat_perf = mysqli_query($conn, "SELECT c.category_name, COALESCE(SUM(oi.quantity * oi.price), 0) as revenue

    FROM category c

    LEFT JOIN product p ON c.category_id = p.category_id

    LEFT JOIN order_items oi ON p.product_id = oi.product_id

    GROUP BY c.category_id ORDER BY revenue DESC LIMIT 8");

if ($cat_perf) {

    while ($row = mysqli_fetch_assoc($cat_perf)) {

        $cat_labels[] = $row['category_name'];

        $cat_revenue[] = round((float)$row['revenue'], 2);

    }

}



$cust_labels = [];

$cust_data = [];

$cust_growth = mysqli_query($conn, "SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as cnt

    FROM customer WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)

    GROUP BY month ORDER BY month ASC");

if ($cust_growth) {

    while ($row = mysqli_fetch_assoc($cust_growth)) {

        $cust_labels[] = date('M Y', strtotime($row['month'] . '-01'));

        $cust_data[] = (int)$row['cnt'];

    }

}



$top_labels = [];

$top_sold = [];

$top_products = mysqli_query($conn, "SELECT p.product_name, SUM(oi.quantity) as sold

    FROM order_items oi JOIN product p ON oi.product_id = p.product_id 

    GROUP BY p.product_id ORDER BY sold DESC LIMIT 6");

if ($top_products) {

    while ($row = mysqli_fetch_assoc($top_products)) {

        $top_labels[] = mb_strimwidth($row['product_name'], 0, 22, '…');

        $top_sold[] = (int)$row['sold'];

    }

}



$inv_labels = ['In Stock', 'Low Stock (≤5)', 'Out of Stock'];

$inv_in = (int)mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM product WHERE product_stock > 5"))['c'];

$inv_low = $low_stock;

$inv_out = $out_of_stock;



$low_stock_list = mysqli_query($conn, "SELECT product_name, product_stock FROM product WHERE product_stock <= 5 ORDER BY product_stock ASC LIMIT 10");

$recent_orders = mysqli_query($conn, "SELECT o.*, c.customer_name FROM orders o JOIN customer c ON o.customer_id = c.customer_id ORDER BY o.order_date DESC LIMIT 5");



$page_title = 'Staff Dashboard';

include '../includes/header.php';

?>



<div class="card">

    <h1><i class="fa-solid fa-chart-line"></i> Analytics Dashboard</h1>

    <p class="text-muted">Welcome back, <?php echo e($_SESSION['employee_name']); ?></p>

</div>



<div class="dashboard-stats">

    <div class="stat-card stat-card--primary">

        <div class="stat-card__label">Total Revenue</div>

        <div class="stat-card__value" style="font-size:1.75rem;"><?php echo formatPrice($total_revenue); ?></div>

    </div>

    <div class="stat-card">

        <div class="stat-card__label">Total Orders</div>

        <div class="stat-card__value"><?php echo $total_orders; ?></div>

        <div class="stat-card__change"><?php echo $pending_orders; ?> pending</div>

    </div>

    <div class="stat-card">

        <div class="stat-card__label">Products</div>

        <div class="stat-card__value"><?php echo $total_products; ?></div>

        <?php if ($low_stock > 0): ?><div class="stat-card__change" style="color:var(--color-warning);"><?php echo $low_stock; ?> low stock</div><?php endif; ?>

    </div>

    <div class="stat-card">

        <div class="stat-card__label">Customers</div>

        <div class="stat-card__value"><?php echo $total_customers; ?></div>

    </div>

</div>



<?php if ($low_stock > 0 || $out_of_stock > 0): ?>

<div class="alert alert-warning">

    <strong><i class="fa-solid fa-triangle-exclamation"></i> Inventory Alert:</strong>

    <?php echo $low_stock; ?> products are low on stock, <?php echo $out_of_stock; ?> are out of stock.

    <a href="edit_products.php">Manage Inventory →</a>

</div>

<?php endif; ?>



<div class="dashboard-grid">

    <div class="card chart-card">

        <h3 style="margin-bottom:1rem;">Revenue (6 Months)</h3>

        <div class="chart-container"><canvas id="chartRevenue"></canvas></div>

    </div>

    <div class="card chart-card">

        <h3 style="margin-bottom:1rem;">Orders per Month</h3>

        <div class="chart-container"><canvas id="chartOrders"></canvas></div>

    </div>

</div>



<div class="dashboard-grid">

    <div class="card chart-card">

        <h3 style="margin-bottom:1rem;">Category Performance</h3>

        <div class="chart-container"><canvas id="chartCategory"></canvas></div>

    </div>

    <div class="card chart-card">

        <h3 style="margin-bottom:1rem;">Top Selling Products</h3>

        <div class="chart-container"><canvas id="chartTopProducts"></canvas></div>

    </div>

</div>



<div class="dashboard-grid">

    <div class="card chart-card">

        <h3 style="margin-bottom:1rem;">Customer Growth</h3>

        <div class="chart-container chart-container--sm"><canvas id="chartCustomers"></canvas></div>

    </div>

    <div class="card chart-card">

        <h3 style="margin-bottom:1rem;">Inventory Overview</h3>

        <div class="chart-container chart-container--sm"><canvas id="chartInventory"></canvas></div>

    </div>

</div>



<?php if ($low_stock_list && mysqli_num_rows($low_stock_list) > 0): ?>

<div class="card">

    <h3 style="margin-bottom:1rem;"><i class="fa-solid fa-boxes-stacked"></i> Low Stock Report</h3>

    <table>

        <thead><tr><th>Product</th><th>Stock</th><th>Status</th></tr></thead>

        <tbody>

        <?php while ($lp = mysqli_fetch_assoc($low_stock_list)): ?>

            <tr>

                <td><?php echo e($lp['product_name']); ?></td>

                <td><?php echo (int)$lp['product_stock']; ?></td>

                <td><?php echo (int)$lp['product_stock'] === 0 ? '<span class="status-badge status-cancelled">Out of Stock</span>' : '<span class="status-badge status-pending">Low Stock</span>'; ?></td>

            </tr>

        <?php endwhile; ?>

        </tbody>

    </table>

</div>

<?php endif; ?>



<div class="card">

    <div class="card__header"><h3>Recent Orders</h3><a href="view_orders.php" class="btn btn--sm btn--outline">View All</a></div>

    <?php if ($recent_orders && mysqli_num_rows($recent_orders) > 0): ?>

    <table>

        <thead><tr><th>Order #</th><th>Customer</th><th>Amount</th><th>Status</th><th>Date</th></tr></thead>

        <tbody>

        <?php while ($o = mysqli_fetch_assoc($recent_orders)): ?>

            <tr>

                <td><a href="order_details.php?id=<?php echo (int)$o['order_id']; ?>">#<?php echo (int)$o['order_id']; ?></a></td>

                <td><?php echo e($o['customer_name']); ?></td>

                <td><?php echo formatPrice($o['total_amount']); ?></td>

                <td><span class="status-badge status-<?php echo strtolower($o['order_status']); ?>"><?php echo e($o['order_status']); ?></span></td>

                <td><?php echo date('M j, Y', strtotime($o['order_date'])); ?></td>

            </tr>

        <?php endwhile; ?>

        </tbody>

    </table>

    <?php else: ?>

        <p class="text-muted">No orders yet.</p>

    <?php endif; ?>

</div>



<div class="card">

    <h3 style="margin-bottom:1rem;">Quick Actions</h3>

    <div style="display:flex;gap:0.75rem;flex-wrap:wrap;">

        <a href="add_products.php" class="btn btn--success"><i class="fa-solid fa-plus"></i> Add Product</a>

        <a href="edit_products.php" class="btn btn--primary"><i class="fa-solid fa-pen"></i> Edit Products</a>

        <a href="delete_products.php" class="btn btn--danger"><i class="fa-solid fa-trash"></i> Delete Products</a>

        <a href="manage_customers.php" class="btn btn--primary"><i class="fa-solid fa-users"></i> Manage Customers</a>

        <a href="manage_employees.php" class="btn btn--primary"><i class="fa-solid fa-user-tie"></i> Manage Employees</a>

        <a href="manage_discounts.php" class="btn btn--primary"><i class="fa-solid fa-tags"></i> Manage Discounts</a>
        <a href="support_tickets.php" class="btn btn--primary"><i class="fa-solid fa-headset"></i> Support Tickets</a>
        <a href="view_orders.php" class="btn btn--primary"><i class="fa-solid fa-receipt"></i> View Orders</a>

    </div>

</div>



<script>

window.byteStoreChartData = {

    labels: <?php echo json_encode($labels); ?>,

    revenue: <?php echo json_encode($revenue_data); ?>,

    orders: <?php echo json_encode($orders_data); ?>,

    catLabels: <?php echo json_encode($cat_labels); ?>,

    catRevenue: <?php echo json_encode($cat_revenue); ?>,

    topLabels: <?php echo json_encode($top_labels); ?>,

    topSold: <?php echo json_encode($top_sold); ?>,

    custLabels: <?php echo json_encode($cust_labels); ?>,

    custData: <?php echo json_encode($cust_data); ?>,

    invData: [<?php echo $inv_in; ?>, <?php echo $inv_low; ?>, <?php echo $inv_out; ?>]

};

</script>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<script src="../assets/js/dashboard-charts.js"></script>



<?php include '../includes/footer.php'; ?>

