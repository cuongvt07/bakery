<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agency extends Model
{
    use HasFactory;

    protected $table = 'diem_ban';

    protected $fillable = [
        'ma_diem_ban',
        'ten_diem_ban',
        'dia_chi',
        'so_dien_thoai',
        'loai_dai_ly',
        'thong_tin_vat_dung',
        'vi_do',
        'kinh_do',
        'trang_thai',
        'ghi_chu',
    ];

    protected $casts = [
        'thong_tin_vat_dung' => 'array',
        'vi_do' => 'decimal:8',
        'kinh_do' => 'decimal:8',
    ];

    // Relationships
    public function notes()
    {
        return $this->hasMany(AgencyNote::class, 'diem_ban_id');
    }

    /**
     * Relationships
     */
    public function nhanVien()
    {
        return $this->belongsToMany(User::class, 'nhan_vien_diem_ban', 'diem_ban_id', 'nguoi_dung_id')
                    ->withPivot('ngay_bat_dau', 'ngay_ket_thuc', 'trang_thai')
                    ->withTimestamps();
    }

    public function caLamViec()
    {
        return $this->hasMany(CaLamViec::class, 'diem_ban_id');
    }

    public function shiftSchedules()
    {
        return $this->hasMany(ShiftSchedule::class, 'diem_ban_id');
    }

    public function shiftTemplates()
    {
        return $this->hasMany(ShiftTemplate::class, 'diem_ban_id');
    }

    public function chamCong()
    {
        return $this->hasMany(ChamCong::class, 'diem_ban_id');
    }
}
