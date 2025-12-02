<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BangLuong extends Model
{
    protected $table = 'bang_luong';

    protected $fillable = [
        'thang',
        'nam',
        'nguoi_dung_id',
        'luong_co_ban',
        'so_ngay_cong',
        'tong_luong',
        'trang_thai',
        'ngay_chot',
        'ngay_thanh_toan',
        'ghi_chu',
    ];

    protected $casts = [
        'luong_co_ban' => 'decimal:2',
        'so_ngay_cong' => 'decimal:2',
        'tong_luong' => 'decimal:2',
        'ngay_chot' => 'datetime',
        'ngay_thanh_toan' => 'datetime',
    ];

    /**
     * Boot for auto-calculation
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $model->tong_luong = $model->calculateSalary();
        });
    }

    /**
     * Calculate total salary
     */
    public function calculateSalary(): float
    {
        return round($this->luong_co_ban * $this->so_ngay_cong, 2);
    }

    /**
     * Relationships
     */
    public function nguoiDung(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nguoi_dung_id');
    }

    /**
     * Scopes
     */
    public function scopeByMonth($query, $month, $year)
    {
        return $query->where('thang', $month)->where('nam', $year);
    }

    public function scopeByYear($query, $year)
    {
        return $query->where('nam', $year);
    }

    public function scopeChuaChot($query)
    {
        return $query->where('trang_thai', 'chua_chot');
    }
}
