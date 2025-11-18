-- Tạo database
CREATE DATABASE IF NOT EXISTS qlphong CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE qlphong;

-- Tạo bảng rooms (phòng học)
CREATE TABLE IF NOT EXISTS rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE COMMENT 'Mã phòng (VD: P101, A201)',
    name VARCHAR(120) NOT NULL COMMENT 'Tên phòng học',
    capacity INT NOT NULL DEFAULT 0 COMMENT 'Sức chứa (số người)',
    building VARCHAR(80) NOT NULL COMMENT 'Tòa nhà (VD: Tòa A, Tòa B)',
    equipment TEXT NULL COMMENT 'Thiết bị kèm theo (máy chiếu, máy lạnh...)',
    is_active TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Trạng thái: 1=Đang dùng, 0=Ngừng dùng',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_code (code),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dữ liệu mẫu
INSERT INTO rooms (code, name, capacity, building, equipment, is_active) VALUES
('P101', 'Phòng thí nghiệm Hóa học', 40, 'Tòa A', 'Máy chiếu, tủ hóa chất, bồn rửa', 1),
('P102', 'Phòng học lý thuyết 1', 60, 'Tòa A', 'Máy chiếu, điều hòa', 1),
('B201', 'Phòng máy tính 1', 45, 'Tòa B', '45 máy tính, máy chiếu, điều hòa', 1),
('B202', 'Phòng máy tính 2', 50, 'Tòa B', '50 máy tính, máy in, điều hòa', 1),
('C301', 'Hội trường lớn', 200, 'Tòa C', 'Hệ thống âm thanh, máy chiếu 2K, điều hòa trung tâm', 1),
('A105', 'Phòng thực hành Vật lý', 35, 'Tòa A', 'Thiết bị thí nghiệm vật lý, bàn thí nghiệm', 0);