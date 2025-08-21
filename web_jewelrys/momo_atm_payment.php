
<?php
session_start();
header('Content-type: text/html; charset=utf-8');

function execPostRequest($url, $data)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data)
    ));
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

// ==== THÔNG TIN THANH TOÁN ==== 
$endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";

$partnerCode = 'MOMOBKUN20180529';
$accessKey = 'klm05TvNBzhg7h7j';
$secretKey = 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa';

$orderId = time() . "";
$requestId = time() . "";
$orderInfo = "Thanh toán đơn hàng MoMo Test";

// Kiểm tra số tiền hợp lệ
$amount = isset($_SESSION['total_amount']) ? (int)$_SESSION['total_amount'] : 0;

// Xác thực số tiền theo giới hạn của MoMo
if ($amount < 10000 || $amount > 50000000) {
    echo "<h2>Lỗi số tiền không hợp lệ</h2>";
    echo "Số tiền thanh toán phải từ 10,000 VND đến 50,000,000 VND<br>";
    echo "Số tiền hiện tại: " . number_format($amount, 0, ',', '.') . " VND<br><br>";
    echo "<p><a href='checkout.php'>Quay lại trang thanh toán</a></p>";
    exit;
}

$redirectUrl = "http://localhost/web_jewelrys/web_jewelrys/order_success.php";
$ipnUrl = "http://localhost/web_jewelrys/web_jewelrys/ipn_momo.php"; // Dummy hoặc xử lý thực
$extraData = "";
$requestType = "payWithATM";

// ==== TẠO CHỮ KÝ ==== 
$rawHash = "accessKey=$accessKey&amount=$amount&extraData=$extraData&ipnUrl=$ipnUrl&orderId=$orderId&orderInfo=$orderInfo&partnerCode=$partnerCode&redirectUrl=$redirectUrl&requestId=$requestId&requestType=$requestType";
$signature = hash_hmac("sha256", $rawHash, $secretKey);

// ==== GỬI DỮ LIỆU ==== 
$data = [
    'partnerCode' => $partnerCode,
    'partnerName' => "Test Store",
    'storeId' => "MomoTestStore",
    'requestId' => $requestId,
    'amount' => $amount,
    'orderId' => $orderId,
    'orderInfo' => $orderInfo,
    'redirectUrl' => $redirectUrl,
    'ipnUrl' => $ipnUrl,
    'lang' => 'vi',
    'extraData' => $extraData,
    'requestType' => $requestType,
    'signature' => $signature
];

// ==== KẾT QUẢ ==== 
try {
    $result = execPostRequest($endpoint, json_encode($data));
    $jsonResult = json_decode($result, true);

    // ==== CHUYỂN HƯỚNG ====
    if (isset($jsonResult['payUrl'])) {
        header('Location: ' . $jsonResult['payUrl']);
        exit;
    } else {
        echo "<h2>Lỗi khi tạo thanh toán MoMo:</h2>";
        if (isset($jsonResult['message'])) {
            echo "Message: " . htmlspecialchars($jsonResult['message']) . "<br>";
        }
        if (isset($jsonResult['localMessage'])) {
            echo "Local Message: " . htmlspecialchars($jsonResult['localMessage']) . "<br>";
        }
        echo "<h3>Response details:</h3>";
        echo "<pre>";
        print_r($jsonResult);
        echo "</pre>";
        echo "<p><a href='checkout.php'>Quay lại trang thanh toán</a></p>";
    }
} catch (Exception $e) {
    echo "<h2>Lỗi kết nối đến MoMo:</h2>";
    echo "Error: " . htmlspecialchars($e->getMessage()) . "<br>";
    echo "<p><a href='checkout.php'>Quay lại trang thanh toán</a></p>";
}
