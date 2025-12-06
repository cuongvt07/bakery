# POS SYSTEM - TESTING CHECKLIST

**Version**: 1.0  
**Date**: 05/12/2024

---

## ✅ PRE-DEPLOYMENT CHECKLIST

### Database
- [x] Migration đã chạy thành công
- [x] Bảng `pending_sales` đã tạo
- [x] Bảng `batch_ban_hang` đã tạo
- [ ] Có data seed (optional)

### Code
- [x] Models created và có relationships
- [x] Livewire components created
- [x] Views created
- [x] Middleware registered
- [x] Routes defined

### Configuration
- [x] Middleware alias registered trong `bootstrap/app.php`
- [x] Routes protected với middleware
- [x] Redirect logic updated

---

## 🧪 FUNCTIONAL TESTING

### TEST 1: Login & Redirect ✅
**Steps**:
1. Login với tài khoản nhân viên
2. Kiểm tra redirect

**Expected**:
- Nếu chưa check-in → `/admin/shift/check-in`
- Nếu đã check-in → `/admin/pos`

**Status**: [ ] Pass / [ ] Fail

---

### TEST 2: Check-in Flow ✅
**Steps**:
1. Vào `/admin/shift/check-in`
2. Nhập tiền mặt đầu ca: 100,000đ
3. Xác nhận số lượng hàng nhận
4. Click "Xác nhận Check-in"

**Expected**:
- ✅ Data saved to database
- ✅ `ca_lam_viec.trang_thai_checkin = true`
- ✅ Records created in `chi_tiet_ca_lam`
- ✅ Auto redirect to `/admin/pos`

**Verify Database**:
```sql
SELECT * FROM ca_lam_viec WHERE nguoi_dung_id = ? ORDER BY id DESC LIMIT 1;
SELECT * FROM chi_tiet_ca_lam WHERE ca_lam_viec_id = ?;
```

**Status**: [ ] Pass / [ ] Fail

---

### TEST 3: POS - Quick Sale ✅
**Steps**:
1. Vào `/admin/pos`
2. Click [+] để thêm sản phẩm
3. Kiểm tra total update
4. Click [THANH TOÁN]

**Expected**:
- ✅ Products load correctly
- ✅ +/- buttons work
- ✅ Total updates in real-time
- ✅ Stock validation works (can't exceed available)
- ✅ Checkout creates pending sale
- ✅ Cart resets after checkout
- ✅ Badge shows "1 đơn"

**Verify Database**:
```sql
SELECT * FROM pending_sales 
WHERE ca_lam_viec_id = ? AND trang_thai = 'pending';
```

**Status**: [ ] Pass / [ ] Fail

---

### TEST 4: Multiple Quick Sales ✅
**Steps**:
1. Tạo 3 đơn hàng liên tiếp
2. Kiểm tra badge

**Expected**:
- ✅ Badge shows "3 đơn"
- ✅ All 3 sales in database với status 'pending'

**Status**: [ ] Pass / [ ] Fail

---

### TEST 5: Pending Sales List ✅
**Steps**:
1. Click badge "3 đơn"
2. Navigate to `/admin/pos/pending`
3. Xem danh sách

**Expected**:
- ✅ All 3 pending sales displayed
- ✅ Show time, items, amounts correctly
- ✅ Checkboxes work

**Status**: [ ] Pass / [ ] Fail

---

### TEST 6: Batch Confirm ✅
**Steps**:
1. Select 2 đơn
2. Click [CHỐT ĐÃ CHỌN]
3. Confirm

**Expected**:
- ✅ Batch created in `batch_ban_hang`
- ✅ Pending sales updated to 'confirmed'
- ✅ `chi_tiet_ca_lam.so_luong_ban` increased
- ✅ Success message shown
- ✅ Pending list updated (1 đơn còn lại)

**Verify Database**:
```sql
-- Check batch
SELECT * FROM batch_ban_hang ORDER BY id DESC LIMIT 1;

-- Check pending sales
SELECT * FROM pending_sales WHERE id IN (?, ?);

-- Check inventory update
SELECT so_luong_ban FROM chi_tiet_ca_lam 
WHERE ca_lam_viec_id = ? AND san_pham_id = ?;
```

**Status**: [ ] Pass / [ ] Fail

---

### TEST 7: Delete Pending Sale ✅
**Steps**:
1. Trong pending list
2. Click delete trên 1 đơn
3. Confirm

**Expected**:
- ✅ Sale updated to 'cancelled'
- ✅ Not displayed in pending list
- ✅ Inventory NOT affected

**Status**: [ ] Pass / [ ] Fail

---

### TEST 8: Shift Closing ✅
**Steps**:
1. Vào `/admin/shift/closing`
2. Nhập tồn cuối cho từng sản phẩm
3. Nhập tiền mặt: 300,000đ
4. Nhập chuyển khoản: 0đ
5. Upload ảnh
6. Click [HOÀN TẤT CHỐT CA]

**Expected**:
- ✅ Show opening stock từ check-in
- ✅ Calculate sold quantity automatically
- ✅ Calculate theoretical revenue
- ✅ Calculate discrepancy
- ✅ Upload images successful
- ✅ Create `phieu_chot_ca`
- ✅ Update shift status to 'da_ket_thuc'
- ✅ Redirect to dashboard

**Verify Database**:
```sql
SELECT * FROM phieu_chot_ca WHERE ca_lam_viec_id = ?;
SELECT trang_thai FROM ca_lam_viec WHERE id = ?;
```

**Status**: [ ] Pass / [ ] Fail

---

## 📱 MOBILE UI TESTING

### TEST 9: Mobile Responsiveness ✅
**Device**: iPhone / Android phone

**Check**:
- [ ] POS screen responsive
- [ ] Buttons large enough (min 48x48px)
- [ ] Sticky header/footer work
- [ ] Text readable without zoom
- [ ] No horizontal scroll

**Status**: [ ] Pass / [ ] Fail

---

### TEST 10: Wake Lock ✅
**Steps**:
1. Vào POS screen trên mobile
2. Để màn hình idle 5 phút

**Expected**:
- ✅ Screen stays on (Wake Lock active)
- ⚠️ Only works on HTTPS or localhost

**Status**: [ ] Pass / [ ] Fail

---

### TEST 11: Touch Interactions ✅
**Steps**:
1. Test tất cả buttons với touch
2. Check responsive time

**Expected**:
- [ ] +/- buttons respond immediately
- [ ] No accidental double-taps
- [ ] Smooth scrolling

**Status**: [ ] Pass / [ ] Fail

---

## 🔐 SECURITY TESTING

### TEST 12: Middleware Protection ✅
**Steps**:
1. Logout
2. Try to access `/admin/pos` directly

**Expected**:
- ✅ Redirect to login

**Status**: [ ] Pass / [ ] Fail

---

### TEST 13: Check-in Required ✅
**Steps**:
1. Login nhưng CHƯA check-in
2. Try to access `/admin/pos`

**Expected**:
- ✅ Redirect to `/admin/shift/check-in`
- ✅ Show error message

**Status**: [ ] Pass / [ ] Fail

---

### TEST 14: Data Isolation ✅
**Steps**:
1. Login as Employee A
2. Create pending sales
3. Logout, login as Employee B
4. Check pending list

**Expected**:
- ✅ Employee B only sees their own pending sales
- ✅ Cannot see Employee A's data

**Status**: [ ] Pass / [ ] Fail

---

## 🐛 ERROR HANDLING

### TEST 15: Validation Errors ✅
**Steps**:
- Checkout with empty cart
- Check-in without entering opening cash
- Shift closing without counting stock
- Upload file > 2MB

**Expected**:
- ✅ Show appropriate error messages
- ✅ No data saved
- ✅ Form remains accessible

**Status**: [ ] Pass / [ ] Fail

---

### TEST 16: Network Errors ✅
**Steps**:
1. Disable network
2. Try to checkout

**Expected**:
- Show error "Không thể kết nối"
- Data NOT lost (if using localStorage)

**Status**: [ ] Pass / [ ] Fail / [ ] N/A

---

## 🎯 PERFORMANCE TESTING

### TEST 17: Load Time ✅
**Measure**:
- POS page load time < 1s?
- Quick sale time < 5s?
- Batch confirm < 2s?

**Status**: [ ] Pass / [ ] Fail

---

### TEST 18: Concurrent Users ✅
**Steps**:
1. 3 employees login simultaneously
2. All create sales at same time

**Expected**:
- ✅ No conflicts
- ✅ All data saved correctly

**Status**: [ ] Pass / [ ] Fail

---

## 📊 DATA INTEGRITY

### TEST 19: Inventory Accuracy ✅
**Steps**:
1. Check-in: Nhận 10 Flan
2. Sell 7 Flan (via pending → batch confirm)
3. Check inventory

**Expected**:
```sql
SELECT 
    so_luong_nhan_ca,  -- 10
    so_luong_ban,      -- 7
    (so_luong_nhan_ca - so_luong_ban) as con_lai  -- 3
FROM chi_tiet_ca_lam 
WHERE ca_lam_viec_id = ? AND san_pham_id = ?;
```

**Status**: [ ] Pass / [ ] Fail

---

### TEST 20: Money Calculation ✅
**Data**:
- Product A: 15,000đ
- Product B: 18,000đ

**Scenario**:
- Sell 2× A + 3× B
- Expected total: 84,000đ

**Verify**:
```
2 × 15,000 = 30,000
3 × 18,000 = 54,000
Total = 84,000 ✓
```

**Status**: [ ] Pass / [ ] Fail

---

## 🎉 ACCEPTANCE CRITERIA

### All tests must pass:
- [ ] Functional tests (1-8): 8/8
- [ ] Mobile tests (9-11): 3/3
- [ ] Security tests (12-14): 3/3
- [ ] Error handling (15-16): 2/2
- [ ] Performance (17-18): 2/2
- [ ] Data integrity (19-20): 2/2

**Total**: [ ] 20/20 tests passed

---

## 📝 NOTES

**Blockers**:
- (List any issues found)

**Future Improvements**:
- Offline support (PWA)
- Barcode scanner
- Receipt printing
- Advanced analytics

---

**Tested by**: _________________  
**Date**: _________________  
**Sign-off**: _________________
