# Project: Bán sách (Frontend HTML + Backend PHP)

Mô tả ngắn: project mẫu bán sách với frontend tĩnh (`fontend/index.html`) và backend PHP (`backend/chucnang.php`) cung cấp CRUD cho bảng `books`.

Yêu cầu môi trường:
- PHP (>=7.0) để chạy backend (PHP built-in server dùng cho development)
- MySQL / MariaDB cho database

Thiết lập database:
1. Mở MySQL client (hoặc phpMyAdmin) và chạy file SQL:

```bash
mysql -u root -p < backend/create_db.sql
```

2. File sẽ tạo database `testdb` và bảng `books` cùng vài bản ghi ví dụ.

Chạy ứng dụng (development):
1. Mở PowerShell và chuyển tới thư mục project (nơi có `backend` và `fontend`):

```powershell
cd d:\TH_MNM
```

2. Khởi động PHP built-in server ở root của project để có thể truy cập cả frontend và backend cùng origin:

```powershell
php -S localhost:8000 -t .
```

3. Mở trình duyệt:
- Backend (admin CRUD): http://localhost:8000/backend/chucnang.php
- Frontend (tĩnh): http://localhost:8000/fontend/index.html

Ghi chú:
- `backend/chucnang.php` hiện có endpoint JSON tại `?format=json` dùng bởi frontend.
- Nếu cần đổi cấu hình DB (user/password/host), chỉnh trực tiếp trong `backend/chucnang.php`.

An toàn & phát triển:
- Các prepared statements đã được dùng cho các thao tác INSERT/UPDATE/DELETE.
- Không dùng authentication trong bản mẫu này — thêm auth trước khi đưa lên môi trường công khai.
# Project
# Sach
