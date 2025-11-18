<?php
/**
 * File chứa các hàm validate dữ liệu
 */

/**
 * Validate dữ liệu phòng học
 * @param array $data Dữ liệu cần validate
 * @return array Mảng lỗi (rỗng nếu không có lỗi)
 */
function validateRoomData($data) {
    $errors = [];
    
    // Validate mã phòng
    $roomCode = trim($data['code'] ?? '');
    if (empty($roomCode)) {
        $errors['code'] = 'Mã phòng là bắt buộc';
    } elseif (strlen($roomCode) > 50) {
        $errors['code'] = 'Mã phòng không được vượt quá 50 ký tự';
    } elseif (!preg_match('/^[A-Za-z0-9]+$/', $roomCode)) {
        $errors['code'] = 'Mã phòng chỉ được chứa chữ cái và số';
    }
    
    // Validate tên phòng
    $roomName = trim($data['name'] ?? '');
    if (empty($roomName)) {
        $errors['name'] = 'Tên phòng là bắt buộc';
    } elseif (strlen($roomName) > 120) {
        $errors['name'] = 'Tên phòng không được vượt quá 120 ký tự';
    }
    
    // Validate tòa nhà
    $building = trim($data['building'] ?? '');
    if (empty($building)) {
        $errors['building'] = 'Tòa nhà là bắt buộc';
    } elseif (strlen($building) > 80) {
        $errors['building'] = 'Tên tòa nhà không được vượt quá 80 ký tự';
    }
    
    // Validate sức chứa
    $capacity = $data['capacity'] ?? '';
    if ($capacity === '' || $capacity === null) {
        $errors['capacity'] = 'Sức chứa là bắt buộc';
    } elseif (!is_numeric($capacity)) {
        $errors['capacity'] = 'Sức chứa phải là số';
    } elseif ((int)$capacity < 0) {
        $errors['capacity'] = 'Sức chứa phải lớn hơn hoặc bằng 0';
    } elseif ((int)$capacity > 1000) {
        $errors['capacity'] = 'Sức chứa không được vượt quá 1000 người';
    }
    
    // Validate thiết bị (không bắt buộc)
    $equipment = trim($data['equipment'] ?? '');
    if (strlen($equipment) > 1000) {
        $errors['equipment'] = 'Mô tả thiết bị không được vượt quá 1000 ký tự';
    }
    
    return $errors;
}

/**
 * Kiểm tra xem có lỗi validate không
 */
function hasValidationErrors($errors) {
    return !empty($errors);
}

/**
 * Lấy thông báo lỗi cho một trường cụ thể
 */
function getValidationError($errors, $field) {
    return $errors[$field] ?? '';
}