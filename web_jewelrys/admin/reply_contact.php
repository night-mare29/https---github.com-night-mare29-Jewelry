<?php
include 'config.php';

// Xử lý phản hồi
if (isset($_POST['reply'])) {
    $contact_id = $_POST['id'];
    $reply_text = $_POST['reply_text'];

    // Kiểm tra nếu đã có phản hồi -> cập nhật, chưa có thì thêm mới
    $check = $con->prepare("SELECT id FROM replies WHERE contact_id = ?");
    $check->bind_param("i", $contact_id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        $stmt = $con->prepare("UPDATE replies SET reply = ?, replied_at = NOW() WHERE contact_id = ?");
        $stmt->bind_param("si", $reply_text, $contact_id);
    } else {
        $stmt = $con->prepare("INSERT INTO replies (contact_id, reply) VALUES (?, ?)");
        $stmt->bind_param("is", $contact_id, $reply_text);
    }
    $stmt->execute();
}

// Lấy danh sách góp ý và phản hồi (LEFT JOIN)
$query = "
    SELECT cr.*, r.reply 
    FROM contact_requests cr 
    LEFT JOIN replies r ON cr.id = r.contact_id
    ORDER BY cr.submitted_at DESC
";
$contacts = $con->query($query);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Phản hồi khách hàng</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/categories.css">
    <link rel="stylesheet" href="../css/reply.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="wrapper">
        <div class="categories-container">
            <div class="page-header">
                <h2>📝 Phản hồi góp ý khách hàng</h2>
                <a href="dashboard.php" class="back-link">
                    <i class="fas fa-arrow-left"></i>
                    Về Tổng quan
                </a>
            </div>

            <?php if (isset($_POST['reply'])): ?>
                <div class="alert success">
                    <i class="fas fa-check-circle"></i>
                    Đã gửi phản hồi thành công!
                </div>
            <?php endif; ?>

            <table class="contact-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Họ tên</th>
                        <th>Email</th>
                        <th>Điện thoại</th>
                        <th>Nội dung</th>
                        <th>Phản hồi</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
            <?php while ($row = $contacts->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= htmlspecialchars($row['phone']) ?></td>
                <td><?= nl2br(htmlspecialchars($row['message'])) ?></td>
                <td>
                    <?= !empty($row['reply']) 
                        ? nl2br(htmlspecialchars($row['reply'])) 
                        : 'Chưa phản hồi' ?>
                </td>
                <td>
                    <form method="post" class="reply-form">
                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                        <textarea name="reply_text" placeholder="Nhập nội dung phản hồi..."><?= isset($row['reply']) ? htmlspecialchars($row['reply']) : '' ?></textarea>
                        <button type="submit" name="reply" class="submit-btn">
                            <i class="fas fa-paper-plane"></i>
                            Gửi phản hồi
                        </button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Hiệu ứng mở form phản hồi
        const textareas = document.querySelectorAll('.reply-form textarea');
        textareas.forEach(textarea => {
            textarea.addEventListener('focus', function() {
                this.style.height = '100px';
                this.nextElementSibling.style.opacity = '1';
            });
            
            textarea.addEventListener('blur', function() {
                if (!this.value) {
                    this.style.height = '40px';
                    this.nextElementSibling.style.opacity = '0.7';
                }
            });
        });

        // Animation cho alert
        const alert = document.querySelector('.alert');
        if (alert) {
            setTimeout(() => {
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-20px)';
                setTimeout(() => alert.remove(), 300);
            }, 3000);
        }

        // Hiệu ứng hover cho table rows
        const tableRows = document.querySelectorAll('.contact-table tbody tr');
        tableRows.forEach(row => {
            row.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-2px)';
                this.style.boxShadow = '0 4px 12px rgba(0,0,0,0.1)';
            });
            
            row.addEventListener('mouseleave', function() {
                this.style.transform = 'none';
                this.style.boxShadow = 'none';
            });
        });
    });
    </script>
</body>
</html>
