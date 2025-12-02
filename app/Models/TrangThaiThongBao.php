<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrangThaiThongBao extends Model
{
    protected $table = 'trang_thai_thong_bao';

    protected $fillable = [
        'thong_bao_id',
        'nguoi_dung_id',
        'da_doc',
        'ngay_doc',
    ];

    protected $casts = [
        'da_doc' => 'boolean',
        'ngay_doc' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function thongBao(): BelongsTo
    {
        return $this->belongsTo(ThongBao::class, 'thong_bao_id');
    }

    public function nguoiDung(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nguoi_dung_id');
    }
}
