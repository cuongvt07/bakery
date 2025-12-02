<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $table = 'nha_cung_cap';

    protected $fillable = [
        'ma_ncc',
        'ten_ncc',
        'so_dien_thoai',
        'dia_chi',
        'email',
        'ghi_chu',
    ];
}
