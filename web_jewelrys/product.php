<?php
session_start();
ob_start();
include 'admin/config.php';

// Xử lý chỉ cho nút mua ngay
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['buy_now'])) {
    // Kiểm tra đăng nhập
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
        header("Location: user/login.php");
        exit;
    }

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

            // Reset giỏ hàng và thêm sản phẩm mới
            $_SESSION['cart'] = [];
            $_SESSION['cart'][$product_id] = [
                'quantity' => $quantity,
                'price' => $price
            ];

            // Debug thông tin
            error_log('Product ID: ' . $product_id);
            error_log('Cart Session: ' . print_r($_SESSION['cart'], true));

            // Chuyển hướng đến trang checkout
            header("Location: checkout.php");
            exit;
        }
    }
}

include 'header.php';

// Danh mục
$category_result = $con->query("SELECT * FROM categories");

// Lọc danh mục
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$category_name = "Tất cả Sản phẩm";

if ($category_id > 0) {
    $stmt_name = $con->prepare("SELECT name FROM categories WHERE id = ?");
    $stmt_name->bind_param("i", $category_id);
    $stmt_name->execute();
    $stmt_name->bind_result($category_name_db);
    if ($stmt_name->fetch()) {
        $category_name = "Danh mục: " . htmlspecialchars($category_name_db);
    }
    $stmt_name->close();

    $stmt = $con->prepare("SELECT * FROM products WHERE category_id = ?");
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $products = $stmt->get_result();
} else {
    $products = $con->query("SELECT * FROM products");
}
?>

<link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="css/product.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="container">
    <h1>🛍️ <?= $category_name ?></h1>

    <?php if (isset($success_message)): ?>
        <div class="alert-success"><?= $success_message ?></div>
    <?php endif; ?>

    <div class="main-content">
        <!-- Sidebar -->
        <aside class="sidebar">
            <h3>Danh mục</h3>
            <ul>
                <li><a href="product.php">Tất cả</a></li>
                <?php $category_result->data_seek(0); while ($cat = $category_result->fetch_assoc()): ?>
                    <li><a href="product.php?category=<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></a></li>
                <?php endwhile; ?>
            </ul>
        </aside>

        <!-- Product List -->
        <div class="product-list">
            <?php if ($products && $products->num_rows > 0): ?>
                <?php while ($p = $products->fetch_assoc()): 
                    // Sửa đường dẫn ảnh - thêm 'admin/' nếu cần
                    $product_image = (!empty($p['image']) ? 
                        (strpos($p['image'], 'admin/') === 0 ? $p['image'] : 'admin/' . $p['image']) 
                        : 'images/default-product.jpg');
                ?>
                    <div class="product-item">
                        <a href="product_detail.php?id=<?= $p['id'] ?>">
                            <img src="<?= htmlspecialchars($product_image) ?>" alt="<?= htmlspecialchars($p['name']) ?>">
                        </a>
                        <h3><?= htmlspecialchars($p['name']) ?></h3>
                        <p class="desc"><?= htmlspecialchars($p['description']) ?></p>
                        <p>Số lượng: <?= $p['quantity'] ?></p>

                        <?php if (!empty($p['discount']) && $p['discount'] > 0): ?>
                            <p class="price">
                                <del><?= number_format($p['price'], 0, ',', '.') ?>₫</del><br>
                                <span style="color:red; font-weight:bold;">
                                    <?= number_format($p['price'] * (1 - $p['discount'] / 100), 0, ',', '.') ?>₫
                                    (-<?= $p['discount'] ?>%)
                                </span>
                            </p>
                        <?php else: ?>
                            <p class="price"><?= number_format($p['price'], 0, ',', '.') ?>₫</p>
                        <?php endif; ?>

                        <form method="post" class="form-cart">
                            <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                            <label for="quantity">Số lượng:</label>
                            <input type="number" name="quantity" min="1" max="<?= $p['quantity'] ?>" value="1" required>
                            <div class="btn-group">
                                <button type="submit" name="add_to_cart" class="btn-cart">Thêm vào giỏ hàng</button>
                                <button type="submit" name="buy_now" class="btn-buy">Mua ngay</button>
                            </div>
                        </form>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Không có sản phẩm nào.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="js/product.js"></script>
<?php include 'footer.php'; ?>