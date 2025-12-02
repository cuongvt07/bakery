<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class ChamCong extends Model
{
    protected $table = 'cham_cong';

    protected $fillable = [
        'nguoi_dung_id',
        'diem_ban_id',
        'ca_lam_viec_id',
        'ngay_cham',
        'gio_vao',
        'vi_do_vao',
        'kinh_do_vao',
        'anh_check_in',
        'gio_ra',
        'vi_do_ra',
        'kinh_do_ra',
        'anh_check_out',
        'tong_gio_lam',
    ];

    protected $casts = [
        'ngay_cham' => 'date',
        'vi_do_vao' => 'decimal:8',
        'kinh_do_vao' => 'decimal:8',
        'vi_do_ra' => 'decimal:8',
        'kinh_do_ra' => 'decimal:8',
        'tong_gio_lam' => 'decimal:2',
    ];

    /**
     * Boot method to auto-calculate working hours
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if ($model->gio_vao && $model->gio_ra) {
                $model->tong_gio_lam = static::calculateWorkingHours(
                    $model->gio_vao,
                    $model->gio_ra
                );
            }
        });
    }

    /**
     * Calculate working hours between check-in and check-out
     */
    public static function calculateWorkingHours($gioVao, $gioRa): float
    {
        $checkIn = Carbon::parse($gioVao);
        $checkOut = Carbon::parse($gioRa);
        
        return round($checkIn->diffInMinutes($checkOut) / 60, 2);
    }

    /**
     * Relationships
     */
    public function nguoiDung(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nguoi_dung_id');
    }

    public function diemBan(): BelongsTo
    {
        return $this->belongsTo(Agency::class, 'diem_ban_id');
    }

    public function caLamViec(): BelongsTo
    {
        return $this->belongsTo(CaLamViec::class, 'ca_lam_viec_id');
    }

    /**
     * Scopes
     */
    public function scopeByMonth($query, $month, $year)
    {
        return $query->whereMonth('ngay_cham', $month)
                     ->whereYear('ngay_cham', $year);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('nguoi_dung_id', $userId);
    }

    public function scopeByDiemBan($query, $diemBanId)
    {
        return $query->where('diem_ban_id', $diemBanId);
    }

    /**
     * Accessors
     */
    public function getTongGioLamAttribute($value)
    {
        return $value ?? 0;
    }
}
