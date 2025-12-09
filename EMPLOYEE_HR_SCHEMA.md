# EMPLOYEE HR MANAGEMENT - DATABASE SCHEMA

**Last Updated**: 08/12/2024  
**Status**: ✅ Enhanced

---

## 📊 THÔNG TIN NHÂN VIÊN

### Fields trong bảng `nguoi_dung`

#### 1. Thông tin cơ bản
| Field | Type | Description | Required |
|-------|------|-------------|----------|
| `ma_nhan_vien` | VARCHAR(20) | Mã nhân viên (unique) | No |
| `ho_ten` | VARCHAR | Họ và tên | Yes |
| `email` | VARCHAR | Email | Yes (unique) |
| `facebook` | VARCHAR | Link Facebook | No |
| `so_dien_thoai` | VARCHAR | Số điện thoại | No |
| `dia_chi` | TEXT | Địa chỉ | No |
| `anh_dai_dien` | VARCHAR | Avatar path | No |

#### 2. Giấy tờ tùy thân
| Field | Type | Description |
|-------|------|-------------|
| `so_cmnd` | VARCHAR(20) | Số CMND/CCCD |
| `ngay_cap_cmnd` | DATE | Ngày cấp |
| `noi_cap_cmnd` | VARCHAR | Nơi cấp |

#### 3. Liên hệ khẩn cấp
| Field | Type | Description |
|-------|------|-------------|
| `nguoi_lien_he_khan_cap` | VARCHAR | Tên người liên hệ |
| `sdt_lien_he_khan_cap` | VARCHAR(15) | SĐT người liên hệ |

#### 4. Hợp đồng lao động
| Field | Type | Values | Description |
|-------|------|--------|-------------|
| `ngay_vao_lam` | DATE | - | Ngày bắt đầu làm việc |
| `ngay_ky_hop_dong` | DATE | - | Ngày ký hợp đồng |
| `ngay_het_han_hop_dong` | DATE | - | Ngày hết hạn HĐ |
| `loai_hop_dong` | ENUM | thu_viec, chinh_thuc, hop_tac | Loại hợp đồng |

#### 5. Lương & Thanh toán
| Field | Type | Description |
|-------|------|-------------|
| `luong_thu_viec` | DECIMAL(12,2) | Mức lương thử việc |
| `luong_chinh_thuc` | DECIMAL(12,2) | Mức lương chính thức |
| `luong_co_ban` | DECIMAL(12,2) | Lương cơ bản (fallback) |
| `loai_luong` | VARCHAR | Hình thức trả lương |
| `luong_hien_tai` | Accessor | Tính theo loại HĐ |

**Logic tính lương hiện tại**:
```php
match($loai_hop_dong) {
    'chinh_thuc' => $luong_chinh_thuc ?? $luong_co_ban,
    'thu_viec' => $luong_thu_viec ?? $luong_co_ban,
    default => $luong_co_ban,
}
```

#### 6. Thông tin ngân hàng
| Field | Type | Description |
|-------|------|-------------|
| `ngan_hang` | VARCHAR | Tên ngân hàng |
| `so_tai_khoan` | VARCHAR | Số tài khoản |
| `chu_tai_khoan` | VARCHAR | Chủ tài khoản |

#### 7. Hệ thống
| Field | Type | Values | Description |
|-------|------|--------|-------------|
| `vai_tro` | ENUM | admin, nhan_vien | Quyền hạn |
| `trang_thai` | ENUM | hoat_dong, tam_ngung, nghi_viec | Trạng thái |

---

## 🔧 MODEL METHODS

### Accessors

#### `luong_hien_tai`
Tự động return mức lương đúng theo loại hợp đồng hiện tại.

**Usage**:
```php
$user->luong_hien_tai; // Auto-calculated
```

#### `contract_status_badge`
Return HTML badge theo trạng thái hợp đồng.

**Output**:
- 🟢 Chính thức (green)
- 🟡 Thử việc (yellow)
- 🔵 Hợp tác (blue)
- 🔴 Hết hạn (red)

### Methods

#### `isContractExpired(): bool`
Check xem hợp đồng có hết hạn không.

**Usage**:
```php
if ($user->isContractExpired()) {
    // Notify HR or admin
}
```

---

## 📝 USE CASES

### 1. Tạo nhân viên mới (Thử việc)
```php
$user = User::create([
    'ma_nhan_vien' => 'NV001',
    'ho_ten' => 'Nguyễn Văn A',
    'email' => 'a@bakery.com',
    'so_dien_thoai' => '0901234567',
    'dia_chi' => '123 ABC, HCM',
    'loai_hop_dong' => 'thu_viec',
    'ngay_ky_hop_dong' => '2024-12-01',
    'ngay_het_han_hop_dong' => '2024-12-31', // 1 month
    'luong_thu_viec' => 5000000,
    'vai_tro' => 'nhan_vien',
]);
```

### 2. Chuyển chính thức
```php
$user->update([
    'loai_hop_dong' => 'chinh_thuc',
    'luong_chinh_thuc' => 7000000,
    'ngay_ky_hop_dong' => '2025-01-01',
    'ngay_het_han_hop_dong' => '2025-12-31', // 1 year
]);
```

### 3. Cập nhật thông tin ngân hàng
```php
$user->update([
    'ngan_hang' => 'Vietcombank',
    'so_tai_khoan' => '1234567890',
    'chu_tai_khoan' => 'NGUYEN VAN A',
]);
```

### 4. Check hợp đồng sắp hết hạn
```php
$expiringSoon = User::where('trang_thai', 'hoat_dong')
    ->whereBetween('ngay_het_han_hop_dong', [
        now(),
        now()->addDays(30)
    ])
    ->get();
```

---

## 🎨 UI DISPLAY

### Employee Card Example
```html
<div class="employee-card">
    <h3>{{ $user->ma_nhan_vien }} - {{ $user->ho_ten }}</h3>
    {!! $user->contract_status_badge !!}
    
    <div>Lương hiện tại: {{ number_format($user->luong_hien_tai) }}đ</div>
    <div>HĐ hết hạn: {{ $user->ngay_het_han_hop_dong?->format('d/m/Y') }}</div>
</div>
```

---

## ⚠️ VALIDATION RULES

### Common Validations
```php
'ma_nhan_vien' => 'nullable|string|max:20|unique:nguoi_dung',
'ho_ten' => 'required|string|max:255',
'email' => 'required|email|unique:nguoi_dung',
'so_dien_thoai' => 'nullable|regex:/^[0-9]{10,11}$/',
'so_cmnd' => 'nullable|regex:/^[0-9]{9,12}$/',
'luong_thu_viec' => 'nullable|numeric|min:0',
'luong_chinh_thuc' => 'nullable|numeric|min:0',
'loai_hop_dong' => 'required|in:thu_viec,chinh_thuc,hop_tac',
```

---

## 🚀 NEXT STEPS

### Module cần tạo:

1. **User Management CRUD**
   - List with filters (role, status, contract type)
   - Create/Edit form with all HR fields
   - Contract renewal workflow
   - Auto-alert for expiring contracts

2. **Salary Management**
   - Monthly salary calculation
   - Payment history
   - Export for accounting

3. **Contract Management**
   - Upload contract documents
   - E-signature integration
   - Renewal reminders

4. **Reports**
   - Employee directory
   - Contract expiry report
   - Salary summary

---

**Document Version**: 1.0  
**Migration**: `2025_12_08_add_employee_hr_fields_to_nguoi_dung.php`
