<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PhanBoHangDiemBan extends Model
{
    use SoftDeletes;
    
    protected $table = 'phan_bo_hang_diem_ban';
    
    protected $fillable = [
        'phieu_xuat_hang_tong_id',
        'me_san_xuat_id',
        'diem_ban_id',
        'san_pham_id',
        'buoi',
        'so_luong',
        'nguoi_nhan_id',
        'nguoi_tao_id',
        'nguoi_duyet_id',
        'ngay_nhan',
        'ngay_duyet',
        'trang_thai',
        'loai_phan_bo',
        'ghi_chu',
    ];

    protected $casts = [
        'ngay_nhan' => 'datetime',
        'ngay_duyet' => 'datetime',
    ];

    // Relationships
    public function productionBatch()
    {
        return $this->belongsTo(ProductionBatch::class, 'me_san_xuat_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'san_pham_id');
    }

    public function chiTiet()
    {
        return $this->hasMany(ChiTietPhanBo::class, 'phan_bo_hang_diem_ban_id');
    }

    public function diemBan()
    {
        return $this->belongsTo(Agency::class, 'diem_ban_id');
    }

    public function phieuTong()
    {
        return $this->belongsTo(PhieuXuatHangTong::class, 'phieu_xuat_hang_tong_id');
    }
    
    public function nguoiTao()
    {
        return $this->belongsTo(User::class, 'nguoi_tao_id');
    }
    
    public function nguoiDuyet()
    {
        return $this->belongsTo(User::class, 'nguoi_duyet_id');
    }
    
    public function nguoiNhan()
    {
        return $this->belongsTo(User::class, 'nguoi_nhan_id');
    }

    // Query Scopes
    public function scopeByAgency($query, $agencyId)
    {
        return $query->where('diem_ban_id', $agencyId);
    }

    public function scopeByProduct($query, $productId)
    {
        return $query->where('san_pham_id', $productId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('trang_thai', $status);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('loai_phan_bo', $type);
    }

    public function scopeBySession($query, $session)
    {
        return $query->where('buoi', $session);
    }

    public function scopeDaduyet($query)
    {
        return $query->whereNotNull('nguoi_duyet_id');
    }

    public function scopeChuaduyet($query)
    {
        return $query->whereNull('nguoi_duyet_id');
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        return match($this->trang_thai) {
            'chua_nhan' => '<span class="px-2 py-1 text-xs rounded bg-yellow-100 text-yellow-800">Chưa nhận</span>',
            'da_nhan' => '<span class="px-2 py-1 text-xs rounded bg-green-100 text-green-800">Đã nhận</span>',
            default => '<span class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-800">N/A</span>',
        };
    }

    public function getTypeLabelAttribute()
    {
        return match($this->loai_phan_bo) {
            'tu_me_sx' => 'Từ mẻ SX',
            'tu_do' => 'Tự do',
            'tu_mau' => 'Từ mẫu',
            default => 'N/A',
        };
    }
}
