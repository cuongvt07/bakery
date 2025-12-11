<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Traits\HasUniqueCode;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasUniqueCode;

    protected $table = 'nguoi_dung';

    protected $fillable = [
        'ma_nhan_vien',
        'ho_ten',
        'email',
        'facebook',
        'so_dien_thoai',
        'nguoi_lien_he_khan_cap',
        'sdt_lien_he_khan_cap',
        'mat_khau',
        'vai_tro',
        'trang_thai',
        'anh_dai_dien',
        'dia_chi',
        'so_cmnd',
        'ngay_cap_cmnd',
        'noi_cap_cmnd',
        'ngay_vao_lam',
        'ngay_ky_hop_dong',
        'ngay_het_han_hop_dong',
        'loai_hop_dong',
        'luong_co_ban',
        'luong_thu_viec',
        'luong_chinh_thuc',
        'loai_luong',
        'ngan_hang',
        'so_tai_khoan',
        'chu_tai_khoan',
    ];

    protected $hidden = [
        'mat_khau',
        'remember_token',
    ];

    protected $appends = ['luong_hien_tai'];

    protected function casts(): array
    {
        return [
            'mat_khau' => 'hashed',
            'ngay_vao_lam' => 'date',
            'ngay_ky_hop_dong' => 'date',
            'ngay_het_han_hop_dong' => 'date',
            'ngay_cap_cmnd' => 'date',
            'luong_co_ban' => 'decimal:2',
            'luong_thu_viec' => 'decimal:2',
            'luong_chinh_thuc' => 'decimal:2',
        ];
    }

    public function getAuthPassword()
    {
        return $this->mat_khau;
    }

    /**
     * Code generation config
     */
    public function getCodePrefix(): string
    {
        return 'NV'; // Nhân viên
    }

    public function getCodeLength(): int
    {
        return 4; // NV0001, NV0002, ...
    }

    /**
     * Accessor: Get current active salary based on contract type
     */
    public function getLuongHienTaiAttribute()
    {
        return match($this->loai_hop_dong) {
            'chinh_thuc' => $this->luong_chinh_thuc ?? $this->luong_co_ban,
            'thu_viec' => $this->luong_thu_viec ?? $this->luong_co_ban,
            default => $this->luong_co_ban,
        };
    }

    /**
     * Check if contract is expired
     */
    public function isContractExpired(): bool
    {
        if (!$this->ngay_het_han_hop_dong) {
            return false;
        }
        return now()->gt($this->ngay_het_han_hop_dong);
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->vai_tro === 'admin';
    }

    /**
     * Check if user is employee
     */
    public function isEmployee(): bool
    {
        return $this->vai_tro === 'nhan_vien';
    }

    /**
     * Get contract status badge
     */
    public function getContractStatusBadgeAttribute(): string
    {
        if ($this->isContractExpired()) {
            return '<span class="px-2 py-1 bg-red-100 text-red-800 rounded text-xs">Hết hạn</span>';
        }
        
        return match($this->loai_hop_dong) {
            'chinh_thức' => '<span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">Chính thức</span>',
            'thu_viec' => '<span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-xs">Thử việc</span>',
            'hop_tac' => '<span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">Hợp tác</span>',
            default => '<span class="px-2 py-1 bg-gray-100 text-gray-800 rounded text-xs">Chưa xác định</span>',
        };
    }
}
