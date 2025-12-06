<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhanBoHangDiemBan extends Model
{
    protected $table = 'phan_bo_hang_diem_ban';
    
    protected $fillable = [
        'phieu_xuat_hang_tong_id',
        'me_san_xuat_id',  // NEW: For direct batch distribution
        'diem_ban_id',
        'san_pham_id',     // NEW: Product ID
        'buoi',            // NEW: Session (sang/chieu)
        'so_luong',        // NEW: Quantity
        'nguoi_nhan_id',
        'ngay_nhan',
        'trang_thai'
    ];

    // Relationships
    public function productionBatch()
    {
        return $this->belongsTo(ProductionBatch::class, 'me_san_xuat_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'san_pham_id');
    }

    public function chiTiet()
    {
        return $this->hasMany(ChiTietPhanBo::class, 'phan_bo_hang_diem_ban_id');
    }

    public function diemBan()
    {
        return $this->belongsTo(Agency::class, 'diem_ban_id');
    }

    public function phieuTong()
    {
        return $this->belongsTo(PhieuXuatHangTong::class, 'phieu_xuat_hang_tong_id');
    }
}
