# LUỒNG NGHIỆP VỤ NHÂN VIÊN - BAKERY SYSTEM

**Version**: 2.0  
**Last Updated**: 08/12/2024  
**Status**: ✅ PRODUCTION READY

---

## 📋 MỤC LỤC

1. [Tổng quan luồng](#tổng-quan-luồng)
2. [Chi tiết từng bước](#chi-tiết-từng-bước)
3. [Database Schema](#database-schema)
4. [Tính năng mới v2.0](#tính-năng-mới-v20)
5. [Troubleshooting](#troubleshooting)

---

## 🔄 TỔNG QUAN LUỒNG

### Sơ đồ luồng đầy đủ (Updated v2.0)

```
┌─────────────────────────────────────────────────────────────┐
│                         NHÂN VIÊN LOGIN                      │
└───────────────────────────┬─────────────────────────────────┘
                            │
                            ▼
                   ┌─────────────────┐
                   │ Có ca làm việc? │
                   └────┬────────┬───┘
                        │        │
                    No  │        │ Yes
                        │        │
                        ▼        ▼
              ┌──────────────┐ ┌───────────────┐
              │ Bắt đầu ca   │ │ Đã check-in?  │
              └──────┬───────┘ └───┬───────┬───┘
                     │             │       │
                     └─────────────┘   No  │ Yes
                            │              │
                            ▼              ▼
                   ┌─────────────────┐   ┌──────────────┐
                   │  CHECK-IN       │   │  POS SCREEN  │
                   │  ─────────────  │   └──────┬───────┘
                   │ • Tiền mặt đầu  │          │
                   │ • Xác nhận hàng │          │
                   └────────┬────────┘          │
                            │                   │
                            └───────────────────┘
                                     │
                                     ▼
┌────────────────────────────────────────────────────────────┐
│              POS - BÁN HÀNG (Updated v2.0)                  │
│  ┌──────────────────────────────────────────────────┐     │
│  │  Sản phẩm 1:  [Input: 0]  │  15,000đ  │  0đ     │     │
│  │  Sản phẩm 2:  [Input: 0]  │  18,000đ  │  0đ     │     │
│  └──────────────────────────────────────────────────┘     │
│                                                             │
│  Tổng: 0đ          [⏳ 3]  [✓ Đã chốt]  [❌ Chốt ca]      │
│  [THANH TOÁN]  [XÓA]                                       │
└─────────────┬──────────────────┬────────────────────────┬─┘
              │                  │                        │
        Thanh toán         Xem đơn                  Chốt ca
              │                  │                        │
              ▼                  ▼                        │
     ┌─────────────────┐  ┌──────────────────┐          │
     │ PENDING SALE    │  │ ALL SALES LIST   │          │
     │ ─────────────   │  │ ───────────────  │          │
     │ • Lưu tạm thời  │  │ ⏳ 08:15 - 30k  │          │
     │ • Auto deduct   │  │ ✓ 08:20 - 45k   │          │
     │ • Quay lại POS  │  │ ✓ 09:00 - 60k   │          │
     └─────────────────┘  │                  │          │
                          │ [CHỐT TẤT CẢ]   │          │
                          │ [EDIT + NOTE]   │          │
                          └────────┬─────────┘          │
                                   │                    │
                            Chốt batch                  │
                                   │                    │
                                   ▼                    │
                          ┌─────────────────┐           │
                          │ BATCH BAN HANG  │           │
                          │ ──────────────  │           │
                          │ • Confirmed     │           │
                          │ • Update tồn    │           │
                          │ • → Back to POS │           │
                          └─────────────────┘           │
                                                         │
                                                         ▼
                                              ┌─────────────────┐
                                              │ SHIFT CLOSING   │
                                              │ ───────────────  │
                                              │ • Đếm tồn cuối  │
                                              │ • Nhập tiền TM  │
                                              │ • Tính chênh    │
                                              │ • Zalo report   │
                                              │ [← POS] option  │
                                              └────────┬────────┘
                                                       │
                                                       ▼
                                                 ┌──────────┐
                                                 │ COMPLETE │
                                                 └──────────┘
```

---

## 📝 CHI TIẾT TỪNG BƯỚC

### BƯỚC 1: LOGIN & REDIRECT

**URL**: `/`  
**Logic**:
```php
if (employee logged in) {
    $shift = Check active shift;
    if ($shift && $shift->trang_thai_checkin) {
        redirect('/admin/pos');
    } else {
        redirect('/admin/shift/check-in');
    }
}
```

**Files**:
- [web.php](file:///d:/Boong/bakery-system/routes/web.php)

---

### BƯỚC 2: CHECK-IN ĐẦU CA

**URL**: `/admin/shift/check-in`  
**Component**: `App\Livewire\Admin\Shift\ShiftCheckIn`

**Input**:
- Tiền mặt đầu ca (VND)
- Số lượng hàng nhận cho từng sản phẩm (auto-load từ phân bổ)

**Process**:
1. Load distribution data (từ `phan_bo_hang_diem_ban`)
2. Auto-fill số lượng phân bổ cho NV (chỉ first load, preserve edits)
3. Nhân viên xác nhận số lượng thực tế nhận
4. Submit → Save data

**Database Updates**:
```sql
-- Update shift
UPDATE ca_lam_viec 
SET tien_mat_dau_ca = ?, 
    trang_thai_checkin = true, 
    thoi_gian_checkin = NOW()
WHERE id = ?;

-- Create shift details
INSERT INTO chi_tiet_ca_lam 
(ca_lam_viec_id, san_pham_id, so_luong_nhan_ca) 
VALUES (?, ?, ?);

-- Mark distribution as received
UPDATE phan_bo_hang_diem_ban 
SET trang_thai = 'da_nhan', nguoi_nhan_id = ?
WHERE diem_ban_id = ? AND buoi = ? AND trang_thai = 'chua_nhan';
```

**Redirect**: → `/admin/pos`

**Files**:
- [ShiftCheckIn.php](file:///d:/Boong/bakery-system/app/Livewire/Admin/Shift/ShiftCheckIn.php)
- [shift-check-in.blade.php](file:///d:/Boong/bakery-system/resources/views/livewire/admin/shift/shift-check-in.blade.php)

---

### BƯỚC 3: POS - BÁN HÀNG NHANH (Updated v2.0)

**URL**: `/admin/pos`  
**Component**: `App\Livewire\Admin\Shift\QuickSale`  
**Middleware**: Auto-redirect if not checked in

**Features (v2.0)**:
- ✅ Load products từ `chi_tiet_ca_lam` (array format)
- ✅ Calculate `so_luong_con_lai` via accessor (nhận ca - đã bán)
- ✅ **Direct number input** (replaced +/- buttons)
- ✅ Min/Max validation on input
- ✅ Real-time total calculation with `wire:model.live`
- ✅ Color-coded stock levels
- ✅ Wake Lock API (screen always on)
- ✅ Navigation buttons:
  - 🟡 Yellow badge: Pending count
  - ⚪ White button: "Đã chốt" (Confirmed sales)
  - 🔴 Red button: Chốt ca (disabled if pending > 0)

**User Actions**:

#### 3.1. Thêm sản phẩm (v2.0)
```
Type number in input → Auto-validate (min=0, max=available) → Update total
```

**Validation**:
- Input type="number" với min="0" max="available"
- Real-time validation via `updatedDistributionData()`
- Auto-cap nếu vượt quá tồn kho
- Flash warning message

#### 3.2. Thanh toán
```
Tap [THANH TOÁN] → Confirm → Save to pending_sales → Auto-deduct inventory → Reset cart
```

**Database**:
```sql
INSERT INTO pending_sales (
    diem_ban_id,
    ca_lam_viec_id,
    nguoi_ban_id,
    thoi_gian,
    chi_tiet,      -- JSON: [{product_id, ten_sp, so_luong, gia, thanh_tien}]
    tong_tien,
    phuong_thuc_thanh_toan,
    trang_thai
) VALUES (?, ?, ?, NOW(), ?, ?, ?, 'pending');

-- Auto-update inventory
UPDATE chi_tiet_ca_lam
SET so_luong_ban = so_luong_ban + ?
WHERE ca_lam_viec_id = ? AND san_pham_id = ?;
```

**Note**: Inventory ĐƯỢC update ngay (v2.0 change!)

#### 3.3. Xem đơn
```
Tap [⏳ X] → Pending sales (chưa chốt)
Tap [✓ Đã chốt] → All sales (pending + confirmed)
```

**Files**:
- [QuickSale.php](file:///d:/Boong/bakery-system/app/Livewire/Admin/Shift/QuickSale.php)
- [quick-sale.blade.php](file:///d:/Boong/bakery-system/resources/views/livewire/admin/shift/quick-sale.blade.php)
- [ChiTietCaLam.php](file:///d:/Boong/bakery-system/app/Models/ChiTietCaLam.php) (Added accessor)

---

### BƯỚC 4A: QUẢN LÝ ĐƠN CHƯA CHỐT (Updated v2.0)

**URL**: `/admin/pos/pending`  
**Component**: `App\Livewire\Admin\Shift\PendingSalesList`

**Features (v2.0)**:
- ✅ Hiển thị pending sales của ca
- ✅ Show **💰 TM lý thuyết phải có** cho đơn tiền mặt
- ✅ Footer split: 💵 TM lý thuyết | 💳 Chuyển khoản | 📊 Tổng
- ✅ Batch **CHỐT TẤT CẢ** → Redirect về POS

**User Actions**:

#### 4.1. Chốt tất cả
```
Tap [CHỐT TẤT CẢ] → Confirm → Create batch → Redirect to /admin/pos
```

**Process (v2.0)**:
```php
DB::transaction(function () {
    // 1. Create batch
    $batch = BatchBanHang::createFromPending($allIds, Auth::id());
    
    // 2. Mark pending sales as confirmed
    PendingSale::whereIn('id', $allIds)
        ->update(['trang_thai' => 'confirmed']);
    
    // 3. Inventory already updated in QuickSale checkout
    // No need to update again
});

// 4. Redirect back to POS
return $this->redirect('/admin/pos', navigate: true);
```

#### 4.2. Xóa đơn (v2.0)
```
Tap [Xóa] → Reverse inventory → Mark cancelled
```

**Process**:
```php
// Restore inventory
$chiTietCaLam->decrement('so_luong_ban', $qty);

// Mark as cancelled
$sale->update(['trang_thai' => 'cancelled']);
```

**Files**:
- [PendingSalesList.php](file:///d:/Boong/bakery-system/app/Livewire/Admin/Shift/PendingSalesList.php)
- [pending-sales-list.blade.php](file:///d:/Boong/bakery-system/resources/views/livewire/admin/shift/pending-sales-list.blade.php)

---

### BƯỚC 4B: QUẢN LÝ ĐƠN ĐÃ CHỐT (NEW v2.0)

**URL**: `/admin/pos/confirmed`  
**Component**: `App\Livewire\Admin\Shift\ConfirmedSalesList`

**Features (NEW!)**:
- ✅ Hiển thị TẤT CẢ đơn (pending + confirmed)
- ✅ Phân biệt: 
  - ⏳ Pending: Badge vàng "Chờ chốt"
  - ✓ Confirmed: Border xanh + badge "Đã chốt"
- ✅ **Edit confirmed orders** với popup chi tiết:
  - Thay đổi số lượng sản phẩm
  - Đổi phương thức thanh toán (TM ↔ CK)
  - **Bắt buộc nhập lý do điều chỉnh**
- ✅ Hiển thị lịch sử điều chỉnh (notes với timestamp)

**User Actions**:

#### 4B.1. Chỉnh sửa đơn đã chốt
```
Tap [✏️] → Popup modal → Edit SL + PT thanh toán → Nhập note → Lưu
```

**Edit Modal**:
- Product list với input số lượng
- Toggle TM/CK buttons
- Textarea note (required, min 5 chars)
- Auto-calculate tổng tiền

**Process**:
```php
DB::transaction(function() {
    // 1. Restore old inventory (reverse)
    foreach ($oldItems) {
        $chiTietCaLam->decrement('so_luong_ban', $oldQty);
    }
    
    // 2. Apply new quantities
    foreach ($newItems) {
        $chiTietCaLam->increment('so_luong_ban', $newQty);
    }
    
    // 3. Update batch
    $batch->chi_tiet_don = $newChiTietDon;
    $batch->tong_tien = $newTotal;
    
    // 4. Update payment method in PendingSale
    PendingSale::where('id', $saleId)
        ->update(['phuong_thuc_thanh_toan' => $newMethod]);
    
    // 5. Append note with timestamp + user name
    $batch->ghi_chu .= "\n[08/12 15:30] Nguyen Van A: Khách trả 2 bánh";
    $batch->save();
});
```

**Audit Trail**:
```
[08/12 10:30] Nguyen Van A: Khách trả lại 2 bánh mì vì không tươi
[08/12 11:45] Tran Thi B: Chuyển sang thanh toán chuyển khoản theo yêu cầu
```

**Files**:
- [ConfirmedSalesList.php](file:///d:/Boong/bakery-system/app/Livewire/Admin/Shift/ConfirmedSalesList.php) (NEW!)
- [confirmed-sales-list.blade.php](file:///d:/Boong/bakery-system/resources/views/livewire/admin/shift/confirmed-sales-list.blade.php) (NEW!)

---

### BƯỚC 5: CHỐT CA (Updated v2.0)

**URL**: `/admin/shift/closing`  
**Component**: `App\Livewire\Admin\Shift\ShiftClosing`

**Features (v2.0)**:
- ✅ Hiển thị tồn đầu ca (từ check-in)
- ✅ Nhập tồn cuối ca (auto-preserve edits)
- ✅ **Chỉ nhập tiền mặt đang giữ** (không nhập CK)
- ✅ Sales summary: Count + Total (TM vs CK)
- ✅ Tự động tính doanh thu
- ✅ **Generate Zalo format report**
- ✅ Upload ảnh két + hàng
- ✅ **[← POS] button** để quay lại POS

**Calculation (v2.0)**:
```php
// Get sales data from BatchBanHang
$batches = BatchBanHang::where('ca_lam_viec_id', $shiftId)->get();

// Calculate revenue
$cashSales = $batches->where('payment_method', 'tien_mat')->sum('tong_tien');
$transferSales = $batches->where('payment_method', 'chuyen_khoan')->sum('tong_tien');

// Actual cash = Input cash holding - Opening cash + All transfers
$actualRevenue = $cashHolding - $openingCash + $transferSales;

// Theoretical revenue = Cash sales + Transfer sales
$theoreticalRevenue = $cashSales + $transferSales;

// Discrepancy
$discrepancy = $actualRevenue - $theoreticalRevenue;
```

**Zalo Report Format (v2.0)**:
```
CA SÁNG - 08/12/2024
Người bán: Nguyễn Văn A

TIỀN:
Tiền mặt đầu ca: 500,000đ
Tổng tiền mặt đang giữ: 2,350,000đ
Bán tiền mặt: 1,850,000đ (5 đơn)
Bán chuyển khoản: 450,000đ (2 đơn)
─────────────────
• Doanh thu: 2,300,000đ
• Chênh lệch: 0đ

HÀNG HÓA:
Bánh mì: Nhận 50 | Bán 42 | Còn 8 | Lệch: 0
Bánh bao: Nhận 30 | Bán 25 | Còn 5 | Lệch: 0
```

**Database**:
```sql
INSERT INTO phieu_chot_ca (
    ma_phieu, diem_ban_id, nguoi_chot_id, ca_lam_viec_id,
    ngay_chot, gio_chot,
    tien_mat, tien_chuyen_khoan, 
    tong_tien_thuc_te, tong_tien_ly_thuyet, tien_lech,
    ton_dau_ca, ton_cuoi_ca,  -- JSON (array)
    anh_tien_mat, anh_hang_hoa,  -- JSON
    ghi_chu, trang_thai
) VALUES (..., 'cho_duyet');

UPDATE ca_lam_viec 
SET trang_thai = 'da_ket_thuc'
WHERE id = ?;
```

**Files**:
- [ShiftClosing.php](file:///d:/Boong/bakery-system/app/Livewire/Admin/Shift/ShiftClosing.php)
- [shift-closing.blade.php](file:///d:/Boong/bakery-system/resources/views/livewire/admin/shift/shift-closing.blade.php)
- [PhieuChotCa.php](file:///d:/Boong/bakery-system/app/Models/PhieuChotCa.php)

---

## 🗄️ DATABASE SCHEMA

### Tables Involved

#### 1. `ca_lam_viec` (Shift) - Updated
```sql
- id
- diem_ban_id
- nguoi_dung_id
- ngay_lam, gio_bat_dau, gio_ket_thuc
- tien_mat_dau_ca         -- ✅ Added
- trang_thai_checkin      -- ✅ Added (boolean)
- thoi_gian_checkin       -- ✅ Added (datetime)
- trang_thai: ENUM('chua_bat_dau', 'dang_lam', 'da_ket_thuc')
```

#### 2. `chi_tiet_ca_lam` (Shift Details) - Updated
```sql
- id
- ca_lam_viec_id
- san_pham_id
- so_luong_nhan_ca      -- Opening stock
- so_luong_giao_ca      -- Closing stock
- so_luong_ban          -- Sold (updated real-time from QuickSale)
-- ✅ Accessor: so_luong_con_lai = nhan_ca - ban
```

#### 3. `pending_sales` (Updated)
```sql
- id
- diem_ban_id
- ca_lam_viec_id
- nguoi_ban_id
- thoi_gian
- chi_tiet              -- JSON
- tong_tien
- phuong_thuc_thanh_toan -- ✅ Added (tien_mat/chuyen_khoan)
- trang_thai: ENUM('pending', 'confirmed', 'cancelled')
```

#### 4. `batch_ban_hang` (Updated)
```sql
- id
- diem_ban_id
- ca_lam_viec_id
- nguoi_chot_id
- ngay_chot, gio_chot
- so_don
- tong_tien
- chi_tiet_don          -- JSON (array of sales with chi_tiet)
- ghi_chu               -- ✅ Audit trail for edits
```

### Data Flow (v2.0)

```
phan_bo_hang_diem_ban (Distribution)
         ↓
chi_tiet_ca_lam (Check-in setup)
         ↓
pending_sales (Quick Sales) → chi_tiet_ca_lam (so_luong_ban +1)
         ↓
batch_ban_hang (Batch Confirm) → pending_sales (mark confirmed)
         ↓
[EDIT confirmed] → batch_ban_hang update + chi_tiet_ca_lam adjust + append note
         ↓
phieu_chot_ca (Shift Closing)
```

---

## 🆕 TÍNH NĂNG MỚI v2.0

### 1. Input Fields thay +/-
- **Old**: Buttons 64x64px
- **New**: `<input type="number">` với validation
- **Benefit**: Nhập nhanh, ít lỗi

### 2. Real-time Inventory Deduction
- **Old**: Chờ confirm batch mới trừ
- **New**: Trừ ngay khi checkout pending
- **Benefit**: Tránh oversell

### 3. Theoretical Cash Display
- **Location**: Pending sales list
- **Show**: "💰 TM lý thuyết phải có" cho mỗi đơn TM
- **Footer**: Split TM/CK với tổng
- **Benefit**: Dễ đối chiếu tiền

### 4. Confirmed Sales Management
- **URL**: `/admin/pos/confirmed`
- **Features**:
  - View all (pending + confirmed)
  - Edit confirmed với note bắt buộc
  - Audit trail
- **Benefit**: Sửa lỗi sau khi chốt

### 5. Zalo Report Format
- **Auto-generate**: Copy-ready text
- **Include**: Sales summary (TM vs CK)
- **Benefit**: Báo cáo nhanh cho nhóm

### 6. Navigation Improvements
- **POS Header**: 3 buttons (Pending | Confirmed | Closing)
- **After confirmAll**: Auto redirect về POS
- **Shift Closing**: Có nút [← POS]
- **Benefit**: Workflow mượt hơn

---

## 🛠️ TROUBLESHOOTING

### Issue 1: "Vui lòng check-in trước"
**Giải pháp**: Quay về `/admin/shift/check-in`

### Issue 2: Input không update
**Nguyên nhân**: Accessor `so_luong_con_lai` thiếu
**Fix**: Added in `ChiTietCaLam` model

### Issue 3: Edit confirmed không lưu
**Check**: 
- Note có đủ 5 ký tự?
- `ghi_chu` field có trong BatchBanHang fillable?

### Issue 4: Inventory bị sai
**Nguyên nhân**: Dùng accessor thay vì field thật
**Fix**: `increment('so_luong_ban')` thay vì `decrement('so_luong_con_lai')`

---

## 🎓 TRAINING NOTES

### Cho nhân viên mới

1. **Check-in**: 
   - Load tự động từ phân bổ
   - Chỉnh nếu khác thực tế

2. **Bán hàng**: 
   - Gõ số lượng trực tiếp
   - Hệ thống tự giới hạn

3. **Chốt đơn**: 
   - Bấm "CHỐT TẤT CẢ" → Tự về POS
   - Xem "Đã chốt" để kiểm tra

4. **Sửa đơn đã chốt**:
   - Nhấn ✏️ trên đơn
   - **Bắt buộc ghi lý do** (>= 5 ký tự)
   - Admin sẽ thấy lịch sử

5. **Chốt ca**:
   - Chỉ nhập tiền mặt đang giữ
   - Copy báo cáo Zalo gửi group

---

**Document Version**: 2.0  
**Last Review**: 08/12/2024  
**Changes**: Added direct input, confirmed management, Zalo report, navigation improvements
