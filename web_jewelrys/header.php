<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'admin/config.php';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php if (isset($_SESSION['user_id'])): ?>
    <meta name="user-logged-in" content="true">
    <?php endif; ?>
    <title>Nightmare Jewelry</title>
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.9.1/gsap.min.js"></script>
    <script src="js/script.js" defer></script>
</head>
<body>
    <header class="site-header">
        <!-- Main Header -->
        <div class="main-header">
            <div class="container">
                <!-- Logo -->
                <div class="logo-wrapper">
                    <h1 class="logo">
                        <a href="index.php">
                            <span class="logo-text">NIGHTMARE</span>
                            <span class="logo-sub">JEWELRY</span>
                        </a>
                    </h1>
                </div>

                <!-- Search Bar -->
                <div class="search-bar">
                    <form action="search.php" method="get">
                        <input type="text" name="keyword" placeholder="Tìm kiếm trang sức..." required="">
                        <button type="submit"><i class="fas fa-search"></i></button>
                    </form>
                </div>

                <!-- User Actions -->
                <div class="user-actions">
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-tiktok"></i></a>
                    </div>
                    <a href="cart.php" class="cart-icon">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="cart-label">Giỏ hàng</span>
                    </a>
                    <?php if (isset($_SESSION['username'])): ?>
                        <div class="user-menu">
                            <div class="user-link">
                                <i class="fas fa-user"></i>
                                <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                            </div>
                            <div class="dropdown-menu">
                                <div class="dropdown-section">
                                    <a href="profile.php">Tài khoản</a>
                                    <a href="user/logout.php">Đăng xuất</a>
                                </div>
                                <div class="dropdown-section contact-info">
                                    <a href="tel:0982958193"><i class="fas fa-phone"></i> 0982.958.193</a>
                                    <a href="mailto:dueccho245@gmail.com"><i class="fas fa-envelope"></i> dueccho245@gmail.com</a>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="auth-buttons">
                            <span class="auth-link"><a href="user/login.php">Đăng nhập</a></span>
                            <span class="divider">|</span>
                            <span class="auth-link"><a href="user/register.php">Đăng ký</a></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>

        <!-- Navigation -->
        <nav class="main-nav">
            <div class="container">
                <ul class="menu">
                    <li><a href="index.php">Trang Chủ</a></li>
                    <li><a href="about.php">Giới Thiệu</a></li>
                    <li class="menu-item-has-children">
                        <a href="product.php">Sản Phẩm</a>
                        <ul class="sub-menu">
                            <li><a href="product.php">Tất cả Sản phẩm</a></li>
                            <?php
                            // Lấy danh mục từ database
                            $category_result = $con->query("SELECT * FROM categories");
                            while ($category = $category_result->fetch_assoc()) {
                                echo '<li><a href="product.php?category=' . $category['id'] . '">' .
                                     htmlspecialchars($category['name']) . '</a></li>';
                            }
                            ?>
                        </ul>
                    </li>
                    <li><a href="policy.php">Chính Sách</a></li>
                    <li><a href="contact.php">Liên Hệ</a></li>
                </ul>
            </div>
        </nav>
    </header>

