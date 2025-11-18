<?php
/**
 * Controller xử lý các yêu cầu liên quan đến phòng học
 */
class RoomController {
    private $roomModel;
    
    public function __construct() {
        $this->roomModel = new Room();
    }
    
    /**
     * Hiển thị danh sách phòng học
     */
    public function index() {
        $searchKeyword = trim($_GET['q'] ?? '');
        $currentPage = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 10;
        
        // Lấy danh sách phòng
        $rooms = $this->roomModel->getAllRooms($searchKeyword, $currentPage, $perPage);
        
        // Tính tổng số trang
        $totalRooms = $this->roomModel->countRooms($searchKeyword);
        $totalPages = max(1, (int)ceil($totalRooms / $perPage));
        
        // Load view
        require_once 'views/rooms/index.php';
    }
    
    /**
     * Hiển thị form thêm phòng mới
     */
    public function create() {
        $roomData = [
            'code' => '',
            'name' => '',
            'capacity' => 0,
            'building' => '',
            'equipment' => '',
            'is_active' => 1
        ];
        $errors = [];
        
        require_once 'views/rooms/create.php';
    }
    
    /**
     * Xử lý thêm phòng mới
     */
    public function store() {
        verifyCsrfToken();
        
        // Lấy dữ liệu từ form
        $roomData = [
            'code' => trim($_POST['code'] ?? ''),
            'name' => trim($_POST['name'] ?? ''),
            'capacity' => (int)($_POST['capacity'] ?? 0),
            'building' => trim($_POST['building'] ?? ''),
            'equipment' => trim($_POST['equipment'] ?? ''),
            'is_active' => isset($_POST['is_active']) ? 1 : 0
        ];
        
        // Validate dữ liệu
        $errors = validateRoomData($roomData);
        
        if (empty($errors)) {
            // Thêm vào database
            $result = $this->roomModel->createRoom($roomData);
            
            if ($result === true) {
                setFlashMessage('Thêm phòng học thành công!', 'success');
                clearOldInput();
                redirect('index.php');
            } else {
                // Có lỗi từ database (vd: trùng mã)
                $errors['code'] = $result['error'];
            }
        }
        
        // Nếu có lỗi, hiển thị lại form
        if (!empty($errors)) {
            saveOldInput($roomData);
            require_once 'views/rooms/create.php';
        }
    }
    
    /**
     * Hiển thị form chỉnh sửa phòng
     */
    public function edit() {
        $roomId = (int)($_GET['id'] ?? 0);
        
        // Lấy thông tin phòng
        $roomData = $this->roomModel->getRoomById($roomId);
        
        if (!$roomData) {
            http_response_code(404);
            die('Không tìm thấy phòng học này.');
        }
        
        $errors = [];
        require_once 'views/rooms/edit.php';
    }
    
    /**
     * Xử lý cập nhật thông tin phòng
     */
    public function update() {
        verifyCsrfToken();
        
        $roomId = (int)($_POST['id'] ?? 0);
        
        // Lấy dữ liệu từ form
        $roomData = [
            'code' => trim($_POST['code'] ?? ''),
            'name' => trim($_POST['name'] ?? ''),
            'capacity' => (int)($_POST['capacity'] ?? 0),
            'building' => trim($_POST['building'] ?? ''),
            'equipment' => trim($_POST['equipment'] ?? ''),
            'is_active' => isset($_POST['is_active']) ? 1 : 0
        ];
        
        // Validate dữ liệu
        $errors = validateRoomData($roomData);
        
        if (empty($errors)) {
            // Cập nhật database
            $result = $this->roomModel->updateRoom($roomId, $roomData);
            
            if ($result === true) {
                setFlashMessage('Cập nhật thông tin phòng thành công!', 'success');
                clearOldInput();
                redirect('index.php');
            } else {
                // Có lỗi từ database
                $errors['code'] = $result['error'];
            }
        }
        
        // Nếu có lỗi, hiển thị lại form
        if (!empty($errors)) {
            $roomData['id'] = $roomId;
            saveOldInput($roomData);
            require_once 'views/rooms/edit.php';
        }
    }
    
    /**
     * Xử lý xóa phòng
     */
    public function delete() {
        verifyCsrfToken();
        
        $roomId = (int)($_POST['id'] ?? 0);
        
        if ($this->roomModel->deleteRoom($roomId)) {
            setFlashMessage('Xóa phòng học thành công!', 'success');
        } else {
            setFlashMessage('Có lỗi xảy ra khi xóa phòng học!', 'error');
        }
        
        redirect('index.php');
    }
}