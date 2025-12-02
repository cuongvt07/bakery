<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChiTietPhanBo extends Model
{
    protected $table = 'chi_tiet_phan_bo';
    public $timestamps = false;
    
    protected $fillable = [
        'phan_bo_hang_diem_ban_id',
        'san_pham_id',
        'so_luong'
    ];

    public function sanPham()
    {
        return $this->belongsTo(Product::class, 'san_pham_id');
    }
}
