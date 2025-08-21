<?php
session_start();
session_unset();    // Xoá tất cả biến session
session_destroy();  // Hủy session hiện tại

header("Location: ../index.php"); // Quay về trang chủ
exit;
