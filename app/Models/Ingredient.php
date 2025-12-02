<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    use HasFactory;

    protected $table = 'nguyen_lieu';

    protected $fillable = [
        'ma_nguyen_lieu',
        'ten_nguyen_lieu',
        'don_vi_tinh',
        'ton_kho_hien_tai',
        'ton_kho_toi_thieu',
    ];

    protected $casts = [
        'ton_kho_hien_tai' => 'decimal:2',
        'ton_kho_toi_thieu' => 'decimal:2',
    ];
}
