# DANH SÁCH KIỂM SOÁT MODULE - BAKERY MANAGEMENT SYSTEM

> **Cập nhật**: 03/12/2024  
> **Mục đích**: Theo dõi tiến độ phát triển từng module

---

## ✅ CÁC MODULE ĐÃ HOÀN THÀNH

### 1. Module Người Dùng (Users Management)
#### ✅ Đã làm:
- [x] CRUD cơ bản (Tạo, Sửa, Xóa, Xem)
- [x] Phân quyền 2 vai trò: Admin, Nhân viên
- [x] Filter theo vai trò, trạng thái, điểm bán
- [x] Sort tăng/giảm theo các cột
- [x] Search theo tên, email, SĐT
- [x] Pagination (15/25/50/100)
- [x] Export Excel
- [x] Gán nhân viên vào điểm bán

#### ⏳ Chưa làm:
- [ ] Quản lý lương (theo ngày/theo giờ)
- [ ] Upload ảnh đại diện
- [ ] Lịch sử thay đổi thông tin

---

### 2. Module Điểm Bán (Agency Management)
#### ✅ Đã làm:
- [x] CRUD điểm bán
- [x] Quản lý thông tin cơ bản (tên, địa chỉ, SĐT)
- [x] Filter theo trạng thái
- [x] Sort và Search
- [x] Pagination
- [x] Export Excel

#### ⏳ Chưa làm:
- [ ] Quản lý GPS (Vĩ độ, Kinh độ) cho check-in
- [ ] Quản lý thông tin vật dụng (JSON)
- [ ] Quản lý chi phí (Điện, nước, tiền nhà, tiền luật)
- [ ] Quản lý hợp đồng thuê mặt bằng
- [ ] Bản đồ điểm bán (Map view)
- [ ] Nhắc nhở đóng tiền

---

### 3. Module Sản Phẩm (Products)
#### ✅ Đã làm:
- [x] CRUD sản phẩm
- [x] Danh mục sản phẩm (Categories)
- [x] Quản lý giá bán
- [x] Đơn vị phân phối (Khay, Hộp)
- [x] Quy đổi đơn vị (1 Khay = 10 Cái)
- [x] **UI Quy cách đóng gói** (Form có section riêng, preview trực tiếp)
- [x] **Hiển thị quy cách trong danh sách** (Badge màu xanh)
- [x] Filter theo danh mục, trạng thái
- [x] Sort theo giá, tên, ngày tạo
- [x] Search và Pagination
- [x] Export Excel

#### ⏳ Chưa làm:
- [ ] Upload ảnh sản phẩm
- [ ] Quản lý biến thể (Size, Loại)
- [ ] Quản lý công thức sản xuất (Nguyên liệu)
- [ ] Kế hoạch sản xuất

---

### 4. Module Kho (Warehouse)
#### ✅ Đã làm:
- [x] CRUD Nhà cung cấp (Suppliers)
- [x] CRUD Nguyên liệu (Ingredients)
- [x] Filter và Sort
- [x] Search và Pagination
- [x] Export Excel
- [x] Cảnh báo tồn kho thấp (UI highlight)

#### ⏳ Chưa làm:
- [ ] Phiếu nhập kho nguyên liệu
- [ ] Tồn kho thành phẩm tại kho tổng
- [ ] Quản lý hạn sử dụng (HSD)
- [ ] Tự động trừ nguyên liệu khi sản xuất
- [ ] Báo cáo nhập/xuất kho

---

### 5. Module Phân Bổ Hàng (Distribution) ⭐ MỚI
#### ✅ Đã làm:
- [x] Tạo phiếu xuất hàng tổng (PhieuXuatHangTong)
- [x] Quản lý nhiều mẻ hàng trong ngày (Sáng, Chiều, Tiếp ứng)
- [x] Phân bổ hàng cho từng điểm bán
- [x] Hỗ trợ phân ca (Morning/Afternoon)
- [x] Danh sách phân bổ (Grouped by Date)
- [x] Xóa mẻ hàng (chỉ với mẻ chưa phân bổ)
- [x] Hiển thị số lượng còn lại khi phân bổ
- [x] Tự động quy đổi đơn vị (Khay -> Cái)
- [x] Filter theo ngày
- [x] Batch Tabs UI (Switch giữa các mẻ)

#### ⏳ Chưa làm:
- [ ] Upload ảnh hàng xuất
- [ ] Xác nhận nhận hàng từ nhân viên
- [ ] Lịch sử phân bổ (Timeline view)
- [ ] In phiếu phân bổ (PDF)
- [ ] Luân chuyển hàng giữa các điểm

---

### 6. Module Ca Làm Việc & Chốt Ca (Shift Management) ⭐ MỚI
#### ✅ Đã làm:
- [x] **Check-in Ca**:
  - [x] Xác nhận tiền đầu ca
  - [x] Xác nhận hàng nhận được
  - [x] Tự động load hàng từ phân bổ
  - [x] Lưu vào `chi_tiet_ca_lam`
- [x] **Chốt Ca** (Shift Closing):
  - [x] Nhập tồn cuối từng sản phẩm
  - [x] Nhập tiền mặt, chuyển khoản
  - [x] Tính toán tự động: Số bán, Doanh thu lý thuyết, Chênh lệch
  - [x] Upload ảnh két tiền và hàng tồn (Multiple images)
  - [x] Cập nhật `chi_tiet_ca_lam` với tồn cuối
  - [x] Kiểm tra bắt buộc check-in trước khi chốt

#### ⏳ Chưa làm:
- [ ] Dashboard quản lý ca làm việc
- [ ] Xếp lịch ca tự động
- [ ] Yêu cầu đổi ca, xin nghỉ
- [ ] Sinh text Zalo tự động cho báo cáo
- [ ] Check-in/out với GPS
- [ ] Chụp ảnh check-in đầu ca, giữa ca
- [ ] Chấm công (Attendance tracking)


---

### 7. Module Quản lý Sản xuất ⭐ MỚI
#### ✅ Đã làm:
- [x] **Công thức sản xuất**:
  - [x] CRUD công thức
  - [x] Dynamic ingredients (thêm/xóa nguyên liệu)
  - [x] Tự động tính chi phí (tổng + /đơn vị)
- [x] **Mẻ sản xuất (Production Batch)**:
  - [x] Tạo mẻ (auto-generate mã: SANG-20241203-001)
  - [x] Quản lý theo Ngày/Buổi
  - [x] QC - Kiểm tra chất lượng
  - [x] Upload ảnh lỗi sản phẩm
  - [x] Tính tỉ lệ hỏng tự động
  - [x] Xác nhận thành phẩm
- [x] **Quản lý nguyên liệu**:
  - [x] Hiển thị NVL cần khi tạo mẻ
  - [x] Kiểm tra tồn kho NVL
  - [x] Cảnh báo thiếu NVL
  - [x] Tự động trừ kho khi QC xong
- [x] Filter, Sort, Search, Pagination
- [x] Sidebar menu (Công thức + Mẻ SX)

#### ⏳ Chưa làm:
- [ ] Tích hợp với Phân bổ (chọn mẻ cụ thể)
- [ ] Báo cáo sản xuất (hiệu suất, tỉ lệ hỏng)
- [ ] Kế hoạch sản xuất tự động

---

### 8. Module Bán Hàng (POS) 🔴 CHƯA LÀM
#### ✅ Đã làm:
- [ ] *Chưa có gì*

#### ⏳ Chưa làm:
- [ ] Giao diện POS Mobile (Siêu tối giản)
- [ ] Nút Cộng/Trừ số lượng theo sản phẩm
- [ ] Màn hình luôn sáng (Wake lock)
- [ ] Tạo đơn hàng
- [ ] Thanh toán (Tiền mặt/CK)
- [ ] In hóa đơn (Bluetooth printer)
- [ ] Lịch sử bán hàng

---

### 8. Module Dashboard Admin 🔴 CHƯA LÀM
#### ✅ Đã làm:
- [ ] *Chưa có gì*

#### ⏳ Chưa làm:
- [ ] Cards tổng quan (Doanh thu, Chốt ca, Cảnh báo)
- [ ] Danh sách phiếu chốt ca
- [ ] Filter: Ngày, Điểm bán, Trạng thái
- [ ] Highlight đỏ khi lệch > 50,000 VNĐ
- [ ] Modal xem chi tiết phiếu chốt ca
- [ ] Duyệt/Từ chối phiếu chốt ca
- [ ] Tính tổng doanh thu hệ thống
- [ ] Realtime update (Livewire Poll)

---

### 9. Module Báo Cáo (Reports) 🔴 CHƯA LÀM
#### ✅ Đã làm:
- [ ] *Chưa có gì*

#### ⏳ Chưa làm:
- [ ] Báo cáo doanh thu (Theo ngày/tuần/tháng)
- [ ] Báo cáo doanh thu theo điểm bán
- [ ] Báo cáo tồn kho từng điểm
- [ ] Báo cáo chênh lệch (Discrepancy report)
- [ ] Biểu đồ xu hướng (Charts)
- [ ] Cảnh báo sắp hết hàng
- [ ] Export PDF

---

### 10. Module Thông Báo & Sự Cố 🔴 CHƯA LÀM
#### ✅ Đã làm:
- [ ] *Chưa có gì*

#### ⏳ Chưa làm:
- [ ] Báo cáo sự cố từ nhân viên
- [ ] Dashboard sự cố cho Admin
- [ ] Xử lý sự cố (Đang xử lý -> Đã xong)
- [ ] Tích hợp Lark Webhook
- [ ] Thông báo chung (Broadcast)
- [ ] Đọc/chưa đọc thông báo

---

### 11. Module Lương 🔴 CHƯA LÀM
#### ✅ Đã làm:
- [ ] *Chưa có gì*

#### ⏳ Chưa làm:
- [ ] Bảng lương tháng
- [ ] Tính công tự động từ chấm công
- [ ] Các khoản trừ/cộng
- [ ] Lịch sử thanh toán lương
- [ ] Export bảng lương

---

## 🔥 ƯU TIÊN TIẾP THEO (ROADMAP)

### Phase Hiện tại: Sprint 3 - Dashboard Admin (2-3 ngày)
**Mục tiêu**: Giúp Admin giám sát toàn bộ hoạt động

1. **Dashboard Tổng Quan** (⏰ Ưu tiên cao)
   - Cards: Doanh thu hôm nay, Số điểm đã chốt, Cảnh báo lệch
   - Danh sách phiếu chốt ca (Table với filter)
   - Highlight đỏ khi lệch tiền > 50k

2. **Quản Lý Phiếu Chốt Ca** (⏰ Ưu tiên cao)
   - Xem chi tiết phiếu (Modal)
   - Duyệt/Từ chối phiếu
   - Xem ảnh két tiền và hàng tồn

---

### Phase Tiếp Theo: Sprint 4 - POS Mobile (3-4 ngày)
**Mục tiêu**: Nhân viên bán hàng nhanh gọn

1. **POS Interface** (⏰ Ưu tiên cao)
   - Giao diện siêu tối giản (Mobile-first)
   - Nút Cộng/Trừ số lượng
   - Thanh toán (Tiền mặt/CK)

2. **Tích hợp với Chốt Ca** (⏰ Ưu tiên cao)
   - Tự động tính số lượng đã bán từ POS
   - Đối chiếu với tồn cuối khi chốt ca

---

### Phase 3: Báo Cáo & Tối Ưu (Tuần 5-6)
1. **Báo cáo doanh thu** (⏰ Ưu tiên trung bình)
2. **Báo cáo tồn kho** (⏰ Ưu tiên trung bình)
3. **Tối ưu performance** (⏰ Ưu tiên thấp)

---

## 📊 THỐNG KÊ TỔNG QUAN

| Module | Tỷ lệ hoàn thành | Trạng thái |
|--------|------------------|-----------|
| Người dùng | 80% | ✅ OK |
| Điểm bán | 60% | ⚠️ Cần bổ sung GPS, Chi phí |
| **Sản phẩm** | 90% | ✅ **Tốt** |
| Kho | 40% | ⚠️ Thiếu Nhập kho, HSD |
| **Quản lý Sản xuất** | 85% | ✅ **Tốt** |
| **Phân bổ hàng** | 90% | ✅ **Tốt** |
| **Ca làm & Chốt ca** | 85% | ✅ **Tốt** |
| POS | 0% | 🔴 Chưa làm |
| Dashboard Admin | 0% | 🔴 Chưa làm |
| Báo cáo | 0% | 🔴 Chưa làm |
| Thông báo & Sự cố | 0% | 🔴 Chưa làm |
| Lương | 0% | 🔴 Chưa làm |

---

## 🎯 MỤC TIÊU CỐT LÕI (MVP)

**Sau Sprint 1-3 (10-12 ngày), hệ thống phải đạt được:**

✅ Admin phân bổ hàng cho điểm bán mỗi sáng (5 phút) - **ĐÃ XON**G
✅ Nhân viên chốt ca nhanh gọn trên mobile (3 phút) - **ĐÃ XONG**  
🔄 Admin nhìn ngay dashboard biết điểm nào lệch tiền (1 phút) - **ĐANG LÀM**  
⏳ Toàn bộ luồng hoạt động mượt mà, không lỗi - **CẦN TEST**

→ **Sẵn sàng 70%!** Còn thiếu Dashboard & POS là có thể vận hành thử nghiệm.

---

## 📝 GHI CHÚ

### Các tính năng nâng cao đã triển khai:
1. ✅ **Quản lý Mẻ Hàng** (Batch Management)
2. ✅ **Phân ca Sáng/Chiều** (Session Management)
3. ✅ **Check-in Ca** (Shift Check-in)
4. ✅ **Quy đổi đơn vị** (Unit Conversion)
5. ✅ **Upload nhiều ảnh** (Multiple Image Upload)
6. ✅ **UI Quy cách đóng gói** (Intuitive Packaging Specification Form)
7. ✅ **Module Sản xuất** (Production Management with Recipe & QC) ⭐ MỚI
8. ✅ **Quản lý Nguyên liệu** (Ingredient Tracking & Auto Deduction) ⭐ MỚI

### Điểm mạnh hiện tại:
- Phân bổ hàng linh hoạt (Nhiều mẻ/ngày)
- Chốt ca chính xác với ảnh minh chứng
- **Sản xuất có công thức & QC chuyên nghiệp**
- **Tự động trừ nguyên liệu khi hoàn thành**
- UI/UX đẹp, responsive

### Điểm cần cải thiện:
- Thiếu POS (Đây là core để bán hàng)
- Thiếu Dashboard (Để Admin giám sát)
- Thiếu Báo cáo (Để phân tích kinh doanh)
- Chưa tích hợp Phân bổ với Mẻ sản xuất

---

**Cập nhật lần cuối**: 03/12/2024 21:36
