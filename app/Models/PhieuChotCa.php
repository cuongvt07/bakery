<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PhieuChotCa extends Model
{
    protected $table = 'phieu_chot_ca';

    protected $fillable = [
        'ma_phieu',
        'diem_ban_id',
        'nguoi_chot_id',
        'ca_lam_viec_id',
        'ngay_chot',
        'gio_chot',
        'tien_mat',
        'tien_chuyen_khoan',
        'tong_tien_thuc_te',
        'tong_tien_ly_thuyet',
        'tien_lech',
        'ton_dau_ca',
        'ton_cuoi_ca',
        'hang_lech',
        'anh_chot_ket',
        'ghi_chu',
        'trang_thai',
        'nguoi_duyet_id',
        'ngay_duyet',
    ];

    protected $casts = [
        'ngay_chot' => 'date',
        'ngay_duyet' => 'datetime',
        'tien_mat' => 'decimal:2',
        'tien_chuyen_khoan' => 'decimal:2',
        'tong_tien_thuc_te' => 'decimal:2',
        'tong_tien_ly_thuyet' => 'decimal:2',
        'tien_lech' => 'decimal:2',
        'ton_dau_ca' => 'array',
        'ton_cuoi_ca' => 'array',
        'hang_lech' => 'array',
    ];

    /**
     * Boot method for auto-calculations
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            // Auto calculate tong_tien_thuc_te
            $model->tong_tien_thuc_te = $model->tien_mat + $model->tien_chuyen_khoan;
            
            // Auto calculate tien_lech
            $model->tien_lech = $model->tong_tien_thuc_te - $model->tong_tien_ly_thuyet;
            
            // Auto calculate hang_lech
            $tonDau = $model->ton_dau_ca;
            $tonCuoi = $model->ton_cuoi_ca;
            
            // Ensure they are arrays (cast might not work in saving event)
            if (is_string($tonDau)) {
                $tonDau = json_decode($tonDau, true) ?? [];
            }
            if (is_string($tonCuoi)) {
                $tonCuoi = json_decode($tonCuoi, true) ?? [];
            }
            
            $model->hang_lech = static::calculateStockDiscrepancy(
                $tonDau ?? [],
                $tonCuoi ?? []
            );
        });
    }

    /**
     * Calculate stock discrepancy
     */
    public static function calculateStockDiscrepancy(array $tonDau, array $tonCuoi): array
    {
        $lech = [];
        $allProducts = array_unique(array_merge(array_keys($tonDau), array_keys($tonCuoi)));
        
        foreach ($allProducts as $productId) {
            $dauCa = $tonDau[$productId] ?? 0;
            $cuoiCa = $tonCuoi[$productId] ?? 0;
            $lech[$productId] = $cuoiCa - $dauCa;
        }
        
        return $lech;
    }

    /**
     * Relationships
     */
    public function diemBan(): BelongsTo
    {
        return $this->belongsTo(Agency::class, 'diem_ban_id');
    }

    public function nguoiChot(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nguoi_chot_id');
    }

    public function nguoiDuyet(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nguoi_duyet_id');
    }

    public function caLamViec(): BelongsTo
    {
        return $this->belongsTo(CaLamViec::class, 'ca_lam_viec_id');
    }

    /**
     * Scopes
     */
    public function scopePending($query)
    {
        return $query->where('trang_thai', 'cho_duyet');
    }

    public function scopeApproved($query)
    {
        return $query->where('trang_thai', 'da_duyet');
    }

    public function scopeByDate($query, $date)
    {
        return $query->whereDate('ngay_chot', $date);
    }

    public function scopeByDiemBan($query, $diemBanId)
    {
        return $query->where('diem_ban_id', $diemBanId);
    }

    /**
     * Generate text for Zalo reporting
     */
    public function generateZaloReport(): string
    {
        $diemBan = $this->diemBan->ten_diem_ban ?? 'N/A';
        $ngay = $this->ngay_chot->format('d/m/Y');
        
        $report = "📊 BÁO CÁO CHỐT CA\n";
        $report .= "Điểm: {$diemBan}\n";
        $report .= "Ngày: {$ngay}\n";
        $report .= "-------------------\n";
        $report .= "💵 TIỀN:\n";
        $report .= "Mặt: " . number_format($this->tien_mat) . " đ\n";
        $report .= "CK: " . number_format($this->tien_chuyen_khoan) . " đ\n";
        $report .= "Tổng: " . number_format($this->tong_tien_thuc_te) . " đ\n";
        $report .= "Lý thuyết: " . number_format($this->tong_tien_ly_thuyet) . " đ\n";
        $report .= "Lệch: " . number_format($this->tien_lech) . " đ ";
        $report .= $this->tien_lech > 0 ? "✅ (Thừa)" : ($this->tien_lech < 0 ? "⚠️ (Thiếu)" : "👌");
        
        return $report;
    }
}
