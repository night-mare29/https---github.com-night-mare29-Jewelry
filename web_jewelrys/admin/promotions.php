<?php
session_start();
include 'config.php';

$edit_mode = false;
$promotion = ['id' => 0, 'title' => '', 'image_url' => ''];

// Xử lý xoá
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = $_GET['delete'];
    $con->query("DELETE FROM promotions WHERE id = $id");
    $_SESSION['message'] = "🗑️ Xoá ưu đãi thành công!";
    header("Location: promotions.php");
    exit;
}

// Xử lý chuyển sang chế độ sửa
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $edit_mode = true;
    $id = $_GET['edit'];
    $stmt = $con->prepare("SELECT * FROM promotions WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $promotion = $result->fetch_assoc();
    if (!$promotion) {
        die("❌ Không tìm thấy ưu đãi.");
    }
}

// Xử lý thêm mới
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_promotion'])) {
    $title = $_POST['title'];
    $image_url = '';

    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        $filename = time() . '_' . basename($_FILES['image_file']['name']);
        $target_path = $upload_dir . $filename;
        move_uploaded_file($_FILES['image_file']['tmp_name'], $target_path);
        $image_url = $target_path;
    } else {
        die("❌ Vui lòng chọn ảnh để thêm.");
    }

    $stmt = $con->prepare("INSERT INTO promotions (title, image_url, created_at) VALUES (?, ?, NOW())");
    $stmt->bind_param("ss", $title, $image_url);
    $stmt->execute();

    $_SESSION['message'] = "✅ Thêm ưu đãi thành công!";
    header("Location: promotions.php");
    exit;
}

// Xử lý cập nhật
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_promotion'])) {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $image_url = $_POST['existing_image'];

    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        $filename = time() . '_' . basename($_FILES['image_file']['name']);
        $target_path = $upload_dir . $filename;
        move_uploaded_file($_FILES['image_file']['tmp_name'], $target_path);
        $image_url = $target_path;
    }

    $stmt = $con->prepare("UPDATE promotions SET title = ?, image_url = ? WHERE id = ?");
    $stmt->bind_param("ssi", $title, $image_url, $id);
    $stmt->execute();

    $_SESSION['message'] = "✅ Cập nhật ưu đãi thành công!";
    header("Location: promotions.php");
    exit;
}

$promotions = $con->query("SELECT * FROM promotions ORDER BY created_at DESC");
?>
<?php include 'sidebar.php'; ?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Ưu đãi</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/promotions.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="promotions-container">
        <div class="page-header">
            <h2>Quản lý Ưu đãi</h2>
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

    <form method="POST" enctype="multipart/form-data" class="promotion-form">
        <input type="hidden" name="id" value="<?= $promotion['id'] ?>">
        
        <div class="form-group">
            <label>Tiêu đề:</label>
            <input type="text" name="title" value="<?= htmlspecialchars($promotion['title']) ?>" required>
        </div>

        <div class="form-group">
            <?php if ($edit_mode): ?>
                <label>Ảnh hiện tại:</label>
                <div class="image-preview">
                    <img src="<?= $promotion['image_url'] ?>" alt="">
                </div>
                <label>Ảnh mới (nếu thay đổi):</label>
                <div class="file-input-wrapper">
                    <input type="file" name="image_file">
                </div>
                <input type="hidden" name="existing_image" value="<?= $promotion['image_url'] ?>">
                <button type="submit" name="update_promotion" class="submit-btn">
                    <i class="fas fa-save"></i>
                    Cập nhật Ưu đãi
                </button>
            <?php else: ?>
                <label>Hình ảnh:</label>
                <div class="image-preview"></div>
                <div class="file-input-wrapper">
                    <input type="file" name="image_file" required>
                </div>
                <button type="submit" name="submit_promotion" class="submit-btn">
                    <i class="fas fa-plus"></i>
                    Thêm Ưu đãi
                </button>
            <?php endif; ?>
        </div>
    </form>

    <h3>Danh sách Ưu đãi</h3>
    <table class="promotions-table">
        <thead>
            <tr>
                <th>Hình ảnh</th>
                <th>Tiêu đề</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php while($p = $promotions->fetch_assoc()): ?>
                <tr>
                    <td><img src="<?= $p['image_url'] ?>" alt=""></td>
                    <td><?= htmlspecialchars($p['title']) ?></td>
                    <td class="action-buttons">
                        <a href="?edit=<?= $p['id'] ?>" class="edit-btn">
                            <i class="fas fa-edit"></i> Sửa
                        </a>
                        <a href="?delete=<?= $p['id'] ?>" class="delete-btn">
                            <i class="fas fa-trash"></i> Xóa
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    </div>
    <script src="../js/promotions.js"></script>

</body>
</html>
