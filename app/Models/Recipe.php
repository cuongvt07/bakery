<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Recipe extends Model
{
    protected $table = 'cong_thuc_san_xuat';

    protected $fillable = [
        'ma_cong_thuc',
        'ten_cong_thuc',
        'san_pham_id',
        'so_luong_san_xuat',
        'don_vi_san_xuat',
        'chi_phi_uoc_tinh',
        'mo_ta',
        'trang_thai',
    ];

    protected $casts = [
        'so_luong_san_xuat' => 'integer',
        'chi_phi_uoc_tinh' => 'decimal:2',
    ];

    // Relationships
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'san_pham_id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(RecipeDetail::class, 'cong_thuc_id');
    }

    public function batches(): HasMany
    {
        return $this->hasMany(ProductionBatch::class, 'cong_thuc_id');
    }

    // Methods
    public function calculateCost(): void
    {
        $total = 0;
        foreach ($this->details as $detail) {
            $total += $detail->so_luong * $detail->don_gia;
        }
        $this->chi_phi_uoc_tinh = $total;
        $this->save();
    }

    public function getCostPerUnitAttribute(): float
    {
        if ($this->so_luong_san_xuat > 0) {
            return $this->chi_phi_uoc_tinh / $this->so_luong_san_xuat;
        }
        return 0;
    }
}
