<?php
/**
 * Model Room - Xử lý logic liên quan đến phòng học
 */
class Room {
    private $db;
    
    public function __construct() {
        $this->db = getDatabase();
    }
    
    /**
     * Lấy danh sách phòng học có phân trang và tìm kiếm
     */
    public function getAllRooms($searchKeyword = '', $page = 1, $perPage = 10) {
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT * FROM rooms WHERE 1=1";
        $params = [];
        
        // Thêm điều kiện tìm kiếm
        if (!empty($searchKeyword)) {
            $sql .= " AND (code LIKE ? OR name LIKE ? OR building LIKE ?)";
            $likeKeyword = "%$searchKeyword%";
            $params = [$likeKeyword, $likeKeyword, $likeKeyword];
        }
        
        $sql .= " ORDER BY id DESC LIMIT ? OFFSET ?";
        $params[] = $perPage;
        $params[] = $offset;
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    /**
     * Đếm tổng số phòng (để tính phân trang)
     */
    public function countRooms($searchKeyword = '') {
        $sql = "SELECT COUNT(*) as total FROM rooms WHERE 1=1";
        $params = [];
        
        if (!empty($searchKeyword)) {
            $sql .= " AND (code LIKE ? OR name LIKE ? OR building LIKE ?)";
            $likeKeyword = "%$searchKeyword%";
            $params = [$likeKeyword, $likeKeyword, $likeKeyword];
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return (int)$result['total'];
    }
    
    /**
     * Lấy thông tin một phòng theo ID
     */
    public function getRoomById($roomId) {
        $sql = "SELECT * FROM rooms WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$roomId]);
        return $stmt->fetch();
    }
    
    /**
     * Thêm phòng học mới
     */
    public function createRoom($data) {
        $sql = "INSERT INTO rooms (code, name, capacity, building, equipment, is_active) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $data['code'],
                $data['name'],
                $data['capacity'],
                $data['building'],
                $data['equipment'],
                $data['is_active']
            ]);
            return true;
        } catch (PDOException $e) {
            // Kiểm tra lỗi trùng mã phòng
            if ($e->errorInfo[1] == 1062) {
                return ['error' => 'Mã phòng đã tồn tại trong hệ thống'];
            }
            return ['error' => 'Có lỗi xảy ra: ' . $e->getMessage()];
        }
    }
    
    /**
     * Cập nhật thông tin phòng học
     */
    public function updateRoom($roomId, $data) {
        $sql = "UPDATE rooms 
                SET code = ?, name = ?, capacity = ?, building = ?, equipment = ?, is_active = ? 
                WHERE id = ?";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $data['code'],
                $data['name'],
                $data['capacity'],
                $data['building'],
                $data['equipment'],
                $data['is_active'],
                $roomId
            ]);
            return true;
        } catch (PDOException $e) {
            // Kiểm tra lỗi trùng mã phòng
            if ($e->errorInfo[1] == 1062) {
                return ['error' => 'Mã phòng đã tồn tại trong hệ thống'];
            }
            return ['error' => 'Có lỗi xảy ra: ' . $e->getMessage()];
        }
    }
    
    /**
     * Xóa phòng học
     */
    public function deleteRoom($roomId) {
        if (HARD_DELETE) {
            // Xóa hẳn khỏi database
            $sql = "DELETE FROM rooms WHERE id = ?";
        } else {
            // Chỉ đặt is_active = 0 (soft delete)
            $sql = "UPDATE rooms SET is_active = 0 WHERE id = ?";
        }
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$roomId]);
    }
    
    /**
     * Kiểm tra mã phòng có tồn tại không (trừ ID hiện tại khi update)
     */
    public function isRoomCodeExists($roomCode, $excludeId = null) {
        $sql = "SELECT COUNT(*) as total FROM rooms WHERE code = ?";
        $params = [$roomCode];
        
        if ($excludeId !== null) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return (int)$result['total'] > 0;
    }
}