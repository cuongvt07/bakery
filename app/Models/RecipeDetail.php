<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecipeDetail extends Model
{
    protected $table = 'chi_tiet_cong_thuc';

    protected $fillable = [
        'cong_thuc_id',
        'nguyen_lieu_id',
        'so_luong',
        'don_vi',
        'don_gia',
    ];

    protected $casts = [
        'so_luong' => 'decimal:2',
        'don_gia' => 'decimal:2',
    ];

    // Relationships
    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class, 'cong_thuc_id');
    }

    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class, 'nguyen_lieu_id');
    }

    // Accessor
    public function getTotalCostAttribute(): float
    {
        return $this->so_luong * $this->don_gia;
    }
}
