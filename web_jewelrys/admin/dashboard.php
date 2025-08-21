<?php
include 'config.php';

// Đếm tổng sản phẩm
$count_products = mysqli_fetch_row(mysqli_query($con, "SELECT COUNT(*) FROM products"))[0];

// Đếm tổng đơn hàng
$count_orders = mysqli_fetch_row(mysqli_query($con, "SELECT COUNT(*) FROM orders"))[0];

// Tính tổng số lượng sản phẩm đã bán
$total_sales = mysqli_fetch_row(mysqli_query($con, "SELECT SUM(quantity) FROM order_items"))[0];

// Lấy doanh thu theo ngày trong 30 ngày gần nhất
$revenue_query = "SELECT
    DATE_FORMAT(o.order_date, '%d/%m') as day,
    SUM(oi.quantity * oi.price) as revenue
FROM orders o
JOIN order_items oi ON o.id = oi.order_id
WHERE o.order_date >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)
GROUP BY o.order_date
ORDER BY o.order_date";

$revenue_result = mysqli_query($con, $revenue_query);
$revenue_data = array(
    'labels' => array(),
    'values' => array()
);

while ($row = mysqli_fetch_assoc($revenue_result)) {
    $revenue_data['labels'][] = $row['day'];
    $revenue_data['values'][] = (int)$row['revenue'];
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
<?php include 'sidebar.php'; ?>

        <!-- Main Content -->
        <main class="main-content">
            <div class="container-fluid">
                <h3>Tổng quan hệ thống</h3>
                <div class="row">
                    <div class="col-md-4">
                        <div class="info-box bg-info">
                            <span class="info-box-icon"><i class="fas fa-box"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Tổng sản phẩm</span>
                                <span class="info-box-number"><?= $count_products ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-box bg-success">
                            <span class="info-box-icon"><i class="fas fa-shopping-cart"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Tổng đơn hàng</span>
                                <span class="info-box-number"><?= $count_orders ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-box bg-warning">
                            <span class="info-box-icon"><i class="fas fa-coins"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Sản phẩm đã bán</span>
                                <span class="info-box-number"><?= $total_sales ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Chart -->
                <div class="chart-container">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </main>
    </div>
    
    <script src="../js/dashboard.js"></script>
    <script>
        // Khởi tạo biểu đồ với dữ liệu PHP
        document.addEventListener('DOMContentLoaded', function() {
            initChart(<?php echo json_encode($revenue_data); ?>);
        });
    </script>
</body>
</html>
