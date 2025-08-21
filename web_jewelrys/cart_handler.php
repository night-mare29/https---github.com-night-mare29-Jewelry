<?php
session_start();
include 'admin/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = (int)$_POST['product_id'];
    $quantity = (int)$_POST['quantity'];

    if ($product_id > 0 && $quantity > 0) {
        $stmt = $con->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();

        if ($product) {
            $price = $product['price'];
            if ($product['discount'] > 0) {
                $price *= (1 - $product['discount'] / 100);
            }

            if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

            if (!isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id] = ['quantity' => 0, 'price' => $price];
            }

            $_SESSION['cart'][$product_id]['quantity'] += $quantity;

            echo json_encode([
                'success' => true,
                'message' => 'Đã thêm sản phẩm vào giỏ hàng',
                'cart_count' => array_sum(array_column($_SESSION['cart'], 'quantity')),
                'product_name' => $product['name']
            ]);
            exit;
        }
    }
}

echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra']);
?>