# HỆ THỐNG QUẢN LÝ BOONG CAKE

> **Cập nhật**: 04/12/2024  
> **Trạng thái**: 75% MVP Complete - Production & Distribution Integrated

## I. TỔNG QUAN DỰ ÁN
### 1. Mục tiêu
Xây dựng hệ thống quản lý toàn diện cho chuỗi cửa hàng bán lẻ (Bánh, Nước, Đồ ăn vặt). Hệ thống phân tách rõ ràng môi trường làm việc theo vai trò và thiết bị.

### 2. Phân loại Môi trường & Thiết bị
- **Hệ thống Quản trị (Admin Portal)**:
    -   **Người dùng**: ADMIN.
    -   **Thiết bị**: Laptop / PC.
    -   **Giao diện**: Tối ưu cho màn hình lớn, thao tác chuột/bàn phím, hiển thị nhiều dữ liệu.
-   **Hệ thống Điểm bán (POS Mobile)**:
    -   **Người dùng**: NHÂN VIÊN ĐIỂM BÁN.
    -   **Thiết bị**: Điện thoại thông minh (Mobile).
    -   **Giao diện**: Mobile-first, tối ưu thao tác chạm, icon lớn, đơn giản hóa quy trình.

## II. CÔNG NGHỆ SỬ DỤNG (Cập nhật)
### Backend & Frontend (Monolith)
-   **Framework**: Laravel 10.x (PHP 8.1+).
-   **View Engine**: Blade Templates.
-   **Interactivity**: **Laravel Livewire 3**.
    -   *Lý do*: Giải quyết bài toán cập nhật dữ liệu liên tục (Data Polling) mà không cần triển khai hạ tầng WebSocket phức tạp (Pusher/Reverb).
    -   *Ứng dụng*: Dashboard tự refresh sau mỗi X giây, POS cập nhật trạng thái đơn hàng, Form validation tức thì.
-   **CSS**: Tailwind CSS (Dễ dàng tùy biến Responsive cho Mobile và PC).
-   **Database**: MySQL 8.0+.

### Giải pháp "Real-time"
-   **Dashboard Data**: Sử dụng `wire:poll` của Livewire để tự động refresh số liệu (Doanh thu, Sự cố, Chốt ca) mà không cần reload trang.
-   **Thông báo (Notifications)**: Tích hợp **Lark Webhook** để bắn thông báo tức thì (Sự cố mới, Lệch tiền, Chốt ca xong) vào nhóm chat quản lý.

---

## II.B. TRẠNG THÁI TRIỂN KHAI (04/12/2024)

### ✅ ĐÃ HOÀN THÀNH (75% MVP)

#### 1. Module Sản xuất (Production Management) ⭐ HOÀN CHỈNH
- **Công thức sản xuất**: CRUD, Dynamic ingredients, Auto cost calculation
- **Mẻ sản xuất đa sản phẩm** ⭐ BREAKTHROUGH:
  - 1 mẻ → Nhiều sản phẩm (thay vì 1-1 mapping cũ)
  - Tính định lượng nguyên liệu theo tỷ lệ tự động
  - Layout 2 cột: Products (60%) | Ingredients realtime (40%)
  - QC từng sản phẩm riêng biệt
- **Quản lý nguyên liệu thông minh**:
  - Cảnh báo 3 cấp: ✓ Đủ | ⚠️ Dùng ≥70% | ❌ Thiếu
  - Hiển thị % sử dụng realtime
  - **Tự động trừ kho** khi QC complete

#### 2. Module Phân bổ (Distribution) ⭐ HOÀN CHỈNH
- **Tích hợp với Mẻ sản xuất**:
  - Chọn mẻ đã hoàn thành QC
  - Hiển thị tất cả sản phẩm từ mẻ
  - Phân bổ từng sản phẩm cho điểm bán
  - Validation số lượng khả dụng
  - **Full traceability**: Mẻ SX → Phân bổ → Check-in nhận hàng

#### 3. Module Ca làm việc (Shift Management) ⭐ HOÀN CHỈNH
- **Check-in**: Tự động load hàng từ mẻ sản xuất theo buổi
- **Chốt ca**: Upload ảnh, tính chênh lệch tự động

#### 4. Data Seeding ✅
- 15 nguyên liệu thực tế (Bột, sữa, trứng...)
- 3 công thức với định lượng
- 8 sản phẩm bánh ngọt
- Login credentials sẵn sàng

### ⏳ CHƯA LÀM (Ưu tiên cao)

#### 1. POS Mobile (NEXT PRIORITY)
- Giao diện siêu tối giản
- Nút Cộng/Trừ số lượng
- Thanh toán Tiền mặt/CK

#### 2. Dashboard Admin (NEXT PRIORITY)
- Cards tổng quan
- Danh sách phiếu chốt ca
- Duyệt/Từ chối phiếu

---

## III. CHI TIẾT CHỨC NĂNG THEO ROLE (MA TRẬN PHÂN QUYỀN)

### A. ROLE: ADMIN (Web/PC)

| Level 1 Module | Level 2 Feature | Level 3 Detail |
| :--- | :--- | :--- |
| **Quản lý Đại lý (Nâng cao)** | Danh sách điểm bán | Quản lý chi tiết: Nhân viên, Chủ nhà, Hợp đồng, Tiền điện/nước, "Tiền luật" (Công an), Vật dụng, Biển bảng. |
| | Bản đồ điểm bán | View dạng Map/Grid để thấy tổng quan tình hình (Điểm nào đang ổn, điểm nào có sự cố). |
| | Nhắc nhở đóng tiền | Lên lịch nhắc đóng tiền nhà, tiền luật, điện nước... |
| **Quản lý Sự cố (Mới)** | Dashboard sự cố | Theo dõi realtime các sự cố từ điểm bán gửi về (Hỏng đồ, Tai nạn, Công an hỏi...). |
| | Xử lý sự cố | Phản hồi, cập nhật trạng thái xử lý (Đang xử lý -> Đã xong). |
| **Quản lý Kho & Phân bổ** | Phân bổ hàng | Phân bổ từ Kho tổng -> Hub (Đại lý riêng tư) -> Điểm bán vỉa hè. |
| | Quản lý Hạn sử dụng | Theo dõi Date bánh (3 ngày). Cảnh báo sắp hết hạn/hết hạn cần tiêu hủy. |
| | Luân chuyển hàng | Điều chuyển bánh giữa các điểm (Vỉa hè <-> Hub) khi hết hàng/thừa hàng. |
| **Báo cáo & Thống kê** | Dashboard tổng quan | Doanh thu, Tồn kho, Lệch tiền, Sự cố chưa xử lý. |

### B. ROLE: NHÂN VIÊN ĐIỂM BÁN (Mobile Web / POS)

| Level 1 Module | Level 2 Feature | Level 3 Detail |
| :--- | :--- | :--- |
| **Bán hàng (POS)** | Giao diện bán hàng | **Siêu tối giản**: Chỉ có nút Cộng/Trừ số lượng theo từng vị bánh. Màn hình luôn sáng (Wake lock). |
| **Vận hành** | Check-in/out | Chụp ảnh check-in đầu ca, giữa ca, cuối ca. |
| | Báo cáo sự cố | Form soạn tin + Chụp ảnh sự cố gửi về trung tâm. |
| | Nhập chi phí | Nhập chỉ số điện/nước cuối tháng (nếu có). |
| **Chốt ca** | Chốt ca cuối ngày | - Đếm tồn cuối (nhập số lượng).<br>- Nhập tiền mặt/CK thực tế.<br>- **Upload ảnh**: Ảnh két tiền, Ảnh khay bánh tồn.<br>- **Sinh text Zalo**: Tự động tạo tin nhắn mẫu để copy gửi nhóm.<br>- Hệ thống tự tính lệch. |
| **Kho tại điểm** | Nhập/Trả hàng | - Nhận bánh từ Hub/Kho tổng.<br>- Trả bánh tồn về Hub (với điểm vỉa hè).<br>- Nhập hạn sử dụng (với Hub). |

---

## IV. LỘ TRÌNH TRIỂN KHAI (PHASING)

### Giai đoạn 1: Nền tảng & Quản lý Đại lý (Tuần 1-3)
-   Setup Laravel + Livewire + Tailwind.
-   Xây dựng Admin Portal (PC Layout).
-   **Module Quản lý Đại lý Nâng cao**: Thêm các trường quản lý chi tiết (Hợp đồng, Chi phí...).

### Giai đoạn 2: Kho, Phân bổ & Sự cố (Tuần 4-7)
-   **Phân bổ & Luân chuyển**: Logic Hub vs Vỉa hè, Quản lý HSD.
-   **Module Sự cố**: Nhân viên báo -> Admin xử lý.
-   **Module Chốt ca**: Hoàn thiện logic tính lệch, Upload ảnh, Sinh text Zalo.

### Giai đoạn 3: POS Mobile & Tối ưu Vận hành (Tuần 8-11)
-   **POS Mobile**: Giao diện +/- siêu tốc.
-   **Dashboard Realtime**: Livewire Poll cho số liệu & Sự cố.
-   **Tích hợp Lark**: Bắn noti sự cố/chốt ca.

## V. DATABASE SCHEMA (MVP PHASE 1)

```sql
-- =====================================================
-- HỆ THỐNG QUẢN LÝ CHUỖI CỬA HÀNG BÁNH & ĐỒ ĂN NHANH
-- Database Schema - ĐƠN GIẢN HÓA
-- =====================================================

-- ============ MODULE: NGƯỜI DÙNG (ĐƠN GIẢN) ============

-- Bảng: Người dùng
CREATE TABLE nguoi_dung (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    ho_ten VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    so_dien_thoai VARCHAR(15),
    mat_khau VARCHAR(255) NOT NULL,
    
    -- CHỈ 2 ROLE: admin hoặc nhan_vien
    vai_tro ENUM('admin', 'nhan_vien') NOT NULL DEFAULT 'nhan_vien',
    
    trang_thai ENUM('hoat_dong', 'khoa') NOT NULL DEFAULT 'hoat_dong',
    anh_dai_dien VARCHAR(255),
    dia_chi TEXT,
    ngay_vao_lam DATE,
    
    -- Lương
    luong_co_ban DECIMAL(12,2) DEFAULT 0,
    loai_luong ENUM('theo_ngay', 'theo_gio') DEFAULT 'theo_ngay',
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_vai_tro (vai_tro)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============ MODULE: ĐIỂM BÁN ============

-- Bảng: Điểm bán
CREATE TABLE diem_ban (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    ma_diem_ban VARCHAR(20) UNIQUE NOT NULL,
    ten_diem_ban VARCHAR(100) NOT NULL,
    dia_chi TEXT NOT NULL,
    so_dien_thoai VARCHAR(15),
    
    -- Thông tin vật dụng (JSON đơn giản)
    thong_tin_vat_dung JSON,
    
    -- GPS cho check-in
    vi_do DECIMAL(10, 8),
    kinh_do DECIMAL(11, 8),
    
    trang_thai ENUM('hoat_dong', 'dong_cua') DEFAULT 'hoat_dong',
    ghi_chu TEXT,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng: Gán nhân viên vào điểm bán
CREATE TABLE nhan_vien_diem_ban (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    nguoi_dung_id BIGINT UNSIGNED NOT NULL,
    diem_ban_id BIGINT UNSIGNED NOT NULL,
    ngay_bat_dau DATE NOT NULL,
    ngay_ket_thuc DATE,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (nguoi_dung_id) REFERENCES nguoi_dung(id) ON DELETE CASCADE,
    FOREIGN KEY (diem_ban_id) REFERENCES diem_ban(id) ON DELETE CASCADE,
    UNIQUE KEY unique_nhan_vien_diem_ban (nguoi_dung_id, diem_ban_id, ngay_bat_dau)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============ MODULE: SẢN PHẨM ============

-- Bảng: Danh mục sản phẩm
CREATE TABLE danh_muc_san_pham (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    ten_danh_muc VARCHAR(100) NOT NULL,
    mo_ta TEXT,
    thu_tu INT DEFAULT 0,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng: Sản phẩm
CREATE TABLE san_pham (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    danh_muc_id BIGINT UNSIGNED,
    ma_san_pham VARCHAR(50) UNIQUE NOT NULL,
    ten_san_pham VARCHAR(200) NOT NULL,
    mo_ta TEXT,
    anh_san_pham VARCHAR(255),
    
    -- Giá bán (đơn giản, không phức tạp size/loại)
    gia_ban DECIMAL(12,2) NOT NULL DEFAULT 0,
    
    -- Đơn vị tính
    don_vi_tinh VARCHAR(20) DEFAULT 'cái', -- cái, khay, hộp
    
    trang_thai ENUM('con_hang', 'het_hang', 'ngung_ban') DEFAULT 'con_hang',
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (danh_muc_id) REFERENCES danh_muc_san_pham(id) ON DELETE SET NULL,
    INDEX idx_danh_muc (danh_muc_id),
    INDEX idx_trang_thai (trang_thai)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============ MODULE: KHO (ĐƠN GIẢN - PHASE 1) ============

-- Bảng: Nhà cung cấp
CREATE TABLE nha_cung_cap (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    ma_ncc VARCHAR(20) UNIQUE NOT NULL,
    ten_ncc VARCHAR(200) NOT NULL,
    so_dien_thoai VARCHAR(15),
    dia_chi TEXT,
    email VARCHAR(100),
    ghi_chu TEXT,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng: Nguyên liệu
CREATE TABLE nguyen_lieu (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    ma_nguyen_lieu VARCHAR(50) UNIQUE NOT NULL,
    ten_nguyen_lieu VARCHAR(200) NOT NULL,
    don_vi_tinh VARCHAR(20) NOT NULL, -- kg, lít, gói
    ton_kho_hien_tai DECIMAL(12,2) DEFAULT 0,
    ton_kho_toi_thieu DECIMAL(12,2) DEFAULT 0, -- Cảnh báo khi thấp hơn
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng: Phiếu nhập kho nguyên liệu
CREATE TABLE phieu_nhap_kho (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    ma_phieu VARCHAR(50) UNIQUE NOT NULL,
    nha_cung_cap_id BIGINT UNSIGNED,
    nguoi_nhap_id BIGINT UNSIGNED NOT NULL, -- Admin nhập
    ngay_nhap DATETIME NOT NULL,
    tong_tien DECIMAL(15,2) DEFAULT 0,
    ghi_chu TEXT,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (nha_cung_cap_id) REFERENCES nha_cung_cap(id) ON DELETE SET NULL,
    FOREIGN KEY (nguoi_nhap_id) REFERENCES nguoi_dung(id),
    INDEX idx_ngay_nhap (ngay_nhap)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng: Chi tiết phiếu nhập kho
CREATE TABLE chi_tiet_phieu_nhap (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    phieu_nhap_kho_id BIGINT UNSIGNED NOT NULL,
    nguyen_lieu_id BIGINT UNSIGNED NOT NULL,
    so_luong DECIMAL(12,2) NOT NULL,
    don_gia DECIMAL(12,2) NOT NULL,
    thanh_tien DECIMAL(15,2) NOT NULL,
    
    FOREIGN KEY (phieu_nhap_kho_id) REFERENCES phieu_nhap_kho(id) ON DELETE CASCADE,
    FOREIGN KEY (nguyen_lieu_id) REFERENCES nguyen_lieu(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng: Tồn kho thành phẩm tại kho tổng
CREATE TABLE ton_kho_thanh_pham (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    san_pham_id BIGINT UNSIGNED NOT NULL,
    so_luong DECIMAL(12,2) DEFAULT 0,
    ngay_san_xuat DATE,
    han_su_dung DATE,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (san_pham_id) REFERENCES san_pham(id) ON DELETE CASCADE,
    INDEX idx_san_pham (san_pham_id),
    INDEX idx_han_su_dung (han_su_dung)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============ MODULE: PHÂN BỔ HÀNG ============

-- Bảng: Phiếu xuất hàng TỔNG từ xưởng (MỖI NGÀY 1 PHIẾU)
CREATE TABLE phieu_xuat_hang_tong (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    ma_phieu VARCHAR(50) UNIQUE NOT NULL, -- PXT-20241202
    nguoi_xuat_id BIGINT UNSIGNED NOT NULL, -- Admin xuất
    
    ngay_xuat DATE NOT NULL,
    gio_xuat TIME NOT NULL,
    
    -- Ảnh chụp toàn bộ hàng xuất trong ngày
    anh_hang_xuat VARCHAR(255),
    
    tong_so_luong DECIMAL(12,2) DEFAULT 0, -- Tổng số bánh xuất
    ghi_chu TEXT,
    
    trang_thai ENUM('dang_chuan_bi', 'da_xuat', 'huy') DEFAULT 'dang_chuan_bi',
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (nguoi_xuat_id) REFERENCES nguoi_dung(id),
    INDEX idx_ngay_xuat (ngay_xuat)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng: Chi tiết phiếu xuất TỔNG (Từng loại bánh)
CREATE TABLE chi_tiet_phieu_xuat_tong (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    phieu_xuat_hang_tong_id BIGINT UNSIGNED NOT NULL,
    san_pham_id BIGINT UNSIGNED NOT NULL,
    so_luong DECIMAL(12,2) NOT NULL,
    
    FOREIGN KEY (phieu_xuat_hang_tong_id) REFERENCES phieu_xuat_hang_tong(id) ON DELETE CASCADE,
    FOREIGN KEY (san_pham_id) REFERENCES san_pham(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng: Phân bổ hàng ĐẾN TỪNG ĐIỂM (Tự động từ setting)
CREATE TABLE phan_bo_hang_diem_ban (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    phieu_xuat_hang_tong_id BIGINT UNSIGNED NOT NULL,
    diem_ban_id BIGINT UNSIGNED NOT NULL,
    nguoi_nhan_id BIGINT UNSIGNED, -- Nhân viên nhận hàng
    
    ngay_nhan DATETIME, -- Khi nhân viên xác nhận nhận
    trang_thai ENUM('chua_nhan', 'da_nhan') DEFAULT 'chua_nhan',
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (phieu_xuat_hang_tong_id) REFERENCES phieu_xuat_hang_tong(id) ON DELETE CASCADE,
    FOREIGN KEY (diem_ban_id) REFERENCES diem_ban(id),
    FOREIGN KEY (nguoi_nhan_id) REFERENCES nguoi_dung(id),
    INDEX idx_diem_ban (diem_ban_id),
    INDEX idx_trang_thai (trang_thai)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng: Chi tiết phân bổ (Từng loại bánh cho từng điểm)
CREATE TABLE chi_tiet_phan_bo (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    phan_bo_hang_diem_ban_id BIGINT UNSIGNED NOT NULL,
    san_pham_id BIGINT UNSIGNED NOT NULL,
    so_luong DECIMAL(12,2) NOT NULL,
    
    FOREIGN KEY (phan_bo_hang_diem_ban_id) REFERENCES phan_bo_hang_diem_ban(id) ON DELETE CASCADE,
    FOREIGN KEY (san_pham_id) REFERENCES san_pham(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- BỎ BẢNG SETTING - KHÔNG CẦN TỰ ĐỘNG

-- Bảng: Tồn kho tại điểm bán (Theo ngày)
CREATE TABLE ton_kho_diem_ban (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    diem_ban_id BIGINT UNSIGNED NOT NULL,
    san_pham_id BIGINT UNSIGNED NOT NULL,
    ngay DATE NOT NULL,
    
    ton_dau_ca DECIMAL(12,2) DEFAULT 0,
    ton_cuoi_ca DECIMAL(12,2) DEFAULT 0,
    
    -- Phase 2: Thêm HSD
    han_su_dung DATE,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (diem_ban_id) REFERENCES diem_ban(id) ON DELETE CASCADE,
    FOREIGN KEY (san_pham_id) REFERENCES san_pham(id),
    UNIQUE KEY unique_ton_kho (diem_ban_id, san_pham_id, ngay),
    INDEX idx_ngay (ngay)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============ MODULE: LUÂN CHUYỂN HÀNG ============

-- Bảng: Luân chuyển hàng giữa các điểm
CREATE TABLE luan_chuyen_hang (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    ma_phieu VARCHAR(50) UNIQUE NOT NULL,
    
    diem_ban_nguon_id BIGINT UNSIGNED NOT NULL, -- Điểm A
    diem_ban_dich_id BIGINT UNSIGNED NOT NULL, -- Điểm B
    
    nguoi_chuyen_id BIGINT UNSIGNED NOT NULL,
    nguoi_nhan_id BIGINT UNSIGNED,
    
    ngay_chuyen DATETIME NOT NULL,
    ngay_nhan DATETIME,
    
    ly_do TEXT,
    trang_thai ENUM('dang_chuyen', 'da_nhan', 'huy') DEFAULT 'dang_chuyen',
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (diem_ban_nguon_id) REFERENCES diem_ban(id),
    FOREIGN KEY (diem_ban_dich_id) REFERENCES diem_ban(id),
    FOREIGN KEY (nguoi_chuyen_id) REFERENCES nguoi_dung(id),
    FOREIGN KEY (nguoi_nhan_id) REFERENCES nguoi_dung(id),
    INDEX idx_ngay_chuyen (ngay_chuyen)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng: Chi tiết luân chuyển
CREATE TABLE chi_tiet_luan_chuyen (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    luan_chuyen_hang_id BIGINT UNSIGNED NOT NULL,
    san_pham_id BIGINT UNSIGNED NOT NULL,
    so_luong DECIMAL(12,2) NOT NULL,
    
    -- Phase 2: Thêm HSD chi tiết
    han_su_dung DATE,
    
    FOREIGN KEY (luan_chuyen_hang_id) REFERENCES luan_chuyen_hang(id) ON DELETE CASCADE,
    FOREIGN KEY (san_pham_id) REFERENCES san_pham(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============ MODULE: CA LÀM VIỆC ============

-- Bảng: Ca làm việc
CREATE TABLE ca_lam_viec (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    diem_ban_id BIGINT UNSIGNED NOT NULL,
    nguoi_dung_id BIGINT UNSIGNED NOT NULL,
    
    ngay_lam DATE NOT NULL,
    gio_bat_dau TIME NOT NULL,
    gio_ket_thuc TIME NOT NULL,
    
    trang_thai ENUM('chua_bat_dau', 'dang_lam', 'da_ket_thuc', 'vang') DEFAULT 'chua_bat_dau',
    ghi_chu TEXT,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (diem_ban_id) REFERENCES diem_ban(id) ON DELETE CASCADE,
    FOREIGN KEY (nguoi_dung_id) REFERENCES nguoi_dung(id) ON DELETE CASCADE,
    INDEX idx_ngay_lam (ngay_lam),
    INDEX idx_nguoi_dung (nguoi_dung_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng: Yêu cầu ca làm (Đổi ca, Xin nghỉ)
CREATE TABLE yeu_cau_ca_lam (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    nguoi_dung_id BIGINT UNSIGNED NOT NULL,
    loai_yeu_cau ENUM('xin_ca', 'doi_ca', 'xin_nghi') NOT NULL,
    
    ca_lam_viec_id BIGINT UNSIGNED, -- Ca muốn đổi/xin nghỉ
    ngay_mong_muon DATE,
    gio_bat_dau TIME,
    gio_ket_thuc TIME,
    
    ly_do TEXT,
    trang_thai ENUM('cho_duyet', 'da_duyet', 'tu_choi') DEFAULT 'cho_duyet',
    
    nguoi_duyet_id BIGINT UNSIGNED,
    ngay_duyet DATETIME,
    ghi_chu_duyet TEXT,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (nguoi_dung_id) REFERENCES nguoi_dung(id) ON DELETE CASCADE,
    FOREIGN KEY (ca_lam_viec_id) REFERENCES ca_lam_viec(id) ON DELETE SET NULL,
    FOREIGN KEY (nguoi_duyet_id) REFERENCES nguoi_dung(id),
    INDEX idx_trang_thai (trang_thai)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============ MODULE: CHẤM CÔNG ============

-- Bảng: Chấm công
CREATE TABLE cham_cong (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    nguoi_dung_id BIGINT UNSIGNED NOT NULL,
    diem_ban_id BIGINT UNSIGNED NOT NULL,
    ca_lam_viec_id BIGINT UNSIGNED,
    
    ngay_cham DATE NOT NULL,
    
    -- Check-in
    gio_vao TIME,
    vi_do_vao DECIMAL(10, 8),
    kinh_do_vao DECIMAL(11, 8),
    anh_check_in VARCHAR(255), -- Phase 2
    
    -- Check-out
    gio_ra TIME,
    vi_do_ra DECIMAL(10, 8),
    kinh_do_ra DECIMAL(11, 8),
    anh_check_out VARCHAR(255), -- Phase 2
    
    tong_gio_lam DECIMAL(5,2), -- Tự động tính
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (nguoi_dung_id) REFERENCES nguoi_dung(id) ON DELETE CASCADE,
    FOREIGN KEY (diem_ban_id) REFERENCES diem_ban(id),
    FOREIGN KEY (ca_lam_viec_id) REFERENCES ca_lam_viec(id) ON DELETE SET NULL,
    INDEX idx_nguoi_dung_ngay (nguoi_dung_id, ngay_cham)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============ MODULE: CHỐT CA ============

-- Bảng: Phiếu chốt ca
CREATE TABLE phieu_chot_ca (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    ma_phieu VARCHAR(50) UNIQUE NOT NULL,
    diem_ban_id BIGINT UNSIGNED NOT NULL,
    nguoi_chot_id BIGINT UNSIGNED NOT NULL,
    ca_lam_viec_id BIGINT UNSIGNED,
    
    ngay_chot DATE NOT NULL,
    gio_chot TIME NOT NULL,
    
    -- Tiền
    tien_mat DECIMAL(15,2) DEFAULT 0,
    tien_chuyen_khoan DECIMAL(15,2) DEFAULT 0,
    tong_tien_thuc_te DECIMAL(15,2) DEFAULT 0,
    tong_tien_ly_thuyet DECIMAL(15,2) DEFAULT 0,
    tien_lech DECIMAL(15,2) DEFAULT 0, -- Tự động tính
    
    -- Hàng hóa (JSON đơn giản)
    ton_dau_ca JSON, -- {"san_pham_id": so_luong}
    ton_cuoi_ca JSON,
    hang_lech JSON, -- Tự động tính
    
    anh_chot_ket VARCHAR(255),
    ghi_chu TEXT,
    
    -- Trạng thái duyệt
    trang_thai ENUM('cho_duyet', 'da_duyet', 'tu_choi') DEFAULT 'cho_duyet',
    nguoi_duyet_id BIGINT UNSIGNED,
    ngay_duyet DATETIME,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (diem_ban_id) REFERENCES diem_ban(id),
    FOREIGN KEY (nguoi_chot_id) REFERENCES nguoi_dung(id),
    FOREIGN KEY (ca_lam_viec_id) REFERENCES ca_lam_viec(id) ON DELETE SET NULL,
    FOREIGN KEY (nguoi_duyet_id) REFERENCES nguoi_dung(id),
    INDEX idx_ngay_chot (ngay_chot),
    INDEX idx_trang_thai (trang_thai)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============ MODULE: LƯƠNG ============

-- Bảng: Bảng lương tháng
CREATE TABLE bang_luong (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    thang INT NOT NULL, -- 1-12
    nam INT NOT NULL,
    nguoi_dung_id BIGINT UNSIGNED NOT NULL,
    
    luong_co_ban DECIMAL(12,2) NOT NULL,
    so_ngay_cong DECIMAL(5,2) NOT NULL, -- Từ bảng chấm công
    
    -- Tính tự động
    tong_luong DECIMAL(15,2) NOT NULL,
    
    -- Trạng thái
    trang_thai ENUM('chua_chot', 'da_chot', 'da_thanh_toan') DEFAULT 'chua_chot',
    ngay_chot DATETIME,
    ngay_thanh_toan DATETIME,
    
    ghi_chu TEXT,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (nguoi_dung_id) REFERENCES nguoi_dung(id) ON DELETE CASCADE,
    UNIQUE KEY unique_luong_thang (nguoi_dung_id, thang, nam),
    INDEX idx_thang_nam (thang, nam)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============ MODULE: THÔNG BÁO ============

-- Bảng: Thông báo
CREATE TABLE thong_bao (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    tieu_de VARCHAR(200) NOT NULL,
    noi_dung TEXT NOT NULL,
    loai_thong_bao ENUM('he_thong', 'canh_bao', 'thong_tin') DEFAULT 'thong_tin',
    
    -- Gửi đến
    gui_toi_tat_ca BOOLEAN DEFAULT FALSE,
    diem_ban_id BIGINT UNSIGNED, -- Gửi đến 1 điểm cụ thể
    nguoi_nhan_id BIGINT UNSIGNED, -- Gửi đến 1 người cụ thể
    
    nguoi_gui_id BIGINT UNSIGNED NOT NULL,
    ngay_gui DATETIME NOT NULL,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (diem_ban_id) REFERENCES diem_ban(id) ON DELETE CASCADE,
    FOREIGN KEY (nguoi_nhan_id) REFERENCES nguoi_dung(id) ON DELETE CASCADE,
    FOREIGN KEY (nguoi_gui_id) REFERENCES nguoi_dung(id),
    INDEX idx_ngay_gui (ngay_gui)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng: Trạng thái đọc thông báo
CREATE TABLE trang_thai_thong_bao (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    thong_bao_id BIGINT UNSIGNED NOT NULL,
    nguoi_dung_id BIGINT UNSIGNED NOT NULL,
    da_doc BOOLEAN DEFAULT FALSE,
    ngay_doc DATETIME,
    
    FOREIGN KEY (thong_bao_id) REFERENCES thong_bao(id) ON DELETE CASCADE,
    FOREIGN KEY (nguoi_dung_id) REFERENCES nguoi_dung(id) ON DELETE CASCADE,
    UNIQUE KEY unique_thong_bao_nguoi_dung (thong_bao_id, nguoi_dung_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============ MODULE: NHẬT KÝ HỆ THỐNG ============

-- Bảng: Log hoạt động
CREATE TABLE nhat_ky_hoat_dong (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    nguoi_dung_id BIGINT UNSIGNED,
    hanh_dong VARCHAR(100) NOT NULL, -- "tao_ca_lam", "nhap_kho", "chot_ca"
    mo_ta TEXT,
    du_lieu_cu JSON, -- Dữ liệu trước khi thay đổi
    du_lieu_moi JSON, -- Dữ liệu sau khi thay đổi
    ip_address VARCHAR(45),
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (nguoi_dung_id) REFERENCES nguoi_dung(id) ON DELETE SET NULL,
    INDEX idx_nguoi_dung (nguoi_dung_id),
    INDEX idx_hanh_dong (hanh_dong),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============ PHASE 2: CÔNG THỨC SẢN XUẤT ============

-- Bảng: Công thức sản xuất (Phase 2)
CREATE TABLE cong_thuc_san_xuat (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    san_pham_id BIGINT UNSIGNED NOT NULL,
    ten_cong_thuc VARCHAR(200) NOT NULL,
    mo_ta TEXT,
    so_luong_san_xuat DECIMAL(12,2) NOT NULL, -- 1 mẻ tạo ra bao nhiêu sp
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (san_pham_id) REFERENCES san_pham(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng: Chi tiết công thức
CREATE TABLE chi_tiet_cong_thuc (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    cong_thuc_san_xuat_id BIGINT UNSIGNED NOT NULL,
    nguyen_lieu_id BIGINT UNSIGNED NOT NULL,
    so_luong DECIMAL(12,2) NOT NULL,
    
    FOREIGN KEY (cong_thuc_san_xuat_id) REFERENCES cong_thuc_san_xuat(id) ON DELETE CASCADE,
    FOREIGN KEY (nguyen_lieu_id) REFERENCES nguyen_lieu(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng: Kế hoạch sản xuất (Phase 2)
CREATE TABLE ke_hoach_san_xuat (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    cong_thuc_san_xuat_id BIGINT UNSIGNED NOT NULL,
    ngay_san_xuat DATE NOT NULL,
    so_me INT NOT NULL,
    so_luong_du_kien DECIMAL(12,2) NOT NULL,
    trang_thai ENUM('ke_hoach', 'dang_san_xuat', 'hoan_thanh') DEFAULT 'ke_hoach',
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
```
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (cong_thuc_san_xuat_id) REFERENCES cong_thuc_san_xuat(id),
    INDEX idx_ngay_san_xuat (ngay_san_xuat)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```
## VI. YÊU CẦU TÍNH NĂNG CHUNG CHO TẤT CẢ MODULE

### 1. Bộ lọc (Filtering)
Mọi danh sách dữ liệu đều phải có khả năng lọc linh hoạt:

#### 1.1. Lọc theo thời gian
-   **Preset ranges**: Hôm nay, Hôm qua, 7 ngày qua, 30 ngày qua, Tháng này, Tháng trước.
-   **Custom range**: Cho phép chọn từ ngày - đến ngày tùy chỉnh.
-   **Mặc định**: Hiển thị dữ liệu 30 ngày gần nhất khi vào trang lần đầu.

#### 1.2. Sắp xếp (Sorting)
-   Cho phép sắp xếp tăng/giảm dần theo các cột quan trọng.
-   **Hiển thị**: Icon mũi tên ↑↓ bên cạnh tiêu đề cột có thể sắp xếp.
-   **Trạng thái**: Visual feedback cho biết đang sắp xếp theo cột nào (tăng/giảm).

#### 1.3. Tìm kiếm (Search)
-   **Search bar**: Luôn hiển thị ở vị trí dễ thấy.
-   **Tìm kiếm theo**: Mã, tên, số điện thoại, hoặc các trường quan trọng khác.
-   **Real-time search**: Tự động lọc khi gõ (debounce 300ms).
-   **Clear button**: Nút xóa nhanh để reset tìm kiếm.

#### 1.4. Bộ lọc nâng cao (Advanced Filters)
Tùy theo module, cần có các filter đặc thù:
-   **Điểm bán**: Lọc theo điểm bán cụ thể (Dropdown).
-   **Trạng thái**: Lọc theo trạng thái (Active/Inactive, Đã duyệt/Chờ duyệt).
-   **Nhân viên**: Lọc theo người thực hiện hành động.
-   **Danh mục**: Lọc theo danh mục sản phẩm.

#### 1.5. Lưu trữ Filter
-   **Session storage**: Lưu trạng thái filter khi user chuyển trang hoặc refresh.
-   **Reset button**: Nút "Xóa tất cả bộ lọc" để về trạng thái mặc định.
-   **URL parameters**: Có thể bookmark hoặc chia sẻ link với filter đã chọn.

---

### 2. Phân trang (Pagination)

#### 2.1. Cấu hình mặc định
-   **Số item mặc định**: 15 items/page.
-   **Options**: Cho phép chọn 15, 25, 50, 100, hoặc "Tất cả".
-   **Position**: Hiển thị phân trang ở cả đầu và cuối danh sách (nếu danh sách dài).

#### 2.2. Thông tin hiển thị
```
Hiển thị 1-15 trên tổng 247 kết quả
```
-   Rõ ràng về vị trí hiện tại và tổng số.
-   Cập nhật real-time khi áp dụng filter.

#### 2.3. Navigation
-   **Nút**: Trang đầu | Trước | [1] 2 3 ... 10 | Tiếp | Trang cuối.
-   **Nhập trực tiếp**: Ô input để nhảy đến trang cụ thể.
-   **Keyboard support**: Phím mũi tên để di chuyển trang.

#### 2.4. Giữ Filter khi chuyển trang
-   **Quan trọng**: Khi chuyển trang, KHÔNG được reset filter đã chọn.
-   Filter, sorting, và search phải giữ nguyên khi paginate.

#### 2.5. Loading State
-   Hiển thị skeleton hoặc spinner khi đang load trang mới.
-   Không cho phép click liên tục gây spam request.

---

### 3. Xuất Excel (Export to Excel)

#### 3.1. Tính năng cơ bản
-   **Button**: Nút "Xuất Excel" 📥 hiển thị rõ ràng trên mỗi danh sách.
-   **Export what you see**: Chỉ xuất data theo filter hiện tại (không xuất tất cả DB).
-   **Confirmation**: Hiển thị popup xác nhận số lượng bản ghi sẽ xuất.

#### 3.2. Định dạng file Excel chuyên nghiệp

**Thông tin Header**:
```
TÊN BÁO CÁO
Ngày xuất: DD/MM/YYYY HH:mm
Người xuất: [Tên Admin]
Bộ lọc áp dụng: [Từ ngày - Đến ngày, Điểm bán, ...]
```

**Định dạng Sheet**:
-   **Header row**: Background màu, chữ in đậm, căn giữa.
-   **Auto-fit columns**: Tự động căn chỉnh độ rộng cột.
-   **Number format**: Định dạng số tiền, ngày tháng đúng chuẩn.
-   **Border**: Có đường viền cho các cell.

**Footer (nếu có)**:
-   Tổng cộng, tổng tiền, tổng số lượng (nếu áp dụng).

#### 3.3. Tên file
Format: `[TenBaoCao]_[NgayXuat]_[ThoiGian].xlsx`

Ví dụ: `BaoCao_DanhSachNhanVien_02122024_132845.xlsx`

#### 3.4. Giới hạn
-   **Cảnh báo**: Nếu số lượng > 5000 records, hiển thị warning.
-   **Gợi ý**: "Bạn đang xuất file lớn. Hãy thu hẹp bộ lọc để tối ưu."

---

### 4. Các tính năng UI/UX chung khác

#### 4.1. Loading States
-   **Skeleton screens**: Ưu tiên dùng skeleton thay vì spinner đơn thuần.
-   **Progress indicator**: Với tác vụ lâu (upload file), hiển thị % tiến trình.

#### 4.2. Empty States
-   Khi không có dữ liệu, hiển thị:
    ```
    🗂️ Chưa có dữ liệu
    [Mô tả ngắn gọn]
    [Nút hành động: "Thêm mới"]
    ```

#### 4.3. Error Handling
-   **User-friendly messages**: Không hiển thị lỗi kỹ thuật cho user.
-   **Retry button**: Cho phép thử lại action bị lỗi.
-   **Toast notifications**: Dùng toast để báo thành công/lỗi.

#### 4.4. Confirmation Dialogs
-   Tất cả hành động XÓA phải có popup xác nhận.
-   **Hiển thị rõ ràng**: "Bạn có chắc muốn xóa [Tên item]?"
-   **Destructive action**: Nút Xóa phải có màu đỏ/warning.

#### 4.5. Breadcrumbs
-   Hiển thị navigation path: `Dashboard > Quản lý Nhân sự > Danh sách Nhân viên`
-   Click được để quay lại màn hình trước.

#### 4.6. Action Buttons
-   **Primary action**: Nổi bật, màu chính (Thêm mới, Lưu).
-   **Secondary action**: Màu nhạt hơn (Hủy, Quay lại).
-   **Destructive action**: Màu đỏ (Xóa, Hủy bỏ).
-   **Icon + Text**: Kết hợp icon và chữ để dễ hiểu.

---

### 5. Responsive Design

#### 5.1. Admin Portal (Desktop)
-   Tối ưu cho màn hình >= 1280px.
-   **Sidebar**: Collapsible để tiết kiệm không gian.
-   **Tables**: Có horizontal scroll nếu cần.

#### 5.2. POS Mobile
-   **Mobile-first**: Thiết kế ưu tiên cho điện thoại.
-   **Touch-friendly**: Button size >= 44x44px.
-   **No hover states**: Chỉ dùng active/focus states.
-   **Portrait mode**: Tối ưu cho chế độ dọc.

---

### 6. Performance Requirements

#### 6.1. Tốc độ tải trang
-   **First load**: < 2 giây.
-   **Subsequent loads**: < 1 giây (nhờ cache).

#### 6.2. Livewire Optimization
-   Dùng `wire:loading` để hiển thị loading state.
-   Lazy loading cho component nặng.
-   Debounce cho input search (300ms).

#### 6.3. Database Queries
-   **Pagination**: Luôn luôn paginate, không select all.
-   **Eager loading**: Tránh N+1 query problem.
-   **Indexing**: Đánh index đúng cột thường xuyên tìm kiếm.

---

### 7. Accessibility (A11y)

#### 7.1. Keyboard Navigation
-   Tất cả action phải thực hiện được bằng bàn phím.
-   **Tab order**: Logic và dễ đoán.
-   **Focus indicators**: Hiển thị rõ element đang focus.

#### 7.2. Screen Reader Support
-   Semantic HTML: Dùng đúng thẻ `<button>`, `<nav>`, `<main>`.
-   `aria-label` cho icon buttons không có text.

#### 7.3. Color Contrast
-   Đảm bảo tỷ lệ contrast đạt WCAG AA (4.5:1 cho text).

---

### 8. Data Validation

#### 8.1. Frontend Validation (Livewire)
-   Real-time validation khi user nhập.
-   Hiển thị lỗi ngay dưới field.
-   Disable submit button nếu form invalid.

#### 8.2. Backend Validation (Laravel)
-   **LUÔN LUÔN** validate lại ở backend.
-   Return clear error messages.
-   Log validation failures cho security audit.

#### 8.3. Required Fields
-   Đánh dấu `*` màu đỏ cho field bắt buộc.
-   Hiển thị helper text nếu cần.

---

### 9. File Upload

#### 9.1. Hỗ trợ định dạng
-   **Ảnh**: JPG, PNG, WebP (Max 5MB).
-   **Documents**: PDF, Excel, Word (Max 10MB).

#### 9.2. Preview
-   Preview ảnh trước khi upload.
-   Cho phép crop/resize nếu cần.

#### 9.3. Progress
-   Hiển thị progress bar khi upload.
-   Cho phép cancel upload.

#### 9.4. Storage
-   Store file với tên unique (UUID).
-   Organize theo thư mục: `/storage/images/products/`, `/storage/uploads/chot_ca/`.
-   **Optimization**: Auto-compress ảnh khi upload.

---

### 10. Notifications & Alerts

#### 10.1. Toast Notifications
-   **Success**: Màu xanh, icon ✓.
-   **Error**: Màu đỏ, icon ✕.
-   **Warning**: Màu vàng, icon ⚠.
-   **Info**: Màu xanh nhạt, icon ℹ.
-   **Auto-dismiss**: Tự động ẩn sau 3-5 giây.
