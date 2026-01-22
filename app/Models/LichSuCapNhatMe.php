<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LichSuCapNhatMe extends Model
{
    protected $table = 'lich_su_cap_nhat_me';

    protected $fillable = [
        'me_san_xuat_id',
        'san_pham_id',
        'diem_ban_id',
        'loai',
        'ca_lam_viec_id',
        'nguoi_cap_nhat_id',
        'so_luong_doi',
        'du_lieu_cu',
        'du_lieu_moi',
        'ghi_chu',
    ];

    protected $casts = [
        'so_luong_doi' => 'integer',
        'du_lieu_cu' => 'integer',
        'du_lieu_moi' => 'integer',
    ];

    // Constants for loai
    const LOAI_PHAN_BO = 'phan_bo';
    const LOAI_BAN = 'ban';
    const LOAI_HONG = 'hong';
    const LOAI_HOAN = 'hoan';
    const LOAI_DIEU_CHINH = 'dieu_chinh';

    // Relationships
    public function productionBatch(): BelongsTo
    {
        return $this->belongsTo(ProductionBatch::class, 'me_san_xuat_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'san_pham_id');
    }

    public function diemBan(): BelongsTo
    {
        return $this->belongsTo(Agency::class, 'diem_ban_id');
    }

    public function caLamViec(): BelongsTo
    {
        return $this->belongsTo(CaLamViec::class, 'ca_lam_viec_id');
    }

    public function nguoiCapNhat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nguoi_cap_nhat_id');
    }

    /**
     * Get total quantity change for a batch-product
     * Negative = reduced, Positive = added
     */
    public static function getTotalChange($batchId, $productId): int
    {
        return self::where('me_san_xuat_id', $batchId)
            ->where('san_pham_id', $productId)
            ->sum('so_luong_doi');
    }

    /**
     * Log a quantity change
     */
    public static function logChange(array $data): self
    {
        return self::create($data);
    }
}
