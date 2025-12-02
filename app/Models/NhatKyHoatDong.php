<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NhatKyHoatDong extends Model
{
    protected $table = 'nhat_ky_hoat_dong';

    public $timestamps = false;

    protected $fillable = [
        'nguoi_dung_id',
        'hanh_dong',
        'mo_ta',
        'du_lieu_cu',
        'du_lieu_moi',
        'ip_address',
    ];

    protected $casts = [
        'du_lieu_cu' => 'array',
        'du_lieu_moi' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function nguoiDung(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nguoi_dung_id');
    }

    /**
     * Scopes
     */
    public function scopeByAction($query, $action)
    {
        return $query->where('hanh_dong', $action);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('nguoi_dung_id', $userId);
    }

    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Log activity
     */
    public static function logActivity(
        string $action,
        ?array $oldData = null,
        ?array $newData = null,
        ?string $description = null
    ): void {
        static::create([
            'nguoi_dung_id' => auth()->id(),
            'hanh_dong' => $action,
            'mo_ta' => $description,
            'du_lieu_cu' => $oldData,
            'du_lieu_moi' => $newData,
            'ip_address' => request()->ip(),
        ]);
    }
}
