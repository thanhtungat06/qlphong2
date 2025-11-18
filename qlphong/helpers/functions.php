<?php
/**
 * File chứa các hàm tiện ích dùng chung
 */

/**
 * Bắt đầu session nếu chưa có
 */
function startSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

/**
 * Tạo CSRF token để bảo vệ form
 */
function generateCsrfToken() {
    startSession();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Kiểm tra CSRF token
 */
function verifyCsrfToken() {
    startSession();
    $tokenFromPost = $_POST['csrf_token'] ?? '';
    $tokenFromSession = $_SESSION['csrf_token'] ?? '';
    
    if ($tokenFromPost !== $tokenFromSession) {
        http_response_code(400);
        die('CSRF token không hợp lệ. Vui lòng thử lại.');
    }
}

/**
 * Chuyển hướng đến URL khác
 */
function redirect($url) {
    header("Location: $url");
    exit;
}

/**
 * Mã hóa HTML để tránh XSS
 */
function escapeHtml($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Đặt thông báo flash (hiển thị 1 lần)
 */
function setFlashMessage($message, $type = 'success') {
    startSession();
    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type'] = $type;
}

/**
 * Lấy và xóa thông báo flash
 */
function getFlashMessage() {
    startSession();
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        $type = $_SESSION['flash_type'] ?? 'success';
        unset($_SESSION['flash_message'], $_SESSION['flash_type']);
        return ['message' => $message, 'type' => $type];
    }
    return null;
}

/**
 * Lấy giá trị old input (khi form bị lỗi)
 */
function oldInput($key, $default = '') {
    startSession();
    return $_SESSION['old_input'][$key] ?? $default;
}

/**
 * Lưu old input
 */
function saveOldInput($data) {
    startSession();
    $_SESSION['old_input'] = $data;
}

/**
 * Xóa old input
 */
function clearOldInput() {
    startSession();
    unset($_SESSION['old_input']);
}