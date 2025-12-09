<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUniqueCode;

class Supplier extends Model
{
    use HasFactory, HasUniqueCode;

    protected $table = 'nha_cung_cap';

    protected $fillable = [
        'ma_ncc',
        'ten_ncc',
        'nguoi_dai_dien',
        'so_dien_thoai',
        'so_dien_thoai_zalo',
        'dia_chi',
        'email',
        'san_pham_cung_cap',
        'noi_dung_thoa_thuan',
        'ghi_chu',
    ];

    /**
     * Code generation config
     */
    public function getCodePrefix(): string
    {
        return 'NCC'; // Nhà cung cấp
    }

    public function getCodeLength(): int
    {
        return 4; // NCC0001, NCC0002, ...
    }
}
