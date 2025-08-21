<?php include 'header.php'; ?>
<link rel="stylesheet" href="css/order_success.css">

<div class="success-container">
    <div class="success-card">
        <div class="success-icon">
            <svg viewBox="0 0 24 24" class="checkmark">
                <path class="checkmark__circle" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"/>
                <path class="checkmark__check" d="M9.75 15.1l-3.5-3.5c-.2-.2-.2-.5 0-.7s.5-.2.7 0l2.8 2.8 6.3-6.3c.2-.2.5-.2.7 0s.2.5 0 .7l-7 7z"/>
            </svg>
        </div>
        
        <h2>Đặt hàng thành công! 🎉</h2>
        
        <div class="success-message">
            <p>Cảm ơn bạn đã đặt hàng. Chúng tôi sẽ liên hệ để xác nhận và giao hàng sớm nhất có thể.</p>
            <p class="order-info">Mã đơn hàng của bạn: <strong>#<?php echo rand(100000, 999999); ?></strong></p>
        </div>

        <div class="next-steps">
            <h3>Các bước tiếp theo</h3>
            <ul>
                <li>✉️ Bạn sẽ nhận được email xác nhận đơn hàng</li>
                <li>📞 Chúng tôi sẽ gọi điện xác nhận trong vòng 24h</li>
                <li>🚚 Đơn hàng sẽ được giao trong 2-3 ngày làm việc</li>
            </ul>
        </div>

        <div class="action-buttons">
            <a href="index.php" class="btn home-btn">🏠 Về trang chủ</a>
            <a href="product.php" class="btn shop-btn">🛍️ Tiếp tục mua sắm</a>
        </div>
    </div>
</div>

<script src="js/order_success.js"></script>
<?php include 'footer.php'; ?>
<?php
session_start();
include 'header.php';
?>

<link rel="stylesheet" href="css/order_success.css">

<div class="container" style="padding: 50px; text-align: center;">
    <h2 style="color: #28a745;">✅ Đặt hàng thành công!</h2>

    <?php if (isset($_SESSION['pending_order_id']) && isset($_SESSION['pending_total'])): ?>
        <p>Cảm ơn bạn đã đặt hàng. Mã đơn hàng của bạn là <strong>#<?= $_SESSION['pending_order_id'] ?></strong>.</p>
        <p>Tổng thanh toán: <strong><?= number_format($_SESSION['pending_total'], 0, ',', '.') ?>₫</strong></p>
        <p>Chúng tôi sẽ liên hệ để xác nhận và giao hàng trong thời gian sớm nhất.</p>
    <?php else: ?>
        <p>Cảm ơn bạn đã mua sắm. Chúng tôi sẽ xử lý đơn hàng của bạn sớm nhất.</p>
    <?php endif; ?>

    <br>
    <a href="index.php" class="btn" style="padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px;">🏠 Về trang chủ</a>
</div>

<?php
// Xoá giỏ hàng và thông tin đơn hàng khỏi session
unset($_SESSION['cart']);
unset($_SESSION['pending_order_id']);
unset($_SESSION['pending_total']);

include 'footer.php';
?>