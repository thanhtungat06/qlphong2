<?php
/*************** CONFIG ***************/
const DB_HOST = '127.0.0.1';
const DB_NAME = 'qlphong';
const DB_USER = 'root';
const DB_PASS = '';
const HARD_DELETE = false; // true = xóa hẳn, false = chỉ đặt is_active=0

session_start();

/*************** DB & CSRF ***************/
function db(): PDO {
    static $pdo;
    if (!$pdo) {
        $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4", DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
    }
    return $pdo;
}
function csrf_token() {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}
function csrf_verify() {
    if (($_POST['csrf'] ?? '') !== ($_SESSION['csrf'] ?? '')) {
        http_response_code(400);
        exit('CSRF token invalid.');
    }
}

/*************** AUTH (giản lược) ***************/
// Ở đây giả sử đã login. Thực tế bạn nên kiểm tra session user role = 'Phòng TB' hay 'Admin'.

/*************** HELPERS ***************/
function redirect($path) { header("Location: $path"); exit; }
function e($s){ return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }
function validate_room($data, &$errors) {
    $errors = [];
    if (!strlen(trim($data['code'] ?? '')))   $errors['code'] = 'Mã phòng là bắt buộc';
    if (!strlen(trim($data['name'] ?? '')))   $errors['name'] = 'Tên phòng là bắt buộc';
    if (!strlen(trim($data['building'] ?? ''))) $errors['building'] = 'Tòa nhà là bắt buộc';
    $cap = $data['capacity'] ?? '';
    if ($cap === '' || !ctype_digit((string)$cap) || (int)$cap < 0) $errors['capacity'] = 'Sức chứa phải là số nguyên ≥ 0';
    if (strlen($data['code'] ?? '') > 50) $errors['code'] = 'Mã quá dài (≤ 50)';
    if (strlen($data['name'] ?? '') > 120) $errors['name'] = 'Tên quá dài (≤ 120)';
    if (strlen($data['building'] ?? '') > 80) $errors['building'] = 'Tòa nhà quá dài (≤ 80)';
    return empty($errors);
}

/*************** ACTION HANDLERS ***************/
$action = $_GET['a'] ?? 'list';

if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $data = [
        'code' => trim($_POST['code'] ?? ''),
        'name' => trim($_POST['name'] ?? ''),
        'capacity' => (int)($_POST['capacity'] ?? 0),
        'building' => trim($_POST['building'] ?? ''),
        'equipment' => trim($_POST['equipment'] ?? ''),
        'is_active' => isset($_POST['is_active']) ? 1 : 0
    ];
    if (validate_room($data, $errs)) {
        try {
            $stmt = db()->prepare("INSERT INTO rooms(code,name,capacity,building,equipment,is_active) VALUES(?,?,?,?,?,?)");
            $stmt->execute([$data['code'],$data['name'],$data['capacity'],$data['building'],$data['equipment'],$data['is_active']]);
            $_SESSION['flash'] = 'Thêm phòng thành công.';
            redirect('index.php');
        } catch (PDOException $e) {
            if ($e->errorInfo[1] == 1062) $errs['code'] = 'Mã phòng đã tồn tại';
            $errors = $errs; $room = $data; $action = 'create_form';
        }
    } else { $errors = $errs; $room = $data; $action = 'create_form'; }
}

if ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $id = (int)($_POST['id'] ?? 0);
    $data = [
        'code' => trim($_POST['code'] ?? ''),
        'name' => trim($_POST['name'] ?? ''),
        'capacity' => (int)($_POST['capacity'] ?? 0),
        'building' => trim($_POST['building'] ?? ''),
        'equipment' => trim($_POST['equipment'] ?? ''),
        'is_active' => isset($_POST['is_active']) ? 1 : 0
    ];
    if (validate_room($data, $errs)) {
        try {
            $stmt = db()->prepare("UPDATE rooms SET code=?, name=?, capacity=?, building=?, equipment=?, is_active=? WHERE id=?");
            $stmt->execute([$data['code'],$data['name'],$data['capacity'],$data['building'],$data['equipment'],$data['is_active'],$id]);
            $_SESSION['flash'] = 'Cập nhật thành công.';
            redirect('index.php');
        } catch (PDOException $e) {
            if ($e->errorInfo[1] == 1062) $errs['code'] = 'Mã phòng đã tồn tại';
            $errors = $errs; $room = $data; $room['id'] = $id; $action = 'edit_form';
        }
    } else { $errors = $errs; $room = $data; $room['id']=$id; $action = 'edit_form'; }
}

if ($action === 'delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $id = (int)($_POST['id'] ?? 0);
    if (HARD_DELETE) {
        $stmt = db()->prepare("DELETE FROM rooms WHERE id=?");
        $stmt->execute([$id]);
    } else {
        $stmt = db()->prepare("UPDATE rooms SET is_active=0 WHERE id=?");
        $stmt->execute([$id]);
    }
    $_SESSION['flash'] = 'Đã xóa phòng.';
    redirect('index.php');
}

/*************** FETCH FORMS ***************/
if ($action === 'create_form') {
    $room = $room ?? ['code'=>'','name'=>'','capacity'=>0,'building'=>'','equipment'=>'','is_active'=>1];
    $errors = $errors ?? [];
}
if ($action === 'edit_form') {
    if (!isset($room)) {
        $id = (int)($_GET['id'] ?? 0);
        $stmt = db()->prepare("SELECT * FROM rooms WHERE id=?");
        $stmt->execute([$id]);
        $room = $stmt->fetch();
        if (!$room) { http_response_code(404); exit('Không tìm thấy phòng.'); }
    }
    $errors = $errors ?? [];
}

/*************** LIST DATA ***************/
if ($action === 'list') {
    $q = trim($_GET['q'] ?? '');
    $where = '1=1';
    $params = [];
    if ($q !== '') {
        $where .= " AND (code LIKE ? OR name LIKE ? OR building LIKE ?)";
        $like = "%$q%";
        $params = [$like,$like,$like];
    }
    $page = max(1, (int)($_GET['page'] ?? 1));
    $per  = 10;
    $offset = ($page-1)*$per;

    $total = db()->prepare("SELECT COUNT(*) FROM rooms WHERE $where");
    $total->execute($params);
    $count = (int)$total->fetchColumn();

    $stmt = db()->prepare("SELECT * FROM rooms WHERE $where ORDER BY id DESC LIMIT $per OFFSET $offset");
    $stmt->execute($params);
    $rooms = $stmt->fetchAll();
}

/*************** VIEW ***************/
?>
<!doctype html>
<html lang="vi">
<head>
<meta charset="utf-8">
<title>Quản lý phòng học</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://unpkg.com/mVP.css">
<style>
.container{max-width:980px;margin:20px auto;padding:0 12px}
.table{width:100%;border-collapse:collapse}
.table th,.table td{border:1px solid #ddd;padding:8px}
.actions form{display:inline}
.error{color:#c00;font-size:.9em}
.badge{padding:2px 6px;border-radius:4px;border:1px solid #ccc}
.badge.on{background:#e7ffe7;border-color:#b6e7b6}
.badge.off{background:#ffe7e7;border-color:#f3b6b6}
</style>
</head>
<body>
<div class="container">
    <h2>Quản lý phòng học</h2>
    <?php if (!empty($_SESSION['flash'])): ?>
        <div class="alert success"><?= e($_SESSION['flash']); unset($_SESSION['flash']); ?></div>
    <?php endif; ?>

    <?php if ($action === 'list'): ?>
        <div style="display:flex;justify-content:space-between;align-items:center;gap:8px">
            <form method="get">
                <input type="hidden" name="a" value="list">
                <input type="text" name="q" value="<?= e($q) ?>" placeholder="Tìm theo mã, tên, tòa">
                <button>Tìm</button>
            </form>
            <a href="?a=create_form" class="button">+ Thêm phòng</a>
        </div>
        <table class="table" style="margin-top:10px">
            <thead>
                <tr>
                    <th>ID</th><th>Mã</th><th>Tên</th><th>Tòa</th><th>Sức chứa</th><th>Thiết bị</th><th>Trạng thái</th><th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rooms as $r): ?>
                <tr>
                    <td><?= e($r['id']) ?></td>
                    <td><?= e($r['code']) ?></td>
                    <td><?= e($r['name']) ?></td>
                    <td><?= e($r['building']) ?></td>
                    <td><?= e($r['capacity']) ?></td>
                    <td><?= e($r['equipment']) ?></td>
                    <td>
                        <?php if ($r['is_active']): ?>
                            <span class="badge on">Đang dùng</span>
                        <?php else: ?>
                            <span class="badge off">Ngừng dùng</span>
                        <?php endif; ?>
                    </td>
                    <td class="actions">
                        <a href="?a=edit_form&id=<?= e($r['id']) ?>">Sửa</a>
                        <form method="post" action="index.php?a=delete" onsubmit="return confirm('Xóa phòng này?');" style="display:inline; margin-left:6px">
                            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                            <input type="hidden" name="id" value="<?= e($r['id']) ?>">
                            <button type="submit">Xóa</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (!$rooms): ?>
                <tr><td colspan="8" style="text-align:center">Không có dữ liệu</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
        <?php
            $pages = max(1, (int)ceil($count/$per));
            if ($pages>1):
        ?>
        <nav>
            <?php for ($i=1; $i<=$pages; $i++): ?>
                <a class="button <?= $i===$page?'primary':'' ?>" href="?a=list&q=<?= urlencode($q) ?>&page=<?= $i ?>"><?= $i ?></a>
            <?php endfor; ?>
        </nav>
        <?php endif; ?>
    <?php endif; ?>

    <?php if ($action === 'create_form' || $action === 'edit_form'): ?>
        <?php $is_edit = $action === 'edit_form'; ?>
        <h3><?= $is_edit ? 'Sửa phòng' : 'Thêm phòng' ?></h3>
        <form method="post" action="index.php?a=<?= $is_edit ? 'update' : 'create' ?>">
            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
            <?php if ($is_edit): ?>
                <input type="hidden" name="id" value="<?= e($room['id']) ?>">
            <?php endif; ?>
            
            <div>
                <label>Mã phòng *</label>
                <input type="text" name="code" value="<?= e($room['code']) ?>" required>
                <?php if (!empty($errors['code'])): ?>
                    <div class="error"><?= e($errors['code']) ?></div>
                <?php endif; ?>
            </div>
            
            <div>
                <label>Tên phòng *</label>
                <input type="text" name="name" value="<?= e($room['name']) ?>" required>
                <?php if (!empty($errors['name'])): ?>
                    <div class="error"><?= e($errors['name']) ?></div>
                <?php endif; ?>
            </div>
            
            <div>
                <label>Tòa nhà *</label>
                <input type="text" name="building" value="<?= e($room['building']) ?>" required>
                <?php if (!empty($errors['building'])): ?>
                    <div class="error"><?= e($errors['building']) ?></div>
                <?php endif; ?>
            </div>
            
            <div>
                <label>Sức chứa *</label>
                <input type="number" min="0" name="capacity" value="<?= e($room['capacity']) ?>" required>
                <?php if (!empty($errors['capacity'])): ?>
                    <div class="error"><?= e($errors['capacity']) ?></div>
                <?php endif; ?>
            </div>
            
            <div>
                <label>Thiết bị kèm theo</label>
                <textarea name="equipment" rows="3"><?= e($room['equipment']) ?></textarea>
            </div>
            
            <div>
                <label>
                    <input type="checkbox" name="is_active" value="1" <?= !empty($room['is_active']) ? 'checked' : '' ?>> 
                    Đang sử dụng
                </label>
            </div>
            
            <div>
                <button type="submit"><?= $is_edit ? 'Cập nhật' : 'Thêm mới' ?></button>
                <a class="button secondary" href="index.php">Hủy</a>
            </div>
        </form>
    <?php endif; ?>
</div>
</body>
</html>
