<?php
/**
 * File cấu hình kết nối Database
 * Sử dụng PDO để kết nối MySQL
 */

// Thông tin kết nối database
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'qlphong');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Cấu hình hệ thống
define('HARD_DELETE', false); // true = xóa hẳn, false = chỉ đặt is_active=0

/**
 * Hàm tạo kết nối PDO
 * Sử dụng Singleton pattern để tránh tạo nhiều kết nối
 */
function getDatabase() {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            $dsn = sprintf(
                "mysql:host=%s;dbname=%s;charset=%s",
                DB_HOST,
                DB_NAME,
                DB_CHARSET
            );
            
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die("Lỗi kết nối database: " . $e->getMessage());
        }
    }
    
    return $pdo;
}