<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ThongBao extends Model
{
    protected $table = 'thong_bao';

    protected $fillable = [
        'tieu_de',
        'noi_dung',
        'loai_thong_bao',
        'gui_toi_tat_ca',
        'diem_ban_id',
        'nguoi_nhan_id',
        'nguoi_gui_id',
        'ngay_gui',
    ];

    protected $casts = [
        'gui_toi_tat_ca' => 'boolean',
        'ngay_gui' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function diemBan(): BelongsTo
    {
        return $this->belongsTo(Agency::class, 'diem_ban_id');
    }

    public function nguoiNhan(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nguoi_nhan_id');
    }

    public function nguoiGui(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nguoi_gui_id');
    }

    public function trangThaiThongBao(): HasMany
    {
        return $this->hasMany(TrangThaiThongBao::class, 'thong_bao_id');
    }

    /**
     * Scopes
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where(function($q) use ($userId) {
            $q->where('gui_toi_tat_ca', true)
              ->orWhere('nguoi_nhan_id', $userId);
        });
    }
}
