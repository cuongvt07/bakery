<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhieuXuatHangTong extends Model
{
    protected $table = 'phieu_xuat_hang_tong';
    
    protected $fillable = [
        'ma_phieu',
        'nguoi_xuat_id',
        'ngay_xuat',
        'gio_xuat',
        'anh_hang_xuat',
        'tong_so_luong',
        'ghi_chu',
        'trang_thai',
        'ten_me_hang'
    ];

    public function chiTiet()
    {
        return $this->hasMany(ChiTietPhieuXuatTong::class, 'phieu_xuat_hang_tong_id');
    }

    public function phanBo()
    {
        return $this->hasMany(PhanBoHangDiemBan::class, 'phieu_xuat_hang_tong_id');
    }

    public function nguoiXuat()
    {
        return $this->belongsTo(User::class, 'nguoi_xuat_id');
    }
}
