<?php
$con = mysqli_connect("localhost", "root", "", "web_jewelry");

if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit(); // để dừng chương trình nếu lỗi
}

mysqli_set_charset($con, "utf8");
?>
