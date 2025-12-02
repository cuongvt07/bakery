# BÁO CÁO TIẾN ĐỘ DỰ ÁN & KẾ HOẠCH TIẾP THEO

## I. CÁC MODULE ĐÃ HOÀN THÀNH (100%)
Các module dưới đây đã có đầy đủ chức năng: **Danh sách, Thêm, Sửa, Xóa, Tìm kiếm, Bộ lọc nâng cao, Phân trang, Xuất Excel**.

1.  **Quản lý Người dùng (Users)**
    *   Phân quyền Admin/Nhân viên.
    *   Lọc theo vai trò, trạng thái.
2.  **Quản lý Điểm bán (Agencies)**
    *   Quản lý thông tin cửa hàng.
    *   Lọc theo trạng thái hoạt động.
3.  **Quản lý Sản phẩm (Products)**
    *   Quản lý giá bán, đơn vị tính.
    *   Lọc theo danh mục, trạng thái hàng.
4.  **Quản lý Danh mục (Categories)**
    *   Phân loại sản phẩm.
5.  **Quản lý Nhà cung cấp (Suppliers)**
    *   Thông tin đối tác cung cấp nguyên liệu.
6.  **Quản lý Nguyên liệu (Ingredients)**
    *   Quản lý tồn kho nguyên liệu.
    *   Cảnh báo tồn kho thấp.

---

## II. CORE FLOW: VẬN HÀNH & CHỐT CA (Đang triển khai)
Đây là luồng nghiệp vụ quan trọng nhất đang được tập trung phát triển.

### ✅ Đã hoàn thành
1.  **Cơ sở dữ liệu (Database)**
    *   Bảng `ton_kho_diem_ban`: Theo dõi tồn kho từng ngày tại từng điểm.
    *   Bảng `phieu_chot_ca`: Lưu trữ kết quả chốt ca (Tiền, Hàng, Lệch).
    *   Bảng `phieu_xuat_hang_tong` & `phan_bo_hang`: Phục vụ luồng phân phối từ xưởng.
2.  **Dữ liệu mẫu (Seed Data)**
    *   Tự động tạo dữ liệu để test ngay luồng chốt ca (Sản phẩm, Nhân viên, Ca làm việc, Tồn đầu ca).
3.  **Tính năng Chốt Ca (Shift Closing)**
    *   **Giao diện**: Form nhập tiền mặt/CK và kiểm đếm hàng hóa tồn cuối.
    *   **Logic Backend**:
        *   Tự động tính số lượng bán = Tồn đầu - Tồn cuối.
        *   Tự động tính doanh thu lý thuyết = Số lượng bán * Giá bán.
        *   **Tự động tính chênh lệch** (Thừa/Thiếu tiền).
    *   **Lưu trữ**: Ghi nhận phiếu chốt ca vào hệ thống.

---

## III. KẾ HOẠCH TIẾP THEO (Next Steps)

### Ưu tiên 1: Dashboard & Cảnh báo (Ngay lập tức)
*   [ ] **Dashboard Admin**:
    *   Hiển thị ngay các phiếu chốt ca có **lệch tiền/hàng** để xử lý gấp.
    *   Biểu đồ doanh thu ngày hôm nay của toàn hệ thống.
*   [ ] **Chi tiết phiếu chốt ca**:
    *   Giao diện cho Admin xem lại chi tiết phiếu chốt (Ai chốt, lệch bao nhiêu, lý do).
    *   Chức năng **Duyệt/Từ chối** phiếu chốt ca.

### Ưu tiên 2: Luồng Phân Bổ Hàng (Distribution)
*   [ ] **Tạo Phiếu Xuất Tổng**:
    *   Admin tạo 1 phiếu xuất kho tổng từ xưởng sản xuất cho ngày hôm nay.
*   [ ] **Phân Bổ Hàng**:
    *   Giao diện chia hàng từ phiếu tổng về từng điểm bán (có thể gợi ý số lượng dựa trên lịch sử bán).
    *   In phiếu giao hàng cho shipper.

### Ưu tiên 3: Quản lý Kho Điểm Bán
*   [ ] **Xem Tồn Kho**:
    *   Admin xem được tồn kho hiện tại của bất kỳ điểm bán nào (Real-time).
*   [ ] **Luân Chuyển Hàng**:
    *   Tạo phiếu chuyển hàng từ Điểm A sang Điểm B (xử lý trường hợp thiếu hàng cục bộ).

---

## IV. GHI CHÚ KỸ THUẬT
*   **Kiến trúc**: Sử dụng Livewire cho các thao tác mượt mà (SPA-like) mà không cần reload trang.
*   **Giao diện**: Đã tối ưu hóa bộ lọc (Filter) gọn gàng, chuẩn 4 cột.
*   **Database**: Đã chuẩn hóa schema cho các bảng cốt lõi (`phieu_chot_ca`, `ton_kho`).
