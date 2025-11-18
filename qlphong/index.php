<?php
/**
 * File chính của ứng dụng - Entry Point
 * Xử lý routing và gọi controller tương ứng
 */

// Khởi tạo session
session_start();

// Load các file cần thiết
require_once './config/database.php';
require_once './helpers/functions.php';
require_once './helpers/validation.php';
require_once './models/Room.php';
require_once './controllers/RoomController.php';

// Khởi tạo controller
$controller = new RoomController();

// Lấy action từ URL (mặc định là 'index')
$action = $_GET['action'] ?? 'index';
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Router - Điều hướng đến action tương ứng
switch ($action) {
    case 'index':
        // Hiển thị danh sách phòng
        $controller->index();
        break;
        
    case 'create':
        // Hiển thị form thêm mới
        if ($requestMethod === 'GET') {
            $controller->create();
        }
        break;
        
    case 'store':
        // Xử lý thêm phòng mới
        if ($requestMethod === 'POST') {
            $controller->store();
        } else {
            redirect('index.php?action=create');
        }
        break;
        
    case 'edit':
        // Hiển thị form chỉnh sửa
        if ($requestMethod === 'GET') {
            $controller->edit();
        }
        break;
        
    case 'update':
        // Xử lý cập nhật thông tin phòng
        if ($requestMethod === 'POST') {
            $controller->update();
        } else {
            redirect('index.php');
        }
        break;
        
    case 'delete':
        // Xử lý xóa phòng
        if ($requestMethod === 'POST') {
            $controller->delete();
        } else {
            redirect('index.php');
        }
        break;
        
    default:
        // Action không tồn tại - chuyển về trang chủ
        redirect('index.php');
        break;
}