<?php
session_start();
include 'admin/config.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Lấy thông tin người dùng
$stmt = $con->prepare("SELECT username, email, phone, address FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    die("Không tìm thấy thông tin người dùng.");
}

// Xử lý cập nhật thông tin người dùng
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $phone    = trim($_POST['phone'] ?? '');
    $address  = trim($_POST['address'] ?? '');

    // Giữ lại dữ liệu cũ nếu không nhập
    $username = $username !== '' ? $username : $user['username'];
    $email    = $email !== '' ? $email : $user['email'];
    $phone    = $phone !== '' ? $phone : $user['phone'];
    $address  = $address !== '' ? $address : $user['address'];

    // Cập nhật vào database
    $update_stmt = $con->prepare("UPDATE users SET username=?, email=?, phone=?, address=? WHERE id=?");
    $update_stmt->bind_param("ssssi", $username, $email, $phone, $address, $user_id);
    $update_stmt->execute();

    $success = "Cập nhật thông công!";

    // Cập nhật lại biến $user để hiển thị thông tin mới
    $user['username'] = $username;
    $user['email']    = $email;
    $user['phone']    = $phone;
    $user['address']  = $address;
}

include 'header.php';
?>

<link rel="stylesheet" href="css/profile.css">

<div class="container">
    <div class="profile-section">
        <h2>🧑 Thông tin khách hàng</h2>

        <?php if (isset($success)): ?>
            <div class="success" role="alert">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <!-- Bảng hiển thị thông tin khách hàng -->
        <table class="info-table">
            <tr>
                <th>Họ tên</th>
                <td><?= htmlspecialchars($user['username']) ?></td>
            </tr>
            <tr>
                <th>Email</th>
                <td><?= htmlspecialchars($user['email']) ?></td>
            </tr>
            <tr>
                <th>Số điện thoại</th>
                <td><?= htmlspecialchars($user['phone']) ?></td>
            </tr>
            <tr>
                <th>Địa chỉ</th>
                <td><?= htmlspecialchars($user['address']) ?></td>
            </tr>
        </table>

        <button class="btn-update" type="button">Cập nhật thông tin</button>

        <!-- Form cập nhật thông tin -->
        <form method="POST" id="updateForm" style="display:none;" autocomplete="off" novalidate>
            <div class="form-group">
                <label for="username">Họ tên:</label>
                <input type="text" id="username" name="username" placeholder="Nhập họ tên mới">
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" placeholder="Nhập email mới">
            </div>

            <div class="form-group">
                <label for="phone">Số điện thoại:</label>
                <input type="text" id="phone" name="phone" placeholder="Nhập số điện thoại mới">
            </div>

            <div class="form-group">
                <label for="address">Địa chỉ:</label>
                <input type="text" id="address" name="address" placeholder="Nhập địa chỉ mới">
            </div>

            <button type="submit">💾 Lưu thay đổi</button>
        </form>
    </div>

    <div class="orders-section">
        <h2>📦 Đơn hàng của bạn</h2>

        <?php
        // Lấy đơn hàng của khách hàng
        $order_stmt = $con->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC");
        $order_stmt->bind_param("i", $user_id);
        $order_stmt->execute();
        $order_result = $order_stmt->get_result();
        ?>

        <?php if ($order_result->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="orders-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Ngày đặt</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th>Địa chỉ giao hàng</th>
                            <th>Phương thức thanh toán</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($order = $order_result->fetch_assoc()): ?>
                            <tr>
                                <td data-label="ID"><?= htmlspecialchars($order['id']) ?></td>
                                <td data-label="Ngày đặt"><?= date('d/m/Y H:i', strtotime($order['order_date'])) ?></td>
                                <td data-label="Tổng tiền"><?= number_format($order['total'], 0, ',', '.') ?> đ</td>
                                <td data-label="Trạng thái">
                                    <span class="status-badge status-<?= strtolower($order['status']) ?>">
                                        <?= htmlspecialchars($order['status']) ?>
                                    </span>
                                </td>
                                <td data-label="Địa chỉ giao hàng"><?= htmlspecialchars($order['shipping_address']) ?></td>
                                <td data-label="Phương thức thanh toán"><?= htmlspecialchars($order['payment_method']) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="no-orders">
                <p>Bạn chưa có đơn hàng nào.</p>
                <a href="product.php" class="btn-shop">🛍️ Mua sắm ngay</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="js/profile.js"></script>
<?php include 'footer.php'; ?>
