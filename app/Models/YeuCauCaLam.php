<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class YeuCauCaLam extends Model
{
    protected $table = 'yeu_cau_ca_lam';

    protected $fillable = [
        'nguoi_dung_id',
        'loai_yeu_cau',
        'ca_lam_viec_id',
        'ngay_mong_muon',
        'gio_bat_dau',
        'gio_ket_thuc',
        'ly_do',
        'trang_thai',
        'nguoi_duyet_id',
        'ngay_duyet',
        'ghi_chu_duyet',
    ];

    protected $casts = [
        'ngay_mong_muon' => 'date',
        'ngay_duyet' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function nguoiDung(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nguoi_dung_id');
    }

    public function caLamViec(): BelongsTo
    {
        return $this->belongsTo(CaLamViec::class, 'ca_lam_viec_id');
    }

    public function nguoiDuyet(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nguoi_duyet_id');
    }

    /**
     * Scopes
     */
    public function scopePending($query)
    {
        return $query->where('trang_thai', 'cho_duyet');
    }

    public function scopeApproved($query)
    {
        return $query->where('trang_thai', 'da_duyet');
    }

    public function scopeRejected($query)
    {
        return $query->where('trang_thai', 'tu_choi');
    }

    public function scopeByLoaiYeuCau($query, $loai)
    {
        return $query->where('loai_yeu_cau', $loai);
    }
    
    /**
     * Accessors
     */
    public function getReasonTextAttribute()
    {
        $lyDoData = json_decode($this->ly_do, true);
        return $lyDoData['reason'] ?? '(Không có lý do)';
    }
    
    public function getShiftScheduleIdAttribute()
    {
        $lyDoData = json_decode($this->ly_do, true);
        return $lyDoData['shift_schedule_id'] ?? null;
    }
    
    public function getNewShiftInfoAttribute()
    {
        $lyDoData = json_decode($this->ly_do, true);
        if (!isset($lyDoData['new_shift_name'])) {
            return null;
        }
        
        return [
            'agency_id' => $lyDoData['new_agency_id'] ?? null,
            'date' => $lyDoData['new_shift_date'] ?? null,
            'shift_name' => $lyDoData['new_shift_name'] ?? null,
            'shift_time' => $lyDoData['new_shift_time'] ?? null,
        ];
    }
}

