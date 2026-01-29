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
        'tong_gio_lam_viec',
    ];

    protected $casts = [
        'ngay_lam' => 'date',
        'trang_thai_checkin' => 'boolean',
        'thoi_gian_checkin' => 'datetime',
        'tien_mat_dau_ca' => 'decimal:2',
    ];

    /**
     * Get shift end datetime for proper time comparison
     */
    public function getShiftEndDateTimeAttribute()
    {
        $timeStr = is_string($this->gio_ket_thuc)
            ? $this->gio_ket_thuc
            : $this->gio_ket_thuc->format('H:i:s');

        return \Carbon\Carbon::createFromFormat(
            'Y-m-d H:i:s',
            $this->ngay_lam->format('Y-m-d') . ' ' . $timeStr
        );
    }

    /**
     * Get shift start datetime for proper time comparison
     */
    public function getShiftStartDateTimeAttribute()
    {
        $timeStr = is_string($this->gio_bat_dau)
            ? $this->gio_bat_dau
            : $this->gio_bat_dau->format('H:i:s');

        return \Carbon\Carbon::createFromFormat(
            'Y-m-d H:i:s',
            $this->ngay_lam->format('Y-m-d') . ' ' . $timeStr
        );
    }

    /**
     * Get total hours worked in this shift
     * Returns stored value from tong_gio_lam_viec column
     * This is calculated and saved when sync button is clicked
     */
    public function getTotalHoursAttribute()
    {
        return $this->tong_gio_lam_viec ?? 0;
    }

    /**
     * Get expected checkout time based on actual check-in time
     * If checked in at 08:05, and shift is 4 hours, expected checkout = 12:05
     */
    public function getExpectedCheckoutTimeAttribute()
    {
        if (!$this->thoi_gian_checkin) {
            // If not checked in yet, use shift_end_date_time
            return $this->shift_end_date_time;
        }

        // Get shift template times as time strings
        $startTimeStr = is_string($this->gio_bat_dau)
            ? $this->gio_bat_dau
            : $this->gio_bat_dau->format('H:i:s');
        $endTimeStr = is_string($this->gio_ket_thuc)
            ? $this->gio_ket_thuc
            : $this->gio_ket_thuc->format('H:i:s');

        // Parse template times on the shift date
        $templateStart = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $this->ngay_lam->format('Y-m-d') . ' ' . $startTimeStr);
        $templateEnd = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $this->ngay_lam->format('Y-m-d') . ' ' . $endTimeStr);

        // Calculate shift duration in minutes
        $shiftDurationMinutes = $templateStart->diffInMinutes($templateEnd, false);

        // Expected checkout = actual check-in + shift duration
        return $this->thoi_gian_checkin->copy()->addMinutes($shiftDurationMinutes);
    }

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

    // Alias for compatibility with views
    public function user()
    {
        return $this->nguoiDung();
    }

    // Alias for compatibility with views
    public function agency()
    {
        return $this->diemBan();
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
     * Calculate and save total hours worked
     * Called when sync button is clicked
     * Only calculates for CLOSED shifts (with phieuChotCa)
     * Unclosed shifts will have total hours = 0
     */
    /**
     * Calculate and save total hours worked
     * Called when sync button is clicked, or when shift is edited
     * Only calculates for CLOSED shifts (with phieuChotCa)
     * Resembles calculateWorkHours from AttendanceManager
     */
    public function calculateAndSaveTotalHours()
    {
        // Force refresh the relation to ensure we have the latest saved data
        $this->load('phieuChotCa');

        // If not checked in yet, set to 0
        if (!$this->thoi_gian_checkin) {
            $this->tong_gio_lam_viec = 0;
            return $this;
        }

        // IMPORTANT: Only calculate for CLOSED shifts (with phieuChotCa)
        if (!$this->phieuChotCa || !$this->phieuChotCa->gio_chot) {
            $this->tong_gio_lam_viec = 0;
            return $this;
        }

        // Get actual checkout time from phieuChotCa
        $checkoutTime = $this->phieuChotCa->gio_chot;
        $ngayChot = $this->phieuChotCa->ngay_chot ?? $this->ngay_lam;
        $gioChotStr = $checkoutTime instanceof \Carbon\Carbon ? $checkoutTime->format('H:i:s') : $checkoutTime;

        try {
            // Use Carbon::parse which is more robust than createFromFormat
            $dateStr = $ngayChot instanceof \Carbon\Carbon ? $ngayChot->format('Y-m-d') : $ngayChot;
            // Extract strictly time H:i:s
            $timeStr = $gioChotStr;
            if (strlen($timeStr) > 8) {
                // If it contains date or other junk, try to parse
                $timeStr = \Carbon\Carbon::parse($gioChotStr)->format('H:i:s');
            }

            $checkoutDateTime = \Carbon\Carbon::parse($dateStr . ' ' . $timeStr);
        } catch (\Exception $e) {
            $this->tong_gio_lam_viec = 0;
            return $this;
        }

        // Calculate absolute minutes difference (always positive)
        $minutes = abs($this->thoi_gian_checkin->diffInMinutes($checkoutDateTime));
        $diffHours = $minutes / 60;

        // Apply Max Hours Cap logic if not OT
        $isOt = (bool) ($this->phieuChotCa->ot ?? false);

        if (!$isOt) {
            // Calculate max hours from schedule
            $maxHours = 8;
            if ($this->gio_bat_dau && $this->gio_ket_thuc) {
                // Determine duration from start/end
                $start = is_string($this->gio_bat_dau) ? \Carbon\Carbon::parse($this->gio_bat_dau) : $this->gio_bat_dau;
                $end = is_string($this->gio_ket_thuc) ? \Carbon\Carbon::parse($this->gio_ket_thuc) : $this->gio_ket_thuc;

                // If they are just times (H:i:s), diffInHours handles it assuming same day
                // But generally shifts are within 24h
                $maxHours = $start->diffInHours($end);
                if ($maxHours == 0)
                    $maxHours = 8;
            }

            $totalHours = min($diffHours, $maxHours);
        } else {
            $totalHours = $diffHours;
        }

        $this->tong_gio_lam_viec = round($totalHours, 2);
        return $this;
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
