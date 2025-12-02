<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'san_pham';

    protected $fillable = [
        'danh_muc_id',
        'ma_san_pham',
        'ten_san_pham',
        'mo_ta',
        'anh_san_pham',
        'gia_ban',
        'don_vi_tinh',
        'trang_thai',
    ];

    protected $casts = [
        'gia_ban' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'danh_muc_id');
    }
}
