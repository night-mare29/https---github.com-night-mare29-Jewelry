<?php
session_start();
include '../admin/config.php';

$error_message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? "");
    $email = trim($_POST['email'] ?? "");
    $password = $_POST['password'] ?? "";
    $confirm_password = $_POST['confirm_password'] ?? "";
    $phone = trim($_POST['phone'] ?? "");
    $address = trim($_POST['address'] ?? "");

    // Validate input
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password) || empty($phone) || empty($address)) {
        $error_message = "Vui lòng điền đầy đủ thông tin.";
    } elseif ($password !== $confirm_password) {
        $error_message = "Mật khẩu xác nhận không khớp.";
    } elseif (strlen($password) < 4) {
        $error_message = "Mật khẩu phải có ít nhất 4 ký tự.";
    } else {
        // Check if email already exists
        $stmt = $con->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error_message = "Email này đã được sử dụng.";
        } else {
            // Hash password and insert new user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $con->prepare("INSERT INTO users (username, email, password, phone, address) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $username, $email, $hashed_password, $phone, $address);

            if ($stmt->execute()) {
                echo "<script>
                    window.onload = function() {
                        showNotification({
                            type: 'success',
                            title: 'Đăng ký thành công! 🎉',
                            message: 'Chào mừng bạn đến với Jewelry Store. Vui lòng đăng nhập để tiếp tục.',
                            duration: 3000
                        });
                        setTimeout(function() {
                            window.location.href = 'login.php';
                        }, 3000);
                    }
                </script>";
            } else {
                $error_message = "Có lỗi xảy ra. Vui lòng thử lại sau.";
            }
        }
        $stmt->close();
    }
    $con->close();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Đăng Ký</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/login.css">
    <link rel="stylesheet" href="../css/notification.css">
</head>
<body>
    <main class="login-container" role="main" aria-label="Registration form">
        <h2>Đăng Ký</h2>
        <p>Đã có tài khoản? <a href="login.php">Đăng nhập</a></p>

        <?php if (!empty($error_message)) : ?>
            <script>
                window.onload = function() {
                    showNotification({
                        type: 'error',
                        title: 'Lỗi',
                        message: '<?= htmlspecialchars($error_message) ?>',
                        duration: 5000
                    });
                }
            </script>
        <?php endif; ?>

        <form method="post" class="login-form" novalidate autocomplete="off">
            <div class="input-group">
                <label for="username">Tên người dùng</label>
                <input
                    type="text"
                    id="username"
                    name="username"
                    placeholder="Nhập tên người dùng"
                    required
                    aria-required="true"
                    value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                />
            </div>

            <div class="input-group">
                <label for="email">Địa chỉ email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="you@example.com"
                    required
                    aria-required="true"
                    value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                />
            </div>

            <div class="input-group">
                <label for="password">Mật khẩu</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Nhập 4 ký tự trở lên"
                    required
                    aria-required="true"
                />
            </div>

            <div class="input-group">
                <label for="confirm_password">Xác nhận mật khẩu</label>
                <input
                    type="password"
                    id="confirm_password"
                    name="confirm_password"
                    placeholder="Nhập lại mật khẩu"
                    required
                    aria-required="true"
                />
            </div>

            <div class="input-group">
                <label for="phone">Số điện thoại</label>
                <input
                    type="tel"
                    id="phone"
                    name="phone"
                    placeholder="Nhập số điện thoại"
                    required
                    aria-required="true"
                    value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>"
                />
            </div>

            <div class="input-group">
                <label for="address">Địa chỉ</label>
                <input
                    type="text"
                    id="address"
                    name="address"
                    placeholder="Nhập địa chỉ"
                    required
                    aria-required="true"
                    value="<?= htmlspecialchars($_POST['address'] ?? '') ?>"
                />
            </div>

            <button type="submit" aria-label="Đăng ký">ĐĂNG KÝ</button>
        </form>
    </main>
    <script src="../js/notification.js"></script>
    <script src="../js/login.js"></script>
</body>
</html>
