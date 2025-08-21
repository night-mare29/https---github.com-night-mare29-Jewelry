<?php
include 'config.php';
$order_id = $_GET['id'] ?? 0;

$stmt = $con->prepare("SELECT * FROM orders WHERE id=?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

$stmt = $con->prepare("SELECT p.name, oi.quantity, oi.price 
                       FROM order_items oi 
                       JOIN products p ON oi.product_id = p.id 
                       WHERE oi.order_id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$items = $stmt->get_result();
?>
<link rel="stylesheet" href="../css/order_detail.css">
<h2>Chi tiết đơn hàng #<?= $order_id ?></h2>
<p>Khách: <?= htmlspecialchars($order['customer_name']) ?></p>
<p>Địa chỉ: <?= htmlspecialchars($order['shipping_address']) ?></p>
<p>Trạng thái: <?= $order['status'] ?></p>

<table border="1" cellpadding="10">
    <tr><th>Sản phẩm</th><th>Số lượng</th><th>Giá</th></tr>
    <?php while ($item = $items->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($item['name']) ?></td>
            <td><?= $item['quantity'] ?></td>
            <td><?= number_format($item['price'], 0, ',', '.') ?>đ</td>
        </tr>
    <?php endwhile; ?>
</table>
