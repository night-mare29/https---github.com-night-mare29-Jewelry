<?php
session_start();
include 'config.php';

$edit = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $stmt = $con->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->bind_param("i", $_GET['edit']);
    $stmt->execute();
    $edit = $stmt->get_result()->fetch_assoc();
}

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $con->query("DELETE FROM categories WHERE id = ".$_GET['delete']);
    $_SESSION['message'] = "🗑️ Xoá loại sản phẩm thành công!";
    header("Location: categories.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_category'])) {
    $name = $_POST['category_name'];
    if (!empty($_POST['category_id'])) {
        $stmt = $con->prepare("UPDATE categories SET name=? WHERE id=?");
        $stmt->bind_param("si", $name, $_POST['category_id']);
        $stmt->execute();
        $_SESSION['message'] = "✅ Cập nhật loại sản phẩm thành công!";
    } else {
        $stmt = $con->prepare("INSERT INTO categories (name) VALUES (?)");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $_SESSION['message'] = "✅ Thêm loại sản phẩm mới thành công!";
    }
    header("Location: categories.php");
    exit;
}

$cats = $con->query("
    SELECT c.id, c.name, COUNT(p.id) AS product_count
    FROM categories c LEFT JOIN products p ON c.id=p.category_id
    GROUP BY c.id
");
?>
<?php include 'sidebar.php'; ?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Loại sản phẩm</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/categories.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="categories-container">
        <div class="page-header">
            <h2>Quản lý Loại sản phẩm</h2>
            <a href="dashboard.php" class="back-link">
                <i class="fas fa-arrow-left"></i>
                Về Tổng quan
            </a>
        </div>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert">
            <?= $_SESSION['message'] ?>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <form method="POST" class="category-form">
        <input type="hidden" name="category_id" value="<?= $edit['id'] ?? '' ?>">
        
        <div class="form-group">
            <label>Tên loại sản phẩm:</label>
            <input type="text"
                   name="category_name"
                   value="<?= htmlspecialchars($edit['name'] ?? '') ?>"
                   required
                   placeholder="Nhập tên loại sản phẩm">
        </div>

        <button type="submit" name="submit_category" class="submit-btn">
            <i class="fas <?= $edit ? 'fa-save' : 'fa-plus' ?>"></i>
            <?= $edit ? 'Cập nhật' : 'Thêm mới' ?>
        </button>
    </form>

    <table class="categories-table">
        <thead>
            <tr>
                <th>Tên loại sản phẩm</th>
                <th>Số sản phẩm</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php while($c = $cats->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($c['name']) ?></td>
                <td><span class="product-count"><?= $c['product_count'] ?></span></td>
                <td class="action-buttons">
                    <a href="?edit=<?= $c['id'] ?>" class="edit-btn">
                        <i class="fas fa-edit"></i> Sửa
                    </a>
                    <a href="?delete=<?= $c['id'] ?>" class="delete-btn">
                        <i class="fas fa-trash"></i> Xóa
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    </div>

    <script src="../js/categories.js"></script>
</body>
</html>
