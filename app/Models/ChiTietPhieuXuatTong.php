<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChiTietPhieuXuatTong extends Model
{
    protected $table = 'chi_tiet_phieu_xuat_tong';
    public $timestamps = false;
    
    protected $fillable = [
        'phieu_xuat_hang_tong_id',
        'san_pham_id',
        'so_luong'
    ];

    public function sanPham()
    {
        return $this->belongsTo(Product::class, 'san_pham_id');
    }
}
