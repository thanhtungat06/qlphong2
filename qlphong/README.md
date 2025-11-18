# HỆ THỐNG QUẢN LÝ PHÒNG HỌC

## 📋 Mô tả dự án
Hệ thống quản lý phòng học được phát triển bằng PHP thuần (không sử dụng framework), cho phép thực hiện các chức năng CRUD (Create, Read, Update, Delete) đối với thông tin phòng học.

## 🎯 Chức năng chính
- ✅ **Xem danh sách** phòng học với phân trang
- 🔍 **Tìm kiếm** phòng theo mã, tên hoặc tòa nhà
- ➕ **Thêm mới** phòng học
- ✏️ **Chỉnh sửa** thông tin phòng
- 🗑️ **Xóa** phòng học (soft delete hoặc hard delete)
- 🔐 **Bảo mật** với CSRF token
- ✔️ **Validation** dữ liệu đầu vào

## 🛠 Công nghệ sử dụng
- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+ / MariaDB
- **Frontend**: HTML5, CSS3
- **CSS Framework**: MVP.css (lightweight)

## 📁 Cấu trúc thư mục

```
QuanLyPhongHoc/
│
├── config/
│   └── database.php          # Cấu hình database
│
├── models/
│   └── Room.php              # Model xử lý dữ liệu phòng
│
├── controllers/
│   └── RoomController.php    # Controller xử lý logic
│
├── views/
│   ├── layout/
│   │   ├── header.php        # Header chung
│   │   └── footer.php        # Footer chung
│   └── rooms/
│       ├── index.php         # Danh sách phòng
│       ├── create.php        # Form thêm mới
│       └── edit.php          # Form chỉnh sửa
│
├── public/
│   ├── css/
│   │   └── style.css         # CSS tùy chỉnh
│   └── index.php             # Entry point
│
├── helpers/
│   ├── functions.php         # Hàm tiện ích
│   └── validation.php        # Hàm validate
│
└── database/
    └── qlphong.sql           # File SQL
```

## ⚙️ Cài đặt

### 1. Yêu cầu hệ thống
- PHP >= 7.4
- MySQL >= 5.7 hoặc MariaDB
- Web server (Apache/Nginx) hoặc PHP built-in server

### 2. Các bước cài đặt

**Bước 1: Clone hoặc tải project về**
```bash
git clone [url-repo]
cd QuanLyPhongHoc
```

**Bước 2: Tạo database**
- Mở phpMyAdmin hoặc MySQL client
- Import file `database/qlphong.sql`
- Hoặc chạy lệnh:
```bash
mysql -u root -p < database/qlphong.sql
```

**Bước 3: Cấu hình kết nối database**
- Mở file `config/database.php`
- Chỉnh sửa thông tin kết nối:
```php
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'qlphong');
define('DB_USER', 'root');
define('DB_PASS', 'your_password');
```

**Bước 4: Chạy ứng dụng**

Với PHP built-in server:
```bash
cd public
php -S localhost:8000
```

Với XAMPP/WAMP:
- Copy thư mục vào `htdocs/` hoặc `www/`
- Truy cập: `http://localhost/QuanLyPhongHoc/public/`

## 🚀 Sử dụng

### Truy cập ứng dụng
```
http://localhost:8000/
```

### Các chức năng chính

**1. Xem danh sách phòng**
- URL: `index.php` hoặc `index.php?action=index`
- Hiển thị danh sách tất cả phòng học
- Hỗ trợ phân trang (10 phòng/trang)

**2. Tìm kiếm phòng**
- Nhập từ khóa vào ô tìm kiếm
- Tìm theo: Mã phòng, Tên phòng, Tòa nhà

**3. Thêm phòng mới**
- URL: `index.php?action=create`
- Điền đầy đủ thông tin
- Click "Thêm phòng"

**4. Chỉnh sửa phòng**
- Click nút "Sửa" ở phòng cần chỉnh sửa
- URL: `index.php?action=edit&id={id}`
- Cập nhật thông tin và click "Cập nhật"

**5. Xóa phòng**
- Click nút "Xóa" ở phòng cần xóa
- Xác nhận trong hộp thoại
- Mặc định: Soft delete (đặt `is_active = 0`)

## 📊 Cấu trúc Database

### Bảng `rooms`
| Cột | Kiểu | Mô tả |
|-----|------|-------|
| id | INT | Khóa chính, tự tăng |
| code | VARCHAR(50) | Mã phòng (unique) |
| name | VARCHAR(120) | Tên phòng |
| capacity | INT | Sức chứa |
| building | VARCHAR(80) | Tòa nhà |
| equipment | TEXT | Thiết bị kèm theo |
| is_active | TINYINT(1) | Trạng thái (1=đang dùng, 0=ngừng) |
| created_at | TIMESTAMP | Thời gian tạo |
| updated_at | TIMESTAMP | Thời gian cập nhật |

## 🔒 Bảo mật

- ✅ CSRF Protection cho mọi form
- ✅ Prepared Statements (PDO) chống SQL Injection
- ✅ HTML Escaping chống XSS
- ✅ Validation dữ liệu đầu vào
- ✅ Error handling đúng cách

## 🎨 Đặc điểm nổi bật

### 1. Code sạch và dễ đọc
- Đặt tên biến rõ ràng, có ý nghĩa
- Comment đầy đủ cho các hàm quan trọng
- Tuân thủ coding convention

### 2. Kiến trúc MVC
- Model: Xử lý logic database
- View: Hiển thị giao diện
- Controller: Điều phối giữa Model và View

### 3. Responsive Design
- Tương thích với mobile, tablet, desktop
- Sử dụng CSS Grid và Flexbox

### 4. User Experience
- Thông báo flash message sau mỗi hành động
- Xác nhận trước khi xóa
- Hiển thị lỗi validation rõ ràng
- Loading state và error handling

## 🐛 Xử lý lỗi thường gặp

### Lỗi: Cannot connect to database
**Giải pháp:**
- Kiểm tra MySQL đã chạy chưa
- Kiểm tra thông tin trong `config/database.php`
- Kiểm tra quyền truy cập database

### Lỗi: CSRF token invalid
**Giải pháp:**
- Xóa cookies và thử lại
- Kiểm tra session đã được khởi tạo

### Lỗi: Page not found
**Giải pháp:**
- Kiểm tra URL có đúng không
- Kiểm tra file index.php có trong thư mục public/

## 📝 Tùy chỉnh

### Thay đổi số phòng mỗi trang
Mở `controllers/RoomController.php`, sửa dòng:
```php
$perPage = 10; // Thay đổi số này
```

### Thay đổi chế độ xóa
Mở `config/database.php`, sửa:
```php
define('HARD_DELETE', false); // false = soft delete, true = hard delete
```

## 👨‍💻 Tác giả
- **Tên**: [Tên sinh viên]
- **MSSV**: [Mã số sinh viên]
- **Lớp**: [Lớp]
- **Email**: [Email]

## 📄 License
Dự án này được phát triển cho mục đích học tập.

## 📞 Hỗ trợ
Nếu có vấn đề, vui lòng tạo issue hoặc liên hệ qua email.

---