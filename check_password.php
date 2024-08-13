<?php

// Mật khẩu đúng
$correctPassword = "Asfycode@2022";

// Số lần thử tối đa
$maxAttempts = 5;

// Biến đếm số lần thử
$attempts = 0;
do {
    // Yêu cầu người dùng nhập mật khẩu
    echo "Vui lòng nhập mật khẩu để tải thư viện: ";
    $password = trim(fgets(STDIN));

    // Kiểm tra mật khẩu
    if ($password === $correctPassword) {
        echo "Mật khẩu chính xác. Tiếp tục cài đặt...\n";
        break;
    } else {
        $attempts++;
        echo "Mật khẩu sai. Bạn còn " . ($maxAttempts - $attempts) . " lần thử.\n";
    }

    if ($attempts >= $maxAttempts) {
        echo "Bạn đã hết lượt thử, vui lòng thử lại sau!.\n";
        exit(1);
    }

} while ($attempts < $maxAttempts);

unlink('check_password.php');
