<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    use HasFactory;

    protected $table = 'danh_muc_san_pham';

    protected $fillable = [
        'ten_danh_muc',
        'mo_ta',
        'thu_tu',
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'danh_muc_id');
    }
}
