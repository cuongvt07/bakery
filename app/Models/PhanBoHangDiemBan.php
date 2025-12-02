<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhanBoHangDiemBan extends Model
{
    protected $table = 'phan_bo_hang_diem_ban';
    
    protected $fillable = [
        'phieu_xuat_hang_tong_id',
        'diem_ban_id',
        'nguoi_nhan_id',
        'ngay_nhan',
        'trang_thai'
    ];

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
