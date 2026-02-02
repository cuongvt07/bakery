<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChiTietLuanChuyen extends Model
{
    protected $table = 'chi_tiet_luan_chuyen';

    public $timestamps = false;

    protected $fillable = [
        'luan_chuyen_hang_id',
        'san_pham_id',
        'me_san_xuat_id',
        'so_luong',
        'han_su_dung',
    ];

    protected $casts = [
        'han_su_dung' => 'date',
        'so_luong' => 'decimal:2',
    ];

    // Relationships
    public function luanChuyen(): BelongsTo
    {
        return $this->belongsTo(LuanChuyenHang::class, 'luan_chuyen_hang_id');
    }

    public function sanPham(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'san_pham_id');
    }

    public function meSanXuat(): BelongsTo
    {
        return $this->belongsTo(ProductionBatch::class, 'me_san_xuat_id');
    }
}
