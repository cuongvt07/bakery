<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NhanVienDiemBan extends Model
{
    protected $table = 'nhan_vien_diem_ban';
    
    protected $fillable = [
        'nguoi_dung_id',
        'diem_ban_id',
        'ngay_bat_dau',
        'ngay_ket_thuc',
    ];

    protected $casts = [
        'ngay_bat_dau' => 'date',
        'ngay_ket_thuc' => 'date',
    ];

    // Relationships
    public function nguoiDung()
    {
        return $this->belongsTo(User::class, 'nguoi_dung_id');
    }

    public function diemBan()
    {
        return $this->belongsTo(Agency::class, 'diem_ban_id');
    }
}
