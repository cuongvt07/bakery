**Chức năng:**
```
A. Dashboard Tổng Quan
├─ Card tổng hợp:
│  ├─ Tổng doanh thu hôm nay
│  ├─ Số điểm đã chốt ca / Tổng số điểm
│  └─ Cảnh báo lệch tiền (nếu có)
│
├─ Bảng danh sách phiếu chốt ca
│  ├─ Cột: Điểm bán | Nhân viên | Ca | Thời gian | Lệch tiền | Lệch hàng | Trạng thái
│  ├─ Highlight đỏ nếu lệch > 50,000 VNĐ
│  └─ Nút [Xem chi tiết]
│
└─ Bộ lọc:
   ├─ Theo ngày
   ├─ Theo điểm bán
   └─ Chỉ hiển thị phiếu có lệch

B. Chi Tiết Phiếu Chốt Ca
├─ Thông tin cơ bản:
│  ├─ Điểm bán, Nhân viên, Ca làm
│  ├─ Thời gian chốt
│  └─ Ảnh két tiền
│
├─ Bảng chi tiết hàng hóa:
│  ├─ Sản phẩm | Tồn đầu | Tồn cuối | Đã bán | Giá | Thành tiền
│  └─ Tổng cộng
│
├─ Bảng tiền:
│  ├─ Tiền mặt: XXX
│  ├─ Tiền CK: XXX
│  ├─ Tổng thực tế: XXX
│  ├─ Lý thuyết: XXX
│  └─ Chênh lệch: XXX (Màu đỏ nếu lệch)
│
└─ Nút hành động:
   ├─ [Duyệt phiếu]
   ├─ [Từ chối - Yêu cầu giải trình]
   └─ [Xuất PDF]
```

**Giao diện Dashboard:**
```
┌──────────────────────────────────────────────────────────────┐
│ DASHBOARD CHỐT CA - 02/12/2024                               │
├──────────────────────────────────────────────────────────────┤
│ 💰 Tổng doanh thu: 2,500,000 VNĐ                            │
│ ✅ Đã chốt: 5/8 điểm                                        │
│ ⚠️  Cảnh báo: 2 điểm có lệch tiền                           │
├──────────────────────────────────────────────────────────────┤
│ Bộ lọc: [02/12/2024] [Tất cả điểm ▼] [☑ Chỉ hiện lệch]   │
├──────────────────────────────────────────────────────────────┤
│ Điểm bán      │ NV   │ Ca    │ Giờ  │ Lệch tiền │ Trạng thái │
├──────────────────────────────────────────────────────────────┤
│ Trần Duy Hưng │ An   │ Sáng  │ 12:05│ -50,000   │ ⚠️ Chờ    │
│ Cầu Giấy      │ Bình │ Sáng  │ 12:10│ 0         │ ✅ Duyệt  │
│ Thanh Xuân    │ Cường│ Sáng  │ 12:15│ +30,000   │ ⚠️ Chờ    │
│ Hoàng Mai     │ Dung │ Sáng  │ 12:20│ 0         │ ✅ Duyệt  │
│ Hai Bà Trưng  │ Em   │ Sáng  │ 12:25│ 0         │ ✅ Duyệt  │
└──────────────────────────────────────────────────────────────┘
      [Xem chi tiết]
```

---

## 📋 CHECKLIST CHI TIẾT

### Sprint 1: Module Phân Bổ Hàng (3-4 ngày)
- [ ] **Backend**
  - [ ] Controller `PhieuXuatHangTongController`
  - [ ] Model `PhieuXuatHangTong`, `ChiTietPhieuXuatTong`
  - [ ] Model `PhanBoHangDiemBan`, `ChiTietPhanBo`
  - [ ] Validation rules
  - [ ] Logic tự động cập nhật `ton_kho_diem_ban.ton_dau_ca`

- [ ] **Frontend (Admin Web)**
  - [ ] Livewire Component: Form nhập phiếu xuất tổng
  - [ ] Livewire Component: Form phân bổ cho từng điểm
  - [ ] Upload ảnh hàng xuất
  - [ ] Hiển thị số lượng còn lại khi phân bổ
  - [ ] Validation client-side

- [ ] **Test**
  - [ ] Test tạo phiếu xuất tổng
  - [ ] Test phân bổ hàng
  - [ ] Test tự động tạo tồn đầu ca

---

### Sprint 2: Module Chốt Ca (4-5 ngày)
- [ ] **Backend**
  - [ ] Controller `ChotCaController`
  - [ ] Model `PhieuChotCa`
  - [ ] Logic tính toán:
    - [ ] Số bán = Tồn đầu - Tồn cuối
    - [ ] Tiền lý thuyết
    - [ ] Chênh lệch tiền/hàng
  - [ ] Logic lấy tồn đầu ca:
    - [ ] Ca đầu: Từ phân bổ
    - [ ] Ca sau: Từ ca trước
  - [ ] Cập nhật `ton_kho_diem_ban.ton_cuoi_ca`

- [ ] **Frontend (Mobile Web)**
  - [ ] Livewire Component: Form chốt ca
  - [ ] Tự động load tồn đầu ca
  - [ ] Nhập tồn cuối từng sản phẩm
  - [ ] Nhập tiền mặt/CK
  - [ ] Upload ảnh két tiền
  - [ ] Hiển thị preview tính toán trước khi submit
  - [ ] Mobile-friendly UI (nút to, dễ chạm)

- [ ] **Test**
  - [ ] Test chốt ca đầu tiên
  - [ ] Test chốt ca thứ 2 (nhận tồn từ ca trước)
  - [ ] Test tính toán lệch tiền
  - [ ] Test validation

---

### Sprint 3: Dashboard Admin (2-3 ngày)
- [ ] **Backend**
  - [ ] API lấy danh sách phiếu chốt ca
  - [ ] Bộ lọc: Ngày, Điểm bán, Trạng thái
  - [ ] API duyệt/từ chối phiếu
  - [ ] Tính tổng doanh thu

- [ ] **Frontend (Admin Web)**
  - [ ] Livewire Component: Dashboard tổng quan
  - [ ] Cards: Doanh thu, Số điểm đã chốt, Cảnh báo
  - [ ] Bảng danh sách phiếu chốt (Livewire Table)
  - [ ] Modal xem chi tiết phiếu
  - [ ] Highlight đỏ khi lệch cao
  - [ ] Nút duyệt/từ chối

- [ ] **Test**
---

### PHASE 3: Báo Cáo & Tối Ưu (Tuần 5-6)
- [ ] **Báo cáo doanh thu**
  - [ ] Theo ngày/tuần/tháng
  - [ ] Theo điểm bán
  - [ ] Biểu đồ xu hướng

- [ ] **Báo cáo tồn kho**
  - [ ] Tồn kho từng điểm
  - [ ] Cảnh báo sắp hết hàng

- [ ] **Tối ưu hiệu năng**
  - [ ] Cache dữ liệu
  - [ ] Tối ưu query
  - [ ] Mobile performance

---

## 🎯 MỤC TIÊU CỐT LÕI

**Sau Sprint 1-3 (10-12 ngày), hệ thống phải đạt được:**

✅ Admin phân bổ hàng cho điểm bán mỗi sáng (5 phút)  
✅ Nhân viên chốt ca nhanh gọn trên mobile (3 phút)  
✅ Admin nhìn ngay dashboard biết điểm nào lệch tiền (1 phút)  
✅ Toàn bộ luồng hoạt động mượt mà, không lỗi  

→ **Đủ để đưa vào vận hành thực tế!**