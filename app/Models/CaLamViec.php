<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CaLamViec extends Model
{
    protected $table = 'ca_lam_viec';

    protected $fillable = [
        'diem_ban_id',
        'nguoi_dung_id',
        'ngay_lam',
        'gio_bat_dau',
        'gio_ket_thuc',
        'trang_thai',
        'trang_thai_checkin',
        'thoi_gian_checkin',
        'tien_mat_dau_ca',
        'ghi_chu',
        'shift_template_id',
    ];

    protected $casts = [
        'ngay_lam' => 'date',
        'trang_thai_checkin' => 'boolean',
        'thoi_gian_checkin' => 'datetime',
        'tien_mat_dau_ca' => 'decimal:2',
    ];

    /**
     * Relationships
     */
    public function diemBan(): BelongsTo
    {
        return $this->belongsTo(Agency::class, 'diem_ban_id');
    }

    public function nguoiDung(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nguoi_dung_id');
    }

    public function chamCong(): HasMany
    {
        return $this->hasMany(ChamCong::class, 'ca_lam_viec_id');
    }

    public function phieuChotCa()
    {
        return $this->hasOne(PhieuChotCa::class, 'ca_lam_viec_id');
    }

    public function chiTietCaLam(): HasMany
    {
        return $this->hasMany(ChiTietCaLam::class, 'ca_lam_viec_id');
    }

    public function shiftTemplate(): BelongsTo
    {
        return $this->belongsTo(ShiftTemplate::class, 'shift_template_id');
    }

    /**
     * Scopes
     */
    public function scopeUpcoming($query)
    {
        return $query->where('ngay_lam', '>=', today())
                     ->where('trang_thai', 'chua_bat_dau');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('ngay_lam', today());
    }

    public function scopeByDate($query, $date)
    {
        return $query->whereDate('ngay_lam', $date);
    }

    public function scopeByDiemBan($query, $diemBanId)
    {
        return $query->where('diem_ban_id', $diemBanId);
    }

    public function scopeByNguoiDung($query, $nguoiDungId)
    {
        return $query->where('nguoi_dung_id', $nguoiDungId);
    }
}
