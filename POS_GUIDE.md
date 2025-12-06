# POS System - Quick Reference Guide

## 🚀 Cách sử dụng

### Cho Nhân Viên

#### 1. Đăng nhập
- Mở trình duyệt trên điện thoại
- Truy cập: `http://[server-ip]:8000`
- Đăng nhập với tài khoản nhân viên

#### 2. Check-in đầu ca
- Nhập **Tiền mặt đầu ca** (ví dụ: 100,000đ)
- Xác nhận **Số lượng hàng nhận** cho từng sản phẩm
- Nhấn **"Xác nhận Check-in"**
- → Tự động chuyển đến màn hình POS

#### 3. Bán hàng nhanh
- Nhấn nút **[+]** để thêm sản phẩm
- Nhấn nút **[-]** để bớt sản phẩm
- Xem tổng tiền ở dưới cùng
- Nhấn **"THANH TOÁN"** khi khách trả tiền
- → Đơn được lưu tạm, giỏ hàng reset tự động

#### 4. Chốt đơn hàng loạt
- Nhấn vào badge **"X đơn"** góc trên bên phải
- Tick chọn các đơn cần chốt (hoặc "Chọn tất cả")
- Nhấn **"CHỐT ĐƠN"**
- Xác nhận
- → Đơn được chốt vào hệ thống, tồn kho tự động cập nhật

---

## 🔑 Các URLs quan trọng

```
Check-in:     /admin/shift/check-in
POS:          /admin/pos
Pending List: /admin/pos/pending
Chốt ca:      /admin/shift/closing
```

---

## ⚠️ Lưu ý quan trọng

### Bắt buộc
- ✅ **PHẢI Check-in** trước khi bán hàng
- ✅ **PHẢI Chốt đơn** thì mới update tồn kho
- ✅ Giữ điện thoại **luôn bật** (Wake Lock tự động)

### Best Practices
- 📱 Để màn hình ở chế độ Portrait (dọc)
- 🔋 Cắm sạc nếu bán cả ngày
- ⏰ Chốt đơn **mỗi 1-2 giờ** để tránh mất dữ liệu
- 💰 Đếm tiền sau mỗi lần chốt đơn

---

## 🐛 Xử lý lỗi

### Lỗi: "Vui lòng check-in trước"
**Nguyên nhân**: Chưa check-in hoặc check-in chưa thành công  
**Giải pháp**: Quay về trang check-in và làm lại

### Lỗi: "Không đủ hàng"
**Nguyên nhân**: Số lượng trong kho không đủ  
**Giải pháp**: Giảm số lượng hoặc liên hệ admin

### Đơn pending bị mất
**Nguyên nhân**: Chưa được lưu vào database  
**Giải pháp**: Thường không xảy ra, nhưng nên chốt đơn thường xuyên

---

## 📊 Thống kê nhanh

- **Thời gian bán 1 đơn**: < 5 giây
- **Thời gian chốt batch**: < 2 giây
- **Số đơn tối đa/batch**: Không giới hạn

---

## 🎨 Màu sắc

- 🟢 **Xanh lá**: Còn nhiều hàng (>5 cái)
- 🟠 **Cam**: Sắp hết (≤5 cái)
- 🔴 **Đỏ**: Hết hàng (0 cái)

---

## 💡 Tips & Tricks

1. **Bán nhanh hơn**: Nhấn giữ nút [+] để thêm nhiều (nếu được hỗ trợ)
2. **Xem lại đơn**: Nhấn badge để xem tất cả đơn chưa chốt
3. **Xóa nhầm**: Có thể xóa từng đơn riêng lẻ trong Pending List
4. **Check số liệu**: Số trên badge = Số đơn chưa chốt

---

**Hỗ trợ**: Liên hệ Admin nếu gặp vấn đề!
