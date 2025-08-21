<?php
session_start();
include '../admin/config.php'; // Kết nối cơ sở dữ liệu

$error_message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? "");
    $password = $_POST['password'] ?? "";

    if (empty($email) || empty($password)) {
        $error_message = "Vui lòng nhập đầy đủ email và mật khẩu.";
    } else {
        // Kiểm tra tài khoản admin (4 trường: id, username, email, password)
        $stmt = $con->prepare("SELECT * FROM admin WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();

            if (password_verify($password, $row['password'])) {
                $_SESSION['admin_id'] = $row['id'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['email'] = $row['email'];
                $_SESSION['role'] = 'admin';
                header("Location: ../admin/dashboard.php");
                exit();
            } else {
                $error_message = "Email hoặc mật khẩu không đúng.";
            }
        } else {
            // Kiểm tra tài khoản user (nhiều hơn 4 trường)
            $stmt->close();
            $stmt = $con->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $row = $result->fetch_assoc();

                if (password_verify($password, $row['password'])) {
                    $_SESSION['user_id'] = $row['id'];
                    $_SESSION['username'] = $row['username'];
                    $_SESSION['email'] = $row['email'];
                    $_SESSION['role'] = 'user';
                    $_SESSION['login_success'] = true;
                    header("Location: ../index.php");
                    exit();
                } else {
                    $error_message = "Email hoặc mật khẩu không đúng.";
                }
            } else {
                $error_message = "Email hoặc mật khẩu không đúng.";
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
    <title>Đăng Nhập</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/login.css">
    <link rel="stylesheet" href="../css/notification.css">
</head>
<body>
    <main class="login-container" role="main" aria-label="Login form">
        <h2>Đăng Nhập</h2>
        <p>Chưa có tài khoản? <a href="register.php">Đăng ký</a></p>

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
                <label for="email">Địa chỉ email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="you@example.com"
                    required
                    aria-required="true"
                    autocomplete="email"
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
                    autocomplete="current-password"
                />
            </div>

            <div class="remember-me">
                <input type="checkbox" id="remember" name="remember" />
                <label for="remember">Ghi nhớ đăng nhập</label>
            </div>

            <button type="submit" aria-label="Đăng nhập">ĐĂNG NHẬP</button>
        </form>
    </main>
    <script src="../js/notification.js"></script>
    <script src="../js/login.js"></script>
</body>
</html>
