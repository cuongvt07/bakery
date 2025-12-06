<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductionBatchDetail extends Model
{
    protected $table = 'chi_tiet_me_san_xuat';

    protected $fillable = [
        'me_san_xuat_id',
        'cong_thuc_id',
        'san_pham_id',
        'so_luong_du_kien',
        'so_luong_that_bai',
        'so_luong_thuc_te',
        'ti_le_hong',
        'ghi_chu',
        'han_su_dung',
    ];

    protected $casts = [
        'so_luong_du_kien' => 'integer',
        'so_luong_that_bai' => 'integer',
        'so_luong_thuc_te' => 'integer',
        'ti_le_hong' => 'decimal:2',
        'han_su_dung' => 'date',
    ];

    // Relationships
    public function batch(): BelongsTo
    {
        return $this->belongsTo(ProductionBatch::class, 'me_san_xuat_id');
    }

    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class, 'cong_thuc_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'san_pham_id');
    }

    // Methods
    public function confirmQC(int $failedQty): void
    {
        $this->so_luong_that_bai = $failedQty;
        $this->so_luong_thuc_te = $this->so_luong_du_kien - $failedQty;
        $this->ti_le_hong = $this->so_luong_du_kien > 0 
            ? ($failedQty / $this->so_luong_du_kien) * 100 
            : 0;
        $this->save();
    }

    public function deductIngredients(): void
    {
        if (!$this->recipe || !$this->recipe->details) {
            return;
        }

        foreach ($this->recipe->details as $detail) {
            $ingredient = $detail->ingredient;
            
            if ($ingredient) {
                // Deduct ingredients based on quantity produced
                $ingredient->ton_kho_hien_tai -= $detail->so_luong;
                $ingredient->save();
            }
        }
    }

    public function getAvailableQuantityAttribute(): int
    {
        // Get distributed quantity for this specific product from this batch
        $distributed = PhanBoHangDiemBan::where('me_san_xuat_id', $this->me_san_xuat_id)
            ->where('san_pham_id', $this->san_pham_id)
            ->sum('so_luong');
        
        return $this->so_luong_thuc_te - $distributed;
    }

    public function calculateAndSetHSD(): void
    {
        if (!$this->product || !$this->batch) {
            return;
        }

        $ngaySanXuat = $this->batch->ngay_san_xuat;
        $soNgayHSD = $this->product->so_ngay_hsd ?? 3;
        
        $this->han_su_dung = $ngaySanXuat->copy()->addDays($soNgayHSD);
        $this->save();
    }

    public function isExpired(): bool
    {
        if (!$this->han_su_dung) {
            return false;
        }

        return now()->greaterThan($this->han_su_dung);
    }

    public function daysUntilExpiry(): int
    {
        if (!$this->han_su_dung) {
            return 999; // Arbitrarily large number
        }

        return (int) now()->diffInDays($this->han_su_dung, false);
    }

    public function isNearExpiry(): bool
    {
        return $this->daysUntilExpiry() <= 1 && !$this->isExpired();
    }
}
