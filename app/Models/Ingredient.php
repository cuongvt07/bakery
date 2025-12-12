<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUniqueCode;

class Ingredient extends Model
{
    use HasFactory, HasUniqueCode;

    protected $table = 'nguyen_lieu';

    protected $fillable = [
        'ma_nguyen_lieu',
        'ten_nguyen_lieu',
        'don_vi_tinh',
        'ton_kho_hien_tai',
        'ton_kho_toi_thieu',
        'gia_nhap',
    ];

    protected $casts = [
        'ton_kho_hien_tai' => 'decimal:2',
        'ton_kho_toi_thieu' => 'decimal:2',
        'gia_nhap' => 'decimal:2',
    ];

    /**
     * Code generation config
     */
    public function getCodePrefix(): string
    {
        return 'NL'; // Nguyên liệu
    }

    public function getCodeLength(): int
    {
        return 4; // NL0001, NL0002, ...
    }

    public function getCodeColumn(): string
    {
        return 'ma_nguyen_lieu';
    }
}
