<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgencyNote extends Model
{
    protected $table = 'ghi_chu_dai_ly';

    protected $fillable = [
        'diem_ban_id',
        'loai',
        'vi_tri_id',
        'tieu_de',
        'noi_dung',
        'metadata',
        'hinh_anh',
        'ngay_nhac_nho',
        'da_xu_ly',
        'muc_do_quan_trong',
        'nguoi_tao_id',
    ];

    protected $casts = [
        'metadata' => 'array',
        'hinh_anh' => 'array',
        'ngay_nhac_nho' => 'date',
        'da_xu_ly' => 'boolean',
    ];

    // Relationships
    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class, 'diem_ban_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(AgencyLocation::class, 'vi_tri_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nguoi_tao_id');
    }

    // Helper methods
    public function isOverdue(): bool
    {
        if (!$this->ngay_nhac_nho || $this->da_xu_ly) {
            return false;
        }

        return now()->greaterThan($this->ngay_nhac_nho);
    }

    public function isDueSoon(): bool
    {
        if (!$this->ngay_nhac_nho || $this->da_xu_ly) {
            return false;
        }

        return now()->diffInDays($this->ngay_nhac_nho, false) <= 7 
               && !$this->isOverdue();
    }

    public function getPriorityColorAttribute(): string
    {
        if ($this->isOverdue()) {
            return 'red';
        }

        return match($this->muc_do_quan_trong) {
            'khan_cap' => 'red',
            'cao' => 'orange',
            'trung_binh' => 'yellow',
            'thap' => 'green',
            default => 'gray',
        };
    }

    public function getPriorityLabelAttribute(): string
    {
        return match($this->muc_do_quan_trong) {
            'khan_cap' => 'Khẩn cấp',
            'cao' => 'Cao',
            'trung_binh' => 'Trung bình',
            'thap' => 'Thấp',
            default => '',
        };
    }

    public function getTypeLabelAttribute(): string
    {
        // Try to get from NoteType table
        $noteType = NoteType::where('diem_ban_id', $this->diem_ban_id)
            ->where('ma_loai', $this->loai)
            ->first();
        
        if ($noteType) {
            return $noteType->display_label;
        }
        
        // Fallback to hardcoded (for backward compatibility)
        return match($this->loai) {
            'hop_dong' => '📄 Hợp đồng',
            'chi_phi' => '💰 Chi phí',
            'cong_an' => '👮 Công an',
            'vat_dung' => '🪑 Vật dụng',
            'bien_bao' => '🪧 Biển bảo',
            'khac' => '📝 Khác',
            default => '📝 ' . $this->loai,
        };
    }
    
    /**
     * Check if this note is for "vat_dung" type
     */
    public function isItemNote(): bool
    {
        return $this->loai === 'vat_dung';
    }
}
