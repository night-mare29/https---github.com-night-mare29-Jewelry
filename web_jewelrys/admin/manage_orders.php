<?php
session_start();
include 'config.php';

$status_filter = $_GET['status'] ?? '';

// Xử lý xoá đơn hàng (xoá cả chi tiết)
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = $_GET['delete'];

    // Xoá chi tiết trước
    $con->query("DELETE FROM order_items WHERE order_id = $id");
    // Rồi xoá đơn hàng
    $con->query("DELETE FROM orders WHERE id = $id");

    $_SESSION['message'] = "🗑️ Đã xoá đơn hàng!";
    header("Location: manage_orders.php");
    exit;
}

// Xử lý cập nhật trạng thái
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['status'];

    $stmt = $con->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $order_id);
    $stmt->execute();

    $_SESSION['message'] = "✅ Đã cập nhật trạng thái đơn hàng!";
    header("Location: manage_orders.php" . ($status_filter ? "?status=$status_filter" : ""));
    exit;
}

// Lọc theo trạng thái
if ($status_filter !== '') {
    $stmt = $con->prepare("SELECT * FROM orders WHERE status = ? ORDER BY order_date DESC");
    $stmt->bind_param("s", $status_filter);
    $stmt->execute();
    $orders = $stmt->get_result();
} else {
    $orders = $con->query("SELECT * FROM orders ORDER BY order_date DESC");
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý đơn hàng</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/categories.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ccc; text-align: center; }
        .filter, .alert { margin: 10px 0; }
        select, button {
            padding: 5px 8px;
            margin-left: 5px;
        }
    </style>
</head>
<body>
<?php include 'sidebar.php'; ?>
<div class="wrapper">
    <div class="categories-container">
        <div class="page-header">
            <h2>📦 Quản lý đơn hàng</h2>
            <a href="dashboard.php" class="back-link">
                <i class="fas fa-arrow-left"></i>
                Về Tổng quan
            </a>
        </div>

    <div class="filter">
        <form method="GET">
            <label>Lọc theo trạng thái:</label>
            <select name="status" onchange="this.form.submit()">
                <option value="">Tất cả</option>
                <option value="pending" <?= $status_filter == 'pending' ? 'selected' : '' ?>>Chờ xử lý</option>
                <option value="shipped" <?= $status_filter == 'shipped' ? 'selected' : '' ?>>Đã giao</option>
                <option value="cancelled" <?= $status_filter == 'cancelled' ? 'selected' : '' ?>>Đã huỷ</option>
            </select>
        </form>
    </div>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert"><?= $_SESSION['message'] ?></div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <table>
        <tr>
            <th>ID</th>
            <th>Ngày đặt</th>
            <th>Khách hàng</th>
            <th>Tổng tiền</th>
            <th>Trạng thái</th>
<th>Chi tiết</th>
            <th>In</th>
            <th>Xoá</th>
        </tr>
        <?php while ($order = $orders->fetch_assoc()): ?>
            <tr>
                <td><?= $order['id'] ?></td>
                <td><?= $order['order_date'] ?></td>
                <td><?= htmlspecialchars($order['customer_name'] ?? 'Khách') ?></td>
                <td><?= number_format($order['total'], 0, ',', '.') ?>đ</td>
                <td>
                    <form method="post" style="display:inline-flex;">
                        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                        <select name="status">
                            <option value="pending" <?= $order['status'] == 'pending' ? 'selected' : '' ?>>Chờ xử lý</option>
                            <option value="shipped" <?= $order['status'] == 'shipped' ? 'selected' : '' ?>>Đã giao</option>
                            <option value="cancelled" <?= $order['status'] == 'cancelled' ? 'selected' : '' ?>>Đã huỷ</option>
                        </select>
                        <button type="submit" name="update_status">Lưu</button>
                    </form>
                </td>
                <td><a href="order_detail.php?id=<?= $order['id'] ?>">🔍</a></td>
                <td><a href="print_order.php?id=<?= $order['id'] ?>" target="_blank">🖨️</a></td>
                <td><a href="?delete=<?= $order['id'] ?>" onclick="return confirm('Bạn chắc chắn muốn xoá đơn hàng?')">❌</a></td>
            </tr>
        <?php endwhile; ?>
    </table>
    </div>
</div>
</body>
</html>
