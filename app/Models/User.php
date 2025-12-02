<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'nguoi_dung';

    protected $fillable = [
        'ho_ten',
        'email',
        'so_dien_thoai',
        'mat_khau',
        'vai_tro',
        'trang_thai',
        'anh_dai_dien',
        'dia_chi',
        'ngay_vao_lam',
        'luong_co_ban',
        'loai_luong',
    ];

    protected $hidden = [
        'mat_khau',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'mat_khau' => 'hashed',
            'ngay_vao_lam' => 'date',
            'luong_co_ban' => 'decimal:2',
        ];
    }

    public function getAuthPassword()
    {
        return $this->mat_khau;
    }
}
