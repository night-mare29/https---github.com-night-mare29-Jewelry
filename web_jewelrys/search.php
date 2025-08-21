<?php
include 'admin/config.php';

$keyword = $_GET['keyword'] ?? '';
$products = [];

if (!empty($keyword)) {
    $like_keyword = '%' . mb_strtolower($keyword, 'UTF-8') . '%';
    $stmt = $con->prepare("SELECT * FROM products WHERE LOWER(name) LIKE LOWER(?) OR LOWER(description) LIKE LOWER(?)");
    $stmt->bind_param("ss", $like_keyword, $like_keyword);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Kết quả tìm kiếm</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/search.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="search-results">
        <h2>Kết quả tìm kiếm cho: <em><?php echo htmlspecialchars($keyword); ?></em></h2>
        
        <?php if (!empty($products)): ?>
            <div class="product-list">
                <?php foreach ($products as $product): ?>
                    <div class="product-item">
                        <img data-src="admin/<?php echo $product['image']; ?>"
                             src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                             alt="<?php echo htmlspecialchars($product['name']); ?>"
                             loading="lazy">
                        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p class="price-final">
                            <?php
                            $price = $product['price'];
                            $discount = $product['discount'];
                            if ($discount > 0) {
                                $final_price = $price * (1 - $discount / 100);
                                echo number_format($final_price, 0, ',', '.') . "₫";
                                echo "<br><span class='price-original'>" . number_format($price, 0, ',', '.') . "₫</span>";
                            } else {
                                echo number_format($price, 0, ',', '.') . "₫";
                            }
                            ?>
                        </p>
                        <p><?php echo htmlspecialchars($product['description']); ?></p>
                        <p>Số lượng: <?php echo $product['quantity']; ?></p>
                        <a href="product_detail.php?id=<?php echo $product['id']; ?>" class="view-detail">Xem chi tiết</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="no-results">Không tìm thấy sản phẩm nào phù hợp.</p>
        <?php endif; ?>
    </div>

    <?php include 'footer.php'; ?>
    <script src="js/search.js"></script>
</body>
</html>
