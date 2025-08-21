<?php
session_start();
include 'config.php';

$edit = null;
$errors = [];
$success = false;

// Xử lý xoá
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = $_GET['delete'];
    $con->query("DELETE FROM banners WHERE id = $id");
    $_SESSION['message'] = "🗑️ Banner đã được xoá.";
    header("Location: banner.php");
    exit;
}

// Xử lý sửa
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $stmt = $con->prepare("SELECT * FROM banners WHERE id = ?");
    $stmt->bind_param("i", $_GET['edit']);
    $stmt->execute();
    $edit = $stmt->get_result()->fetch_assoc();
}

// Xử lý thêm/cập nhật
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $image_url = $edit['image_url'] ?? '';
    $banner_id = $_POST['banner_id'] ?? '';

    // Xử lý upload ảnh nếu có
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

        $filename = time() . '_' . basename($_FILES['image_file']['name']);
        $target_path = $upload_dir . $filename;

        if (!move_uploaded_file($_FILES['image_file']['tmp_name'], $target_path)) {
            $errors[] = "❌ Lỗi khi upload file ảnh.";
        } else {
            $image_url = $target_path;
        }
    }

    if (empty($errors)) {
        if ($banner_id) {
            $stmt = $con->prepare("UPDATE banners SET title = ?, image_url = ? WHERE id = ?");
            $stmt->bind_param("ssi", $title, $image_url, $banner_id);
            $_SESSION['message'] = "✏️ Banner đã được cập nhật.";
        } else {
            $stmt = $con->prepare("INSERT INTO banners (title, image_url) VALUES (?, ?)");
            $stmt->bind_param("ss", $title, $image_url);
            $_SESSION['message'] = "✅ Banner đã được thêm thành công!";
        }

        if (!$stmt->execute()) {
            $errors[] = "❌ Lỗi MySQL: " . $stmt->error;
        } else {
            header("Location: banner.php");
            exit;
        }
    }
}

$banners = $con->query("SELECT * FROM banners ORDER BY id DESC");
?>
<?php include 'sidebar.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Banner</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/banner.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="banner-container">
        <div class="page-header">
            <h2>Quản lý Banner</h2>
            <a href="dashboard.php" class="back-link">
                <i class="fas fa-arrow-left"></i>
                Về Tổng quan
            </a>
        </div>

    <?php if (!empty($_SESSION['message'])): ?>
        <div class="alert success"><?= $_SESSION['message'] ?></div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <?php foreach ($errors as $err): ?>
        <div class="alert error"><?= $err ?></div>
    <?php endforeach; ?>

    <form method="POST" enctype="multipart/form-data" class="banner-form">
        <input type="hidden" name="banner_id" value="<?= $edit['id'] ?? '' ?>">
        
        <div class="input-group">
            <label>Tiêu đề:</label>
            <input type="text" name="title" value="<?= htmlspecialchars($edit['title'] ?? '') ?>" required>
        </div>

        <div class="input-group">
            <label>Hình ảnh:</label>
            <div class="file-preview">
                <?php if ($edit && $edit['image_url']): ?>
                    <img src="<?= $edit['image_url'] ?>" width="150">
                <?php endif; ?>
            </div>
            <div class="file-input-wrapper">
                <input type="file" name="image_file" <?= $edit ? '' : 'required' ?>>
            </div>
        </div>

        <button type="submit" class="submit-btn">
            <i class="fas <?= $edit ? 'fa-save' : 'fa-plus' ?>"></i>
            <?= $edit ? 'Cập nhật banner' : 'Thêm banner' ?>
        </button>
    </form>

    <table class="banner-table">
        <thead>
            <tr>
                <th>Hình ảnh</th>
                <th>Tiêu đề</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php while($b = $banners->fetch_assoc()): ?>
                <tr>
                    <td><img src="<?= $b['image_url'] ?>" width="100"></td>
                    <td><?= htmlspecialchars($b['title']) ?></td>
                    <td>
                        <a href="?edit=<?= $b['id'] ?>" class="action-btn edit-btn">
                            <i class="fas fa-edit"></i> Sửa
                        </a>
                        <a href="?delete=<?= $b['id'] ?>" class="action-btn delete-btn">
                            <i class="fas fa-trash"></i> Xoá
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

    <script src="../js/banner.js"></script>
</body>
</html>
