<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShiftSchedule extends Model
{
    protected $fillable = [
        'nguoi_dung_id',
        'diem_ban_id',
        'shift_template_id',
        'ngay_lam',
        'gio_bat_dau',
        'gio_ket_thuc',
        'trang_thai', // pending, approved, rejected, completed
        'ghi_chu',
    ];

    protected $casts = [
        'ngay_lam' => 'date',
        'gio_bat_dau' => 'datetime:H:i',
        'gio_ket_thuc' => 'datetime:H:i',
    ];

    /**
     * Employee who registered for this shift
     */
    public function nguoiDung(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nguoi_dung_id');
    }

    /**
     * Agency/Location for this shift
     */
    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class, 'diem_ban_id');
    }

    /**
     * Shift template used (if any)
     */
    public function shiftTemplate(): BelongsTo
    {
        return $this->belongsTo(ShiftTemplate::class, 'shift_template_id');
    }

    /**
     * Scope for pending schedules
     */
    public function scopePending($query)
    {
        return $query->where('trang_thai', 'pending');
    }

    /**
     * Scope for approved schedules
     */
    public function scopeApproved($query)
    {
        return $query->where('trang_thai', 'approved');
    }
}
