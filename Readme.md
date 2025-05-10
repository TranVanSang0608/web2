# 🗂️ Quản Lý Dự Án & Nhiệm Vụ

Một hệ thống quản lý công việc đơn giản nhưng mạnh mẽ, giúp cá nhân và nhóm dễ dàng:
- Tổ chức công việc
- Quản lý dự án
- Theo dõi tiến độ từng nhiệm vụ

> 🌟 Thiết kế giao diện hiện đại với Bootstrap 5, biểu tượng trực quan, phân vùng rõ ràng.  
> 💡 Phù hợp làm project tốt nghiệp hoặc showcase portfolio thực tập Fullstack.

---

## 🚀 Tính năng chính

### 👤 Quản lý tài khoản
- Đăng ký, đăng nhập, xác thực bảo mật CSRF

### 📁 Quản lý dự án
- Tạo / sửa / xóa dự án
- Thêm / xóa thành viên trong dự án
- Xem danh sách dự án cá nhân & được mời tham gia

### ✅ Quản lý nhiệm vụ
- Tạo nhiệm vụ cá nhân hoặc trong dự án
- Gán người phụ trách
- Cập nhật trạng thái (Đang thực hiện / Hoàn thành)
- Xóa nhiệm vụ

---

## 🖥️ Giao diện người dùng (UI)
- Responsive layout với Bootstrap 5
- Biểu tượng: Bootstrap Icons
- Badge màu sắc & phân vùng rõ ràng
- Giao diện nhất quán: card, shadow, spacing hợp lý
- Hiển thị avatar người dùng (có avatar mặc định)

---

## 🛠️ Công nghệ sử dụng

| Thành phần       | Công nghệ               |
|------------------|--------------------------|
| Backend          | Laravel (PHP)            |
| Frontend         | Blade + Bootstrap 5      |
| Cơ sở dữ liệu     | MySQL                    |
| Icon UI          | Bootstrap Icons          |
| Hệ thống session | Laravel Auth & CSRF      |

---

## 📸 Giao diện minh hoạ

| Dashboard | Project | Task |
|----------|---------|------|
| ![Dashboard](./img/home.png) | ![Project](./img/project.png) | ![Task](./img/task.png) |

---

## 📌 Hướng dẫn sử dụng

```bash
# Clone repo
git clone https://github.com/TranVanSang0608/web2.git
cd web2

# Cài đặt composer & npm
composer install
npm install && npm run dev

# Tạo .env & setup DB
cp .env.example .env
php artisan key:generate
php artisan migrate
```


🔮 Định hướng phát triển
Gửi email thông báo / thông báo đẩy

Tính năng báo cáo tiến độ dự án

Tích hợp dark mode

Refactor lại thành SPA (React/Vue + API)

🔗 Repo:
🔗 https://github.com/TranVanSang0608/web2

