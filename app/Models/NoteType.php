<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NoteType extends Model
{
    protected $table = 'loai_ghi_chu_diem_ban';

    protected $fillable = [
        'diem_ban_id',
        'ma_loai',
        'ten_hien_thi',
        'icon',
        'mau_sac',
        'la_mac_dinh',
        'hien_thi',
        'thu_tu',
    ];

    protected $casts = [
        'la_mac_dinh' => 'boolean',
        'hien_thi' => 'boolean',
        'thu_tu' => 'integer',
    ];

    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class, 'diem_ban_id');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(AgencyNote::class, 'loai', 'ma_loai')
            ->where('diem_ban_id', $this->diem_ban_id);
    }

    /**
     * Get display label with icon: "🪑 Vật dụng"
     */
    public function getDisplayLabelAttribute(): string
    {
        return $this->icon . ' ' . $this->ten_hien_thi;
    }

    /**
     * Create default "vat_dung" type for an agency
     */
    public static function createDefaultForAgency($agencyId): self
    {
        return self::create([
            'diem_ban_id' => $agencyId,
            'ma_loai' => 'vat_dung',
            'ten_hien_thi' => 'Vật dụng',
            'icon' => '🪑',
            'mau_sac' => 'blue',
            'la_mac_dinh' => true,
            'thu_tu' => 1,
        ]);
    }

    /**
     * Scope: Get types for specific agency
     */
    public function scopeForAgency($query, $agencyId)
    {
        return $query->where('diem_ban_id', $agencyId)
                    ->where('hien_thi', true)
                    ->orderBy('thu_tu');
    }
}
