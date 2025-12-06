<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class BatchBanHang extends Model
{
    protected $table = 'batch_ban_hang';

    protected $fillable = [
        'diem_ban_id',
        'ca_lam_viec_id',
        'nguoi_chot_id',
        'ngay_chot',
        'gio_chot',
        'so_don',
        'tong_tien',
        'chi_tiet_don',
    ];

    protected $casts = [
        'chi_tiet_don' => 'array',
        'tong_tien' => 'decimal:2',
        'ngay_chot' => 'date',
        'gio_chot' => 'datetime:H:i',
    ];

    /**
     * Relationships
     */
    public function caLamViec(): BelongsTo
    {
        return $this->belongsTo(CaLamViec::class, 'ca_lam_viec_id');
    }

    public function diemBan(): BelongsTo
    {
        return $this->belongsTo(Agency::class, 'diem_ban_id');
    }

    public function nguoiChot(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nguoi_chot_id');
    }

    /**
     * Create batch from pending sales
     */
    public static function createFromPending(array $pendingSaleIds, $userId): self
    {
        return DB::transaction(function () use ($pendingSaleIds, $userId) {
            // Get all pending sales
            $pendingSales = PendingSale::whereIn('id', $pendingSaleIds)
                ->where('trang_thai', 'pending')
                ->get();

            if ($pendingSales->isEmpty()) {
                throw new \Exception('No pending sales found');
            }

            $firstSale = $pendingSales->first();
            $totalAmount = $pendingSales->sum('tong_tien');
            $totalCount = $pendingSales->count();

            // Create batch
            $batch = self::create([
                'diem_ban_id' => $firstSale->diem_ban_id,
                'ca_lam_viec_id' => $firstSale->ca_lam_viec_id,
                'nguoi_chot_id' => $userId,
                'ngay_chot' => now()->toDateString(),
                'gio_chot' => now()->toTimeString(),
                'so_don' => $totalCount,
                'tong_tien' => $totalAmount,
                'chi_tiet_don' => $pendingSales->map(function ($sale) {
                    return [
                        'id' => $sale->id,
                        'thoi_gian' => $sale->thoi_gian,
                        'chi_tiet' => $sale->chi_tiet,
                        'tong_tien' => $sale->tong_tien,
                    ];
                })->toArray(),
            ]);

            // Mark pending sales as confirmed
            PendingSale::whereIn('id', $pendingSaleIds)
                ->update(['trang_thai' => 'confirmed']);

            // Update inventory
            $batch->updateInventory();

            return $batch;
        });
    }

    /**
     * Update inventory after batch confirmation
     */
    public function updateInventory(): void
    {
        $productSales = [];

        // Aggregate product quantities from all sales in this batch
        foreach ($this->chi_tiet_don as $sale) {
            foreach ($sale['chi_tiet'] as $item) {
                $productId = $item['product_id'];
                $qty = $item['so_luong'];

                if (!isset($productSales[$productId])) {
                    $productSales[$productId] = 0;
                }
                $productSales[$productId] += $qty;
            }
        }

        // Update shift details (ChiTietCaLam)
        foreach ($productSales as $productId => $totalSold) {
            $shiftDetail = ChiTietCaLam::where('ca_lam_viec_id', $this->ca_lam_viec_id)
                ->where('san_pham_id', $productId)
                ->first();

            if ($shiftDetail) {
                $shiftDetail->increment('so_luong_ban', $totalSold);
            }
        }
    }
}
