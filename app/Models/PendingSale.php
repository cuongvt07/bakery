<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PendingSale extends Model
{
    protected $table = 'pending_sales';

    protected $fillable = [
        'diem_ban_id',
        'ca_lam_viec_id',
        'nguoi_ban_id',
        'thoi_gian',
        'chi_tiet',
        'tong_tien',
        'phuong_thuc_thanh_toan',
        'trang_thai',
    ];

    protected $casts = [
        'chi_tiet' => 'array',
        'tong_tien' => 'decimal:2',
        'thoi_gian' => 'datetime:H:i',
    ];

    /**
     * Relationships
     */
    public function caLamViec(): BelongsTo
    {
        return $this->belongsTo(CaLamViec::class, 'ca_lam_viec_id');
    }

    public function diemBan(): BelongsTo
    {
        return $this->belongsTo(Agency::class, 'diem_ban_id');
    }

    public function nguoiBan(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nguoi_ban_id');
    }

    /**
     * Confirm this pending sale
     */
    public function confirm(): bool
    {
        return $this->update(['trang_thai' => 'confirmed']);
    }

    /**
     * Cancel this pending sale
     */
    public function cancel(): bool
    {
        return $this->update(['trang_thai' => 'cancelled']);
    }

    /**
     * Get only pending sales
     */
    public function scopePending($query)
    {
        return $query->where('trang_thai', 'pending');
    }

    /**
     * Get sales for a specific shift
     */
    public function scopeForShift($query, $shiftId)
    {
        return $query->where('ca_lam_viec_id', $shiftId);
    }
}
