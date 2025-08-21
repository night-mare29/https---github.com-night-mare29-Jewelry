<?php
include 'config.php';

$edit = null;

// Xử lý sửa sản phẩm
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $stmt = $con->prepare("SELECT * FROM products WHERE id=?");
    $stmt->bind_param("i", $_GET['edit']);
    $stmt->execute();
    $edit = $stmt->get_result()->fetch_assoc();
}

// Xử lý xoá sản phẩm
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $product_id = $_GET['delete'];

    // Bắt đầu transaction
    $con->begin_transaction();
    
    try {
        // Xoá tất cả order_items liên quan
        $stmt = $con->prepare("DELETE FROM order_items WHERE product_id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        
        // Xoá sản phẩm
        $stmt = $con->prepare("DELETE FROM products WHERE id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        
        $con->commit();
        $_SESSION['message'] = "🗑️ Xoá sản phẩm thành công!";
    } catch (Exception $e) {
        $con->rollback();
        $_SESSION['error'] = "❌ Lỗi khi xoá sản phẩm: " . $e->getMessage();
    }
    
    header("Location: manage_product.php");
    exit;
}

// Xử lý thêm hoặc cập nhật sản phẩm
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_product'])) {
    $name = $con->real_escape_string($_POST['name']);
    $price = floatval($_POST['price']);
    $desc = $con->real_escape_string($_POST['description']);
    $discount = intval($_POST['discount'] ?? 0);
    $quantity = intval($_POST['quantity'] ?? 0);
    $category_id = intval($_POST['category_id']);
    $image = null;

    // Xử lý ảnh upload
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        // Kiểm tra định dạng ảnh
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = $_FILES['image_file']['type'];
        
        if (!in_array($file_type, $allowed_types)) {
            $_SESSION['error'] = "Chỉ chấp nhận file ảnh (JPEG, PNG, GIF)";
            header("Location: manage_product.php");
            exit;
        }
        
        // Tạo tên file duy nhất
        $ext = pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $ext;
        $target = $upload_dir . $filename;
        
        if (move_uploaded_file($_FILES['image_file']['tmp_name'], $target)) {
            $image = 'uploads/' . $filename; // Lưu đường dẫn tương đối
        } else {
            $_SESSION['error'] = "Lỗi khi upload ảnh";
            header("Location: manage_product.php");
            exit;
        }
    } elseif (!empty($_POST['product_id'])) {
        // Nếu là cập nhật và không có ảnh mới, giữ ảnh cũ
        $stmt = $con->prepare("SELECT image FROM products WHERE id = ?");
        $stmt->bind_param("i", $_POST['product_id']);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $image = $result['image'] ?? null;
    }

    // Nếu không có ảnh (kể cả ảnh cũ), sử dụng ảnh mặc định
    if ($image === null) {
        $image = 'no-image.jpg';
    }

    // Thực hiện insert hoặc update
    try {
        if (!empty($_POST['product_id'])) {
            $stmt = $con->prepare("UPDATE products SET name=?, price=?, description=?, image=?, discount=?, quantity=?, category_id=? WHERE id=?");
            $stmt->bind_param("sdssiiii", $name, $price, $desc, $image, $discount, $quantity, $category_id, $_POST['product_id']);
            $message = "✏️ Cập nhật sản phẩm thành công!";
        } else {
            $stmt = $con->prepare("INSERT INTO products (name, price, description, image, discount, quantity, category_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sdssiii", $name, $price, $desc, $image, $discount, $quantity, $category_id);
            $message = "✅ Thêm sản phẩm mới thành công!";
        }
        
        if ($stmt->execute()) {
            $_SESSION['message'] = $message;
        } else {
            $_SESSION['error'] = "Lỗi database: " . $stmt->error;
        }
    } catch (Exception $e) {
        $_SESSION['error'] = "Lỗi: " . $e->getMessage();
    }
    
    header("Location: manage_product.php");
    exit;
}

// Lấy danh sách sản phẩm và danh mục
$products = $con->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id");
$cats = $con->query("SELECT * FROM categories");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Sản phẩm</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/products.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>
    
    <div class="main-content">
        <div class="page-header">
            <h2>Quản lý Sản phẩm</h2>
            <a href="dashboard.php" class="back-link">
                <i class="fas fa-arrow-left"></i>
                Về Tổng quan
            </a>
        </div>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert success"><?= $_SESSION['message'] ?></div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert error"><?= $_SESSION['error'] ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="product-form">
            <input type="hidden" name="product_id" value="<?= $edit['id'] ?? '' ?>">
            
            <div class="form-group">
                <label>Tên sản phẩm:</label>
                <input type="text" name="name" value="<?= htmlspecialchars($edit['name'] ?? '') ?>" required>
            </div>
            
            <div class="form-group">
                <label>Giá:</label>
                <input type="number" step="0.01" name="price" value="<?= $edit['price'] ?? '' ?>" required>
            </div>
            
            <div class="form-group">
                <label>Giảm giá (%):</label>
                <input type="number" name="discount" min="0" max="100" value="<?= $edit['discount'] ?? 0 ?>">
            </div>
            
            <div class="form-group">
                <label>Số lượng:</label>
                <input type="number" name="quantity" min="0" value="<?= $edit['quantity'] ?? 0 ?>" required>
            </div>
            
            <div class="form-group">
                <label>Mô tả:</label>
                <textarea name="description" required><?= htmlspecialchars($edit['description'] ?? '') ?></textarea>
            </div>
            
            <div class="form-group">
                <label>Danh mục:</label>
                <select name="category_id" required>
                    <?php while ($c = $cats->fetch_assoc()): ?>
                        <option value="<?= $c['id'] ?>" <?= ($edit['category_id'] ?? '') == $c['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['name']) ?>
                        </option>
                    <?php endwhile; ?>
                    <?php $cats->data_seek(0); // Reset pointer để dùng lại ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Ảnh sản phẩm:</label>
                <input type="file" name="image_file" accept="image/*">
                <?php if (!empty($edit['image'])): ?>
                    <div class="current-image">
                        <p>Ảnh hiện tại:</p>
                        <img src="<?= $edit['image'] ?>" class="product-image" alt="Ảnh sản phẩm">
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="form-actions">
                <button type="submit" name="submit_product" class="submit-btn">
                    <i class="fas <?= $edit ? 'fa-save' : 'fa-plus' ?>"></i>
                    <?= $edit ? 'Cập nhật' : 'Thêm mới' ?>
                </button>
                <?php if ($edit): ?>
                    <a href="manage_product.php" class="cancel-btn">Hủy</a>
                <?php endif; ?>
            </div>
        </form>

        <table class="product-table">
            <thead>
                <tr>
                    <th>Ảnh</th>
                    <th>Tên</th>
                    <th>Giá</th>
                    <th>Giảm giá</th>
                    <th>Số lượng</th>
                    <th>Danh mục</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($pr = $products->fetch_assoc()): ?>
                <tr>
                    <td><img src="<?= $pr['image'] ?>" class="product-image" alt="<?= htmlspecialchars($pr['name']) ?>"></td>
                    <td><?= htmlspecialchars($pr['name']) ?></td>
                    <td class="price-column"><?= number_format($pr['price'], 0, ',', '.') ?>đ</td>
                    <td><span class="discount-badge"><?= $pr['discount'] ?>%</span></td>
                    <td><span class="quantity-badge"><?= $pr['quantity'] ?></span></td>
                    <td><span class="category-badge"><?= htmlspecialchars($pr['category_name'] ?? 'Không có') ?></span></td>
                    <td class="actions">
                        <a href="?edit=<?= $pr['id'] ?>" class="edit-btn">
                            <i class="fas fa-edit"></i> Sửa
                        </a>
                        <a href="?delete=<?= $pr['id'] ?>" class="delete-btn">
                            <i class="fas fa-trash"></i> Xóa
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script src="../js/products.js"></script>
</body>
</html>