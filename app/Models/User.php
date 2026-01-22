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
        'phong_ban_id',
        'trang_thai',
        'anh_dai_dien',
        'dia_chi',
        'so_cmnd',
        'ngay_cap_cmnd',
        'noi_cap_cmnd',
        'ngay_vao_lam',
        'ngay_thu_viec',
        'ngay_chinh_thuc',
        'ngay_ky_hop_dong',
        'ngay_het_han_hop_dong',
        'loai_hop_dong',
        'ghi_chu_hop_dong',
        'file_hop_dong',
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

    protected $casts = [
        'mat_khau' => 'hashed',
        'email_verified_at' => 'datetime',
        'ngay_cap_cmnd' => 'date',
        'ngay_vao_lam' => 'date',
        'ngay_thu_viec' => 'date',
        'ngay_chinh_thuc' => 'date',
        'ngay_ky_hop_dong' => 'date',
        'ngay_het_han_hop_dong' => 'date',
        'luong_co_ban' => 'decimal:2',
        'luong_thu_viec' => 'decimal:2',
        'luong_chinh_thuc' => 'decimal:2',
    ];

    // Mutators to handle empty date strings
    public function setNgayKyHopDongAttribute($value)
    {
        $this->attributes['ngay_ky_hop_dong'] = ($value === '' || $value === null) ? null : $value;
    }

    public function setNgayHetHanHopDongAttribute($value)
    {
        $this->attributes['ngay_het_han_hop_dong'] = ($value === '' || $value === null) ? null : $value;
    }

    public function setNgayCapCmndAttribute($value)
    {
        $this->attributes['ngay_cap_cmnd'] = ($value === '' || $value === null) ? null : $value;
    }

    public function setNgayVaoLamAttribute($value)
    {
        $this->attributes['ngay_vao_lam'] = ($value === '' || $value === null) ? null : $value;
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
     * Get salary for calculation: chinh_thuc first, then thu_viec, then co_ban
     * Returns monthly salary (hourly is calculated by dividing by standard work hours)
     * Priority: chinh_thuc (if > 0) > thu_viec (if > 0) > co_ban
     */
    public function getSalaryForCalculation()
    {
        // Priority: chinh_thuc > thu_viec > co_ban
        // Check value > 0, not just null (to handle 0 values correctly)
        if ($this->luong_chinh_thuc > 0) {
            return $this->luong_chinh_thuc;
        }
        if ($this->luong_thu_viec > 0) {
            return $this->luong_thu_viec;
        }
        return $this->luong_co_ban ?? 0;
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
    public function diemBan()
    {
        return $this->belongsToMany(Agency::class, 'nhan_vien_diem_ban', 'nguoi_dung_id', 'diem_ban_id')
                    ->withPivot('ngay_bat_dau', 'ngay_ket_thuc')
                    ->withTimestamps();
    }
    
    /**
     * Department relationship
     */
    public function department()
    {
        return $this->belongsTo(Department::class, 'phong_ban_id');
    }
    
    /**
     * Get check-in type based on department
     * Returns: 'sales', 'production', 'office'
     */
    public function getCheckinType(): string
    {
        if (!$this->department) {
            return 'office';
        }
        
        $deptCode = $this->department->ma_phong_ban;
        
        if (in_array($deptCode, ['PB0001'])) {
            return 'sales';
        }
        
        if (in_array($deptCode, ['PB0002'])) {
            return 'production';
        }
        
        return 'office';
    }
}

