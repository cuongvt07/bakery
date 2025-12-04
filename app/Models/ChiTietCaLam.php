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

    public function caLamViec()
    {
        return $this->belongsTo(CaLamViec::class);
    }

    public function sanPham()
    {
        return $this->belongsTo(Product::class, 'san_pham_id');
    }
}
