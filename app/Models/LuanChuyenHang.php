<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LuanChuyenHang extends Model
{
    protected $table = 'luan_chuyen_hang';

    protected $fillable = [
        'ma_phieu',
        'diem_ban_nguon_id',
        'diem_ban_dich_id',
        'nguoi_chuyen_id',
        'nguoi_nhan_id',
        'ngay_chuyen',
        'ngay_nhan',
        'ly_do',
        'trang_thai', // dang_chuyen, da_nhan, huy
    ];

    protected $casts = [
        'ngay_chuyen' => 'datetime',
        'ngay_nhan' => 'datetime',
    ];

    // Relationships
    public function diemBanNguon(): BelongsTo
    {
        return $this->belongsTo(Agency::class, 'diem_ban_nguon_id');
    }

    public function diemBanDich(): BelongsTo
    {
        return $this->belongsTo(Agency::class, 'diem_ban_dich_id');
    }

    public function nguoiChuyen(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nguoi_chuyen_id');
    }

    public function nguoiNhan(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nguoi_nhan_id');
    }

    public function chiTiet(): HasMany
    {
        return $this->hasMany(ChiTietLuanChuyen::class, 'luan_chuyen_hang_id');
    }
}
