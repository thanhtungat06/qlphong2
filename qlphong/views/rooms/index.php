<?php
$pageTitle = 'Danh sách phòng học';
require_once 'views/layout/header.php';
?>

<div class="room-list-page">
    <h2>📋 Danh sách phòng học</h2>
    
    <!-- Form tìm kiếm và nút thêm mới -->
    <div class="toolbar">
        <form method="GET" action="index.php" class="search-form">
            <input type="hidden" name="action" value="index">
            <input 
                type="text" 
                name="q" 
                value="<?= escapeHtml($searchKeyword) ?>" 
                placeholder="Tìm theo mã, tên hoặc tòa nhà..."
                class="search-input"
            >
            <button type="submit" class="btn-search">🔍 Tìm kiếm</button>
            <?php if (!empty($searchKeyword)): ?>
                <a href="index.php" class="btn-clear">✖ Xóa bộ lọc</a>
            <?php endif; ?>
        </form>
        
        <a href="index.php?action=create" class="btn-add">➕ Thêm phòng mới</a>
    </div>
    
    <!-- Bảng danh sách phòng -->
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th width="5%">ID</th>
                    <th width="10%">Mã phòng</th>
                    <th width="20%">Tên phòng</th>
                    <th width="12%">Tòa nhà</th>
                    <th width="8%">Sức chứa</th>
                    <th width="20%">Thiết bị</th>
                    <th width="10%">Trạng thái</th>
                    <th width="15%">Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($rooms)): ?>
                    <tr>
                        <td colspan="8" class="text-center">
                            <em>Không tìm thấy phòng học nào</em>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($rooms as $room): ?>
                        <tr>
                            <td><?= escapeHtml($room['id']) ?></td>
                            <td><strong><?= escapeHtml($room['code']) ?></strong></td>
                            <td><?= escapeHtml($room['name']) ?></td>
                            <td><?= escapeHtml($room['building']) ?></td>
                            <td class="text-center"><?= escapeHtml($room['capacity']) ?> người</td>
                            <td class="equipment-cell">
                                <?= escapeHtml($room['equipment'] ?: 'Không có') ?>
                            </td>
                            <td class="text-center">
                                <?php if ($room['is_active']): ?>
                                    <span class="badge badge-active">✓ Đang dùng</span>
                                <?php else: ?>
                                    <span class="badge badge-inactive">✗ Ngừng dùng</span>
                                <?php endif; ?>
                            </td>
                            <td class="actions">
                                <a href="index.php?action=edit&id=<?= $room['id'] ?>" 
                                   class="btn-edit" 
                                   title="Chỉnh sửa">
                                    ✏️ Sửa
                                </a>
                                
                                <form method="POST" 
                                      action="index.php?action=delete" 
                                      style="display: inline-block;"
                                      onsubmit="return confirmDelete(event)">
                                    <input type="hidden" name="csrf_token" value="<?= escapeHtml(generateCsrfToken()) ?>">
                                    <input type="hidden" name="id" value="<?= $room['id'] ?>">
                                    <button type="submit" class="btn-delete" title="Xóa">
                                        🗑️ Xóa
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Phân trang -->
    <?php if ($totalPages > 1): ?>
        <nav class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="index.php?action=index&q=<?= urlencode($searchKeyword) ?>&page=<?= $i ?>" 
                   class="page-link <?= $i === $currentPage ? 'active' : '' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </nav>
        <p class="pagination-info">
            Trang <?= $currentPage ?> / <?= $totalPages ?> 
            (Tổng <?= $totalRooms ?> phòng)
        </p>
    <?php endif; ?>
</div>

<?php require_once 'views/layout/footer.php'; ?>