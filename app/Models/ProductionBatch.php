<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductionBatch extends Model
{
    protected $table = 'me_san_xuat';

    protected $fillable = [
        'ma_me',
        'nguoi_tao_id',
        'ngay_san_xuat',
        'buoi',
        'han_su_dung',
        'anh_qc',
        'ghi_chu_qc',
        'nguoi_qc_id',
        'trang_thai',
    ];

    protected $casts = [
        'ngay_san_xuat' => 'date',
        'han_su_dung' => 'date',
        'anh_qc' => 'array',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($batch) {
            if (empty($batch->ma_me)) {
                $batch->ma_me = static::generateBatchCode($batch->buoi, $batch->ngay_san_xuat);
            }
        });
    }

    /**
     * Generate batch code with format: SANG261225-01, CHIEU261225-002
     */
    public static function generateBatchCode(string $buoi, $ngaySanXuat): string
    {
        // Convert buoi to prefix
        $prefix = match(strtolower($buoi)) {
            'sang', 'sáng' => 'SANG',
            'chieu', 'chiều' => 'CHIEU',
            'toi', 'tối' => 'TOI',
            default => strtoupper($buoi),
        };
        
        // Format date as DDMMYY
        $date = \Carbon\Carbon::parse($ngaySanXuat);
        $dateStr = $date->format('dmy'); // 261225
        
        // Get next sequence number for this buoi and date
        $existingBatches = static::where('buoi', $buoi)
            ->whereDate('ngay_san_xuat', $date->format('Y-m-d'))
            ->where('ma_me', 'like', $prefix . $dateStr . '-%')
            ->count();
        
        $sequence = str_pad($existingBatches + 1, 2, '0', STR_PAD_LEFT);
        
        return "{$prefix}{$dateStr}-{$sequence}";
    }

    // Relationships
    public function details(): HasMany
    {
        return $this->hasMany(ProductionBatchDetail::class, 'me_san_xuat_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nguoi_tao_id');
    }

    public function qcPersonnel(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nguoi_qc_id');
    }

    public function distributions(): HasMany
    {
        return $this->hasMany(PhanBoHangDiemBan::class, 'me_san_xuat_id');
    }

    // Methods
    public function confirmQC(array $qcData, array $images = [], string $notes = ''): void
    {
        // QC data format: [detail_id => failed_quantity]
        foreach ($qcData as $detailId => $failedQty) {
            $detail = $this->details()->find($detailId);
            if ($detail) {
                $detail->confirmQC($failedQty);
            }
        }

        // Update batch-level QC info
        $this->anh_qc = $images;
        $this->ghi_chu_qc = $notes;
        $this->trang_thai = 'hoan_thanh';
        $this->save();

        // Deduct ingredients for all products
        $this->deductIngredientsFromInventory();
    }

    public function deductIngredientsFromInventory(): void
    {
        foreach ($this->details as $detail) {
            $detail->deductIngredients();
        }
    }

    public function getRequiredIngredientsAttribute(): array
    {
        $allIngredients = [];
        
        foreach ($this->details as $detail) {
            if (!$detail->recipe || !$detail->recipe->details) {
                continue;
            }

            foreach ($detail->recipe->details as $recipeDetail) {
                $ingredientId = $recipeDetail->nguyen_lieu_id;
                
                if (!isset($allIngredients[$ingredientId])) {
                    $allIngredients[$ingredientId] = [
                        'ten_nguyen_lieu' => $recipeDetail->ingredient->ten_nguyen_lieu,
                        'so_luong' => 0,
                        'don_vi' => $recipeDetail->don_vi,
                        'ton_kho' => $recipeDetail->ingredient->ton_kho_hien_tai,
                    ];
                }
                
                // Accumulate quantity needed
                $allIngredients[$ingredientId]['so_luong'] += $recipeDetail->so_luong;
            }
        }

        // Add du_lieu flag
        foreach ($allIngredients as &$ing) {
            $ing['du_lieu'] = $ing['ton_kho'] >= $ing['so_luong'];
        }

        return array_values($allIngredients);
    }

    public function getTotalExpectedQuantityAttribute(): int
    {
        return $this->details->sum('so_luong_du_kien');
    }

    public function getTotalActualQuantityAttribute(): int
    {
        return $this->details->sum('so_luong_thuc_te');
    }

    public function getTotalFailedQuantityAttribute(): int
    {
        return $this->details->sum('so_luong_that_bai');
    }

    public function getAverageDefectRateAttribute(): float
    {
        $totalExpected = $this->total_expected_quantity;
        $totalFailed = $this->total_failed_quantity;
        
        return $totalExpected > 0 ? ($totalFailed / $totalExpected) * 100 : 0;
    }
}
