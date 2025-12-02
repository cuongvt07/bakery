<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgencyStaff extends Model
{
    use HasFactory;

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

    public function user()
    {
        return $this->belongsTo(User::class, 'nguoi_dung_id');
    }

    public function agency()
    {
        return $this->belongsTo(Agency::class, 'diem_ban_id');
    }
}
