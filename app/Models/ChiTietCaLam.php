<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChiTietCaLam extends Model
{
    use HasFactory;

    protected $table = 'chi_tiet_ca_lam';

    protected $fillable = [
        'ca_lam_viec_id',
        'san_pham_id',
        'so_luong_nhan_ca',
        'so_luong_giao_ca',
        'so_luong_ban',
    ];

    protected $appends = ['so_luong_con_lai'];

    public function caLamViec()
    {
        return $this->belongsTo(CaLamViec::class);
    }

    public function sanPham()
    {
        return $this->belongsTo(Product::class, 'san_pham_id');
    }

    /**
     * Accessor: Calculate remaining quantity
     * Remaining = Received - Sold
     */
    public function getSoLuongConLaiAttribute()
    {
        return ($this->so_luong_nhan_ca ?? 0) - ($this->so_luong_ban ?? 0);
    }
}
