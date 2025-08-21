<?php
session_start();
include 'admin/config.php';

// Khởi tạo giỏ hàng nếu chưa có
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Hàm lấy thông tin sản phẩm từ DB
function getProduct($id, $con) {
    $stmt = $con->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Xử lý thêm sản phẩm vào giỏ hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $product_id = (int)$_POST['product_id'];
    $quantity = max(1, (int)$_POST['quantity']);

    $product = getProduct($product_id, $con);
    if ($product) {
        // Tính giá sau giảm
        $price = $product['price'];
        if ($product['discount'] > 0) {
            $price *= (1 - $product['discount'] / 100);
        }

        // Nếu sản phẩm chưa có trong giỏ
        if (!isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id] = [
                'quantity' => $quantity,
                'price' => $price
            ];
        } else {
            $_SESSION['cart'][$product_id]['quantity'] += $quantity;
        }

        // Lưu tên sản phẩm vừa thêm để hiển thị
        $_SESSION['just_added'] = [
            'name' => $product['name'],
            'quantity' => $quantity
        ];
    }

    header("Location: cart.php");
    exit;
}

// Xử lý xóa sản phẩm
if (isset($_GET['remove'])) {
    $remove_id = (int)$_GET['remove'];
    unset($_SESSION['cart'][$remove_id]);
    header("Location: cart.php");
    exit;
}

// Xử lý cập nhật số lượng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_qty'])) {
    $pid = (int)$_POST['product_id'];
    $qty = max(1, (int)$_POST['quantity']);
    if (isset($_SESSION['cart'][$pid])) {
        $_SESSION['cart'][$pid]['quantity'] = $qty;
    }
    header("Location: cart.php");
    exit;
}

include 'header.php';
?>

<link rel="stylesheet" href="css/cart.css">

<div class="container">
    <h2>🛒 Giỏ hàng của bạn</h2>

    <?php if (isset($_SESSION['just_added'])): 
        $just = $_SESSION['just_added']; ?>
        <div class="alert-success">
            ✅ Đã thêm <strong><?php echo htmlspecialchars($just['name']); ?></strong> (SL: <?php echo $just['quantity']; ?>) vào giỏ hàng.
        </div>
        <?php unset($_SESSION['just_added']); ?>
    <?php endif; ?>

    <?php if (empty($_SESSION['cart'])): ?>
        <div class="empty-cart">
            <p>Giỏ hàng đang trống.</p>
            <a href="product.php" class="btn">🛍️ Mua thêm sản phẩm</a>
        </div>
        </div>
        <?php include 'footer.php'; exit; endif; ?>

    <div class="cart-table-container">
        <table>
            <thead>
                <tr>
                    <th>Sản phẩm</th>
                    <th>Giá</th>
                    <th>Số lượng</th>
                    <th>Tổng</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $total = 0;
                foreach ($_SESSION['cart'] as $product_id => $item):
                    $product = getProduct($product_id, $con);
                    if (!$product) continue;

                    $quantity = $item['quantity'];
                    $price = $item['price'];
                    $line_total = $price * $quantity;
                    $total += $line_total;
                ?>
                <tr class="cart-item">
                    <td class="product-name"><?php echo htmlspecialchars($product['name']); ?></td>
                    <td class="product-price"><?php echo number_format($price, 0, ',', '.'); ?>đ</td>
                    <td>
                        <form method="post" class="quantity-form">
                            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                            <input type="number" name="quantity" min="1" max="<?php echo $product['quantity']; ?>" 
                                   value="<?php echo $quantity; ?>" class="quantity-input">
                            <button type="submit" name="update_qty" class="update-btn">Cập nhật</button>
                        </form>
                    </td>
                    <td class="line-total"><?php echo number_format($line_total, 0, ',', '.'); ?>đ</td>
                    <td>
                        <a href="cart.php?remove=<?php echo $product_id; ?>"
                           class="remove-item">
                            ❌ Xóa
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <h3 class="cart-total">Tổng cộng: <?php echo number_format($total, 0, ',', '.'); ?> VNĐ</h3>

    <div class="action-buttons">
        <a href="product.php" class="btn">🛍️ Mua thêm sản phẩm</a>
        <a href="checkout.php" class="btn checkout-btn">🧾 Tiến hành thanh toán</a>
    </div>
</div>

<script src="js/cart.js"></script>
<?php include 'footer.php'; ?>
