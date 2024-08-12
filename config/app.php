<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');

$debug = ($_ENV['APP_DEBUG'] ?? false) == "true" ? true : false;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', './storage/logs/'.date('d-m-Y').'.log'); 

error_reporting(E_ALL);
libxml_use_internal_errors(true);

// Ghi lỗi cả ở màn hình và file log
if (!$debug) {
    // Chỉ ghi lỗi ở file log
    set_error_handler(function ($errno, $errstr, $errfile, $errline) {
        $log = "[ERROR] [$errno] $errstr - $errfile:$errline" . PHP_EOL;
        error_log($log);
    });
    // Bắt lỗi nghiêm trọng không bắt được khi script shutdown
    register_shutdown_function(function () {
        $error = error_get_last();
        if ($error !== NULL) {
            $log = "[SHUTDOWN ERROR] [{$error['type']}] {$error['message']} - {$error['file']}:{$error['line']}" . PHP_EOL;
            error_log($log);
        }
    });
} 

