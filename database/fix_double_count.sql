-- Reset so_luong_ban to 0 for current active shift
-- This fixes the double-count bug from old data

UPDATE chi_tiet_ca_lam 
SET so_luong_ban = 0 
WHERE ca_lam_viec_id IN (
    SELECT id FROM ca_lam_viec 
    WHERE trang_thai = 'dang_lam'
);

-- Verify results
SELECT 
    sp.ten_san_pham,
    ctl.so_luong_nhan_ca,
    ctl.so_luong_ban,
    (ctl.so_luong_nhan_ca - ctl.so_luong_ban) as so_luong_con_lai
FROM chi_tiet_ca_lam ctl
JOIN san_pham sp ON ctl.san_pham_id = sp.id
WHERE ctl.ca_lam_viec_id IN (
    SELECT id FROM ca_lam_viec WHERE trang_thai = 'dang_lam'
);
