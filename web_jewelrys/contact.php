<?php
$response = ""; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'admin/config.php';

    if (!isset($con) || $con === null) {
        die("Lỗi: Không thể kết nối cơ sở dữ liệu.");
    }

    $name = isset($_POST['name']) ? htmlspecialchars(trim($_POST['name'])) : '';
    $email = isset($_POST['email']) ? htmlspecialchars(trim($_POST['email'])) : '';
    $phone = isset($_POST['phone']) ? htmlspecialchars(trim($_POST['phone'])) : '';
    $message = isset($_POST['message']) ? htmlspecialchars(trim($_POST['message'])) : '';
    $submitted_at = date('Y-m-d H:i:s');

    if ($name && $email && $message) {
        $stmt = $con->prepare("INSERT INTO contact_requests (name, email, phone, message, submitted_at) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $email, $phone, $message, $submitted_at);

        if ($stmt->execute()) {
            $response = "<div class='success-msg'>✅ Yêu cầu của bạn đã được gửi thành công!</div>";
        } else {
            $response = "<div class='error-msg'>❌ Lỗi khi lưu dữ liệu: " . $stmt->error . "</div>";
        }

        $stmt->close();
    } else {
        $response = "<div class='error-msg'>⚠️ Vui lòng nhập đầy đủ thông tin bắt buộc.</div>";
    }

    $con->close();
}
?>
<?php
include 'header.php';
?>
<link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="css/contact.css">
<div class="contact-content">
    <h2>Liên Hệ Với Chúng Tôi</h2>

    <?= $response ?>

    <div class="contact-form-container">
        <form method="POST" action="contact.php" id="contactForm" novalidate>
            <div class="form-group">
                <label for="name">Họ và tên*</label>
                <input type="text" id="name" name="name" required>
            </div>

            <div class="form-group">
                <label for="email">Email*</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="phone">Số điện thoại</label>
                <input type="tel" id="phone" name="phone">
            </div>

            <div class="form-group">
                <label for="message">Nội dung tin nhắn*</label>
                <textarea id="message" name="message" rows="5" required></textarea>
            </div>

            <button type="submit">Gửi tin nhắn</button>
        </form>
    </div>
</div>

<script src="js/contact.js"></script>
<?php include 'footer.php'; ?>
