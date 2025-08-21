<?php
ob_start();
include 'header.php';
include 'admin/config.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name       = trim($_POST['name']);
    $address    = trim($_POST['address']);
    $phone      = trim($_POST['phone']);
    $payment    = $_POST['payment_method'];

    $total = 0;

    // Tính tổng tiền
    foreach ($_SESSION['cart'] as $id => $item) {
        $product = $con->prepare("SELECT price, discount FROM products WHERE id = ?");
        $product->bind_param("i", $id);
        $product->execute();
        $result = $product->get_result()->fetch_assoc();

        $price = $result['price'];
        if ($result['discount'] > 0) {
            $price *= (1 - $result['discount'] / 100);
        }

        $quantity = is_array($item) ? $item['quantity'] : (int)$item;
        $total += $price * $quantity;
    }

    // Làm tròn tổng tiền lên số nguyên
    $total = ceil($total);

    // Xác định trạng thái đơn hàng
    $status = ($payment === 'bank') ? 'chờ chuyển khoản' : 'pending';

    // Lưu đơn hàng
    $stmt = $con->prepare("INSERT INTO orders (user_id, order_date, total, status, shipping_address, payment_method, customer_name) VALUES (?, NOW(), ?, ?, ?, ?, ?)");
    $stmt->bind_param("idssss", $user_id, $total, $status, $address, $payment, $name);
    $stmt->execute();
    $order_id = $stmt->insert_id;

    // Lưu chi tiết đơn hàng
    foreach ($_SESSION['cart'] as $id => $item) {
        $product = $con->prepare("SELECT price, discount FROM products WHERE id = ?");
        $product->bind_param("i", $id);
        $product->execute();
        $result = $product->get_result()->fetch_assoc();

        $price = $result['price'];
        if ($result['discount'] > 0) {
            $price *= (1 - $result['discount'] / 100);
        }

        $quantity = is_array($item) ? $item['quantity'] : (int)$item;

        $insert = $con->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        $insert->bind_param("iiid", $order_id, $id, $quantity, $price);
        $insert->execute();
    }

    $_SESSION['cart'] = [];

    if ($payment === 'momo') {
        $_SESSION['last_order_id'] = $order_id;
        $_SESSION['total_amount'] = $total;
        header("Location: momo_atm_payment.php?order_id=" . $order_id);
        exit;
    } else {
        header("Location: order_success.php");
        exit;
    }

    exit;
}
?>

<div class="checkout-container">
    <h2>🧾 Xác nhận đơn hàng</h2>
    <div class="content-wrapper">
        <div class="products-section">
            <?php if (!empty($_SESSION['cart'])): ?>
            <h3>Sản phẩm trong đơn hàng:</h3>
            <table>
            <tr>
                <th>Tên sản phẩm</th>
                <th>Giá</th>
                <th>Số lượng</th>
                <th>Tạm tính</th>
            </tr>
            <?php
            $grand_total = 0;
            foreach ($_SESSION['cart'] as $id => $item):
                $stmt = $con->prepare("SELECT name, price, discount FROM products WHERE id = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $product = $stmt->get_result()->fetch_assoc();

                $price = $product['price'];
                if ($product['discount'] > 0) {
                    $price *= (1 - $product['discount'] / 100);
                }

                $quantity = is_array($item) ? $item['quantity'] : (int)$item;
                $line_total = $price * $quantity;
                $grand_total += $line_total;
            ?>
                <tr>
                    <td><?= htmlspecialchars($product['name']) ?></td>
                    <td><?= number_format($price, 0, ',', '.') ?>₫</td>
                    <td><?= $quantity ?></td>
                    <td><?= number_format($line_total, 0, ',', '.') ?>₫</td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="3"><strong>Tổng cộng</strong></td>
                <td><strong><?= number_format($grand_total, 0, ',', '.') ?>₫</strong></td>
            </tr>
        </table>
    <?php else: ?>
        <p>Không có sản phẩm nào trong giỏ hàng.</p>
    <?php endif; ?>
        </div>
    
        <div class="shipping-section">
        <h3>Thông tin giao hàng</h3>
        <form method="POST" autocomplete="off" id="checkoutForm">
            <div class="form-group">
                <label>Họ tên:</label>
                <input type="text" name="name" class="form-input" required placeholder="Nhập họ tên">
                <div class="input-status"></div>
            </div>

            <div class="form-group">
                <label>Địa chỉ:</label>
                <textarea name="address" class="form-input" required placeholder="Nhập địa chỉ đầy đủ"></textarea>
                <div class="input-status"></div>
            </div>

            <div class="form-group">
                <label>Số điện thoại:</label>
                <input type="text" name="phone" class="form-input" required placeholder="Nhập số điện thoại">
                <div class="input-status"></div>
            </div>

            <div class="form-group">
                <label>Phương thức thanh toán:</label>
                <select name="payment_method" id="payment_method" class="form-input" required>
            <option value="cod">Thanh toán khi nhận hàng (COD)</option>
            <option value="bank">Chuyển khoản ngân hàng</option>
            <option value="momo">Ví MoMo (ATM/QRCODE)</option>
        </select><br><br>

        <!-- Thông tin ngân hàng (ẩn/hiện) -->
        <div id="bank-info" style="display: none; margin: 15px 0; padding: 15px; background-color: #f7f7f7; border: 1px solid #ccc;">
            <h4>Thông tin chuyển khoản:</h4>
            <p><strong>Ngân hàng:</strong> Vietcombank</p>
            <p><strong>Số tài khoản:</strong> 0123456789</p>
            <p><strong>Chủ tài khoản:</strong> Trà Ngọc Hiển</p>
            <p><strong>Nội dung chuyển khoản:</strong> Thanh toán đơn hàng của <?= $_SESSION['user_name'] ?? 'Khách hàng' ?></p>
        </div>

                <div class="button-group">
                    <button type="submit">✅ Xác nhận đơn hàng</button>
                    <a href="product.php" class="btn">🛍️ Mua thêm</a>
                </div>
            </form>
        </div>
    </div>
</div>

<link rel="stylesheet" href="css/checkout.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="js/checkout.js"></script>

<style>
.my-swal {
    z-index: 9999;
}
.my-swal .swal2-title {
    font-size: 24px;
    color: #1a73e8;
}
.my-swal .swal2-content {
    font-size: 16px;
}
.my-swal .swal2-confirm {
    padding: 12px 24px;
}
.my-swal .swal2-cancel {
    background-color: #6c757d;
}
</style>

<?php include 'footer.php'; ?>