<?php
include 'config.php';
$order_id = $_GET['id'] ?? 0;

$order = $con->query("SELECT * FROM orders WHERE id = $order_id")->fetch_assoc();
$items = $con->query("
    SELECT p.name, oi.quantity, oi.price 
    FROM order_items oi 
    JOIN products p ON oi.product_id = p.id 
    WHERE oi.order_id = $order_id
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>In đơn hàng</title>
</head>
<body onload="window.print()">
    <h2>Đơn hàng #<?= $order_id ?></h2>
    <p>Khách: <?= $order['customer_name'] ?></p>
    <p>Địa chỉ: <?= $order['shipping_address'] ?></p>
    <p>Ngày: <?= $order['order_date'] ?></p>

    <table border="1" cellpadding="10">
        <tr><th>Sản phẩm</th><th>Số lượng</th><th>Giá</th></tr>
        <?php while ($item = $items->fetch_assoc()): ?>
        <tr>
            <td><?= $item['name'] ?></td>
            <td><?= $item['quantity'] ?></td>
            <td><?= number_format($item['price'], 0, ',', '.') ?>đ</td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
