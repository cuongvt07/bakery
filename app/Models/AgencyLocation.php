<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AgencyLocation extends Model
{
    protected $table = 'vi_tri_diem_ban';

    protected $fillable = [
        'diem_ban_id',
        'ma_vi_tri',
        'ten_vi_tri',
        'mo_ta',
        'dia_chi',
    ];

    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class, 'diem_ban_id');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(AgencyNote::class, 'vi_tri_id');
    }

    /**
     * Get display label: "GI1 - Giỏ 1"
     */
    public function getDisplayLabelAttribute(): string
    {
        return $this->ma_vi_tri . ' - ' . $this->ten_vi_tri;
    }

    /**
     * Get full info with description
     */
    public function getFullInfoAttribute(): string
    {
        $info = $this->display_label;
        if ($this->mo_ta) {
            $info .= ' (' . $this->mo_ta . ')';
        }
        return $info;
    }
}
