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
        'don_vi_phan_phoi',
        'so_luong_quy_doi',
        'trang_thai',
    ];

    protected $casts = [
        'gia_ban' => 'decimal:2',
        'so_luong_quy_doi' => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'danh_muc_id');
    }

    public function getQuantityDisplayAttribute()
    {
        return function ($quantity) {
            if (!$this->don_vi_phan_phoi || $this->so_luong_quy_doi <= 1) {
                return number_format($quantity) . ' ' . $this->don_vi_tinh;
            }
            
            $units = floor($quantity / $this->so_luong_quy_doi);
            $remainder = $quantity % $this->so_luong_quy_doi;
            
            $text = "";
            if ($units > 0) {
                $text .= number_format($units) . ' ' . $this->don_vi_phan_phoi;
            }
            
            if ($remainder > 0) {
                $text .= ($text ? ' + ' : '') . number_format($remainder) . ' ' . $this->don_vi_tinh;
            }
            
            if (!$text) return '0 ' . $this->don_vi_tinh;
            
            return $text . " (" . number_format($quantity) . " " . $this->don_vi_tinh . ")";
        };
    }
}
