<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Quản lý phòng học' ?></title>
    
    <!-- CSS Framework -->
    <link rel="stylesheet" href="https://unpkg.com/mvp.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
    <header>
        <nav>
            <h1>🏫 Hệ thống Quản lý Phòng học</h1>
            <ul>
                <li><a href="index.php">Danh sách phòng</a></li>
                <li><a href="index.php?action=create">Thêm phòng mới</a></li>
            </ul>
        </nav>
    </header>
    
    <main>
        <div class="container">
            <?php 
            // Hiển thị thông báo flash
            $flashMessage = getFlashMessage();
            if ($flashMessage): 
            ?>
                <div class="alert alert-<?= escapeHtml($flashMessage['type']) ?>">
                    <?= escapeHtml($flashMessage['message']) ?>
                </div>
            <?php endif; ?>