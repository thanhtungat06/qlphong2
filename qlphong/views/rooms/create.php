<?php
$pageTitle = 'Thêm phòng học mới';
require_once 'views/layout/header.php';
?>

<div class="form-page">
    <h2>➕ Thêm phòng học mới</h2>
    
    <form method="POST" action="index.php?action=store" class="room-form">
        <!-- CSRF Token -->
        <input type="hidden" name="csrf_token" value="<?= escapeHtml(generateCsrfToken()) ?>">
        
        <!-- Mã phòng -->
        <div class="form-group <?= !empty($errors['code']) ? 'has-error' : '' ?>">
            <label for="code">
                Mã phòng <span class="required">*</span>
            </label>
            <input 
                type="text" 
                id="code" 
                name="code" 
                value="<?= escapeHtml($roomData['code']) ?>"
                placeholder="Ví dụ: P101, A201, B305"
                required
                maxlength="50"
            >
            <?php if (!empty($errors['code'])): ?>
                <span class="error-message">❌ <?= escapeHtml($errors['code']) ?></span>
            <?php endif; ?>
            <small class="form-hint">Chỉ sử dụng chữ cái và số, tối đa 50 ký tự</small>
        </div>
        
        <!-- Tên phòng -->
        <div class="form-group <?= !empty($errors['name']) ? 'has-error' : '' ?>">
            <label for="name">
                Tên phòng <span class="required">*</span>
            </label>
            <input 
                type="text" 
                id="name" 
                name="name" 
                value="<?= escapeHtml($roomData['name']) ?>"
                placeholder="Ví dụ: Phòng thí nghiệm Hóa học"
                required
                maxlength="120"
            >
            <?php if (!empty($errors['name'])): ?>
                <span class="error-message">❌ <?= escapeHtml($errors['name']) ?></span>
            <?php endif; ?>
        </div>
        
        <!-- Tòa nhà -->
        <div class="form-group <?= !empty($errors['building']) ? 'has-error' : '' ?>">
            <label for="building">
                Tòa nhà <span class="required">*</span>
            </label>
            <input 
                type="text" 
                id="building" 
                name="building" 
                value="<?= escapeHtml($roomData['building']) ?>"
                placeholder="Ví dụ: Tòa A, Tòa B, Tòa C"
                required
                maxlength="80"
            >
            <?php if (!empty($errors['building'])): ?>
                <span class="error-message">❌ <?= escapeHtml($errors['building']) ?></span>
            <?php endif; ?>
        </div>
        
        <!-- Sức chứa -->
        <div class="form-group <?= !empty($errors['capacity']) ? 'has-error' : '' ?>">
            <label for="capacity">
                Sức chứa <span class="required">*</span>
            </label>
            <input 
                type="number" 
                id="capacity" 
                name="capacity" 
                value="<?= escapeHtml($roomData['capacity']) ?>"
                min="0"
                max="1000"
                required
            >
            <?php if (!empty($errors['capacity'])): ?>
                <span class="error-message">❌ <?= escapeHtml($errors['capacity']) ?></span>
            <?php endif; ?>
            <small class="form-hint">Số người tối đa có thể chứa (0-1000)</small>
        </div>
        
        <!-- Thiết bị -->
        <div class="form-group <?= !empty($errors['equipment']) ? 'has-error' : '' ?>">
            <label for="equipment">
                Thiết bị kèm theo
            </label>
            <textarea 
                id="equipment" 
                name="equipment" 
                rows="4"
                placeholder="Ví dụ: Máy chiếu, máy lạnh, bảng thông minh, micro..."
                maxlength="1000"
            ><?= escapeHtml($roomData['equipment']) ?></textarea>
            <?php if (!empty($errors['equipment'])): ?>
                <span class="error-message">❌ <?= escapeHtml($errors['equipment']) ?></span>
            <?php endif; ?>
            <small class="form-hint">Mô tả các thiết bị có trong phòng (không bắt buộc)</small>
        </div>
        
        <!-- Trạng thái -->
        <div class="form-group">
            <label class="checkbox-label">
                <input 
                    type="checkbox" 
                    name="is_active" 
                    value="1"
                    <?= $roomData['is_active'] ? 'checked' : '' ?>
                >
                <span>Phòng đang sử dụng</span>
            </label>
            <small class="form-hint">Bỏ chọn nếu phòng tạm ngừng sử dụng</small>
        </div>
        
        <!-- Buttons -->
        <div class="form-actions">
            <button type="submit" class="btn-submit">
                ✓ Thêm phòng
            </button>
            <a href="index.php" class="btn-cancel">
                ✗ Hủy bỏ
            </a>
        </div>
    </form>
</div>

<?php require_once 'views/layout/footer.php'; ?>