<?php
session_start();

// Hiển thị thông báo đăng nhập thành công
if (isset($_SESSION['login_success']) && $_SESSION['login_success'] === true) {
    echo "<script>
        window.onload = function() {
            showNotification({
                type: 'success',
                title: 'Đăng nhập thành công! 🎉',
                message: 'Chào mừng bạn quay trở lại!',
                duration: 3000
            });
        }
    </script>";
    unset($_SESSION['login_success']);
}

include 'header.php';
include 'admin/config.php';
?>

<link rel="stylesheet" href="css/style.css?v=1">
<link rel="stylesheet" href="css/notification.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="container" role="main">

  <section aria-label="Main promotional banner" class="banner">
    <?php
    $banner_sql = "SELECT image_url, title FROM banners WHERE image_url IS NOT NULL AND image_url != '' ORDER BY created_at DESC LIMIT 1";
    $banner_result = $con->query($banner_sql);

    if ($banner_result && $banner_result->num_rows > 0):
      $banner = $banner_result->fetch_assoc();
      $banner_image = !empty($banner['image_url']) ? 'admin/' . $banner['image_url'] : 'images/default-banner.png';
    ?>
      <img src="<?php echo htmlspecialchars($banner_image); ?>" alt="<?php echo htmlspecialchars($banner['title']); ?>" class="main-banner" />
    <?php else: ?>
      <img src="images/default-banner.png" alt="Banner mặc định" class="main-banner" />
    <?php endif; ?>
  </section>

  <!-- Sản phẩm nổi bật -->
  <section aria-labelledby="featured-heading" class="featured-section">
    <h2 id="featured-heading" class="section-title">SẢN PHẨM NỔI BẬT</h2>
    <div class="featured-products">
      <?php
      $sql = "SELECT id, name, image, price, discount FROM products 
              WHERE image IS NOT NULL AND image != '' 
              ORDER BY created_at DESC LIMIT 4";
      $result = $con->query($sql);

      if ($result && $result->num_rows > 0):
        while ($product = $result->fetch_assoc()):
          // Tính giá sau giảm giá
          $discounted_price = $product['price'];
          if ($product['discount'] > 0) {
            $discounted_price = $product['price'] * (1 - $product['discount']/100);
          }
          // Sửa đường dẫn ảnh
          $product_image = !empty($product['image']) ? 'admin/' . $product['image'] : 'images/default-product.jpg';
      ?>
        <div class="product-card" data-product-id="<?php echo $product['id']; ?>">
          <img src="<?php echo htmlspecialchars($product_image); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-image" />
          <div class="product-info">
            <h3 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h3>
            <div class="price-container">
              <?php if ($product['discount'] > 0): ?>
                <span class="original-price"><?php echo number_format($product['price'], 0, ',', '.'); ?>đ</span>
                <span class="discounted-price"><?php echo number_format($discounted_price, 0, ',', '.'); ?>đ</span>
                <span class="discount-percent">-<?php echo $product['discount']; ?>%</span>
              <?php else: ?>
                <span class="final-price"><?php echo number_format($product['price'], 0, ',', '.'); ?>đ</span>
              <?php endif; ?>
            </div>
            <form method="post" class="add-to-cart-form">
              <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
              <input type="hidden" name="quantity" value="1">
              <button type="submit" class="add-to-cart-btn" aria-label="Thêm vào giỏ hàng">
                <i class="fas fa-cart-plus"></i> Thêm vào giỏ
              </button>
            </form>
          </div>
        </div>
      <?php
        endwhile;
      else:
        echo '<p class="no-products">Chưa có sản phẩm nào.</p>';
      endif;
      ?>
    </div>
  </section>

  <!-- Ưu đãi từ nhãn hàng -->
  <section aria-labelledby="brandPromoHeading" class="brand-promo-section">
    <h2 id="brandPromoHeading" class="brand-promo-title">ƯU ĐÃI TỪ NHÃN HÀNG</h2>
    <div class="brand-promo-grid" id="brandPromoGrid">
      <?php
      $promo_sql = "SELECT id, image_url, title FROM promotions 
                    WHERE image_url IS NOT NULL AND image_url != '' 
                    ORDER BY created_at DESC LIMIT 4";
      $promo_result = $con->query($promo_sql);

      if ($promo_result && $promo_result->num_rows > 0):
        while ($promo = $promo_result->fetch_assoc()):
          // Sửa đường dẫn ảnh
          $promo_image = !empty($promo['image_url']) ? 'admin/' . $promo['image_url'] : 'images/default-promo.jpg';
      ?>
        <article class="brand-promo-card" tabindex="0" data-promo-id="<?php echo $promo['id']; ?>">
          <img src="<?php echo htmlspecialchars($promo_image); ?>" 
               alt="<?php echo htmlspecialchars($promo['title']); ?>" 
               class="promo-image" />
          <div class="promo-overlay">
            <h3><?php echo htmlspecialchars($promo['title']); ?></h3>
          </div>
        </article>
      <?php
        endwhile;
      else:
        echo '<p class="no-promos">Không có ưu đãi nào hiện có.</p>';
      endif;
      ?>
    </div>
  </section>

</div>

<script src="js/notification.js"></script>
<script src="js/cart.js"></script>
<script src="script.js"></script>

<?php include 'footer.php'; ?>