<?php
session_start();
include 'config.php';

$error_message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? "");
    $password = $_POST['password'] ?? "";

    if (empty($email) || empty($password)) {
        $error_message = "Vui lòng nhập đầy đủ email và mật khẩu.";
    } else {
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
                $_SESSION['show_success'] = true;
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            } else {
                $error_message = "Email hoặc mật khẩu không đúng.";
            }
        } else {
            $error_message = "Email hoặc mật khẩu không đúng.";
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
    <title>Đăng Nhập Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/admin_login.css">
    <link rel="stylesheet" href="../css/notification.css">
</head>
<body>
    <main class="login-container" role="main" aria-label="Admin login form">
        <h2>Đăng Nhập Quản Trị</h2>

        <?php if (isset($_SESSION['show_success'])) : ?>
            <script>
                window.onload = function() {
                    showNotification({
                        type: 'success',
                        title: 'Đăng nhập thành công! 🎉',
                        message: 'Chào mừng <?= htmlspecialchars($_SESSION['username']) ?> quay trở lại!',
                        duration: 3000
                    });
                    setTimeout(function() {
                        window.location.href = 'dashboard.php';
                    }, 3000);
                }
            </script>
            <?php unset($_SESSION['show_success']); ?>
        <?php endif; ?>
        
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
                    placeholder="admin@example.com"
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
                    placeholder="Nhập mật khẩu"
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
    <script src="../js/admin_login.js"></script>
</body>
</html>
