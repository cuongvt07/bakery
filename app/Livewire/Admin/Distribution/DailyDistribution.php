<?php

namespace App\Livewire\Admin\Distribution;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Product;
use App\Models\Agency;
use App\Models\ProductionBatch;
use App\Models\PhanBoHangDiemBan;
use App\Models\ChiTietPhanBo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

#[Layout('components.layouts.app')]
class DailyDistribution extends Component
{
    public $date;
    public $selectedProductionBatchId = null;
    
    public $productionBatches = [];
    public $currentBatch = null;
    
    // Distribution Data - NEW: Batch input
    public $selectedAgencyId = null;
    public $selectedSession = 'sang';
    public $distributionData = []; // ['product_id' => quantity]
    public $agencies = [];
    
    public function mount()
    {
        $this->date = request()->query('date', Carbon::today()->format('Y-m-d'));
        $batchId = request()->query('batch_id');
        
        // Load Agencies
        $this->agencies = Agency::where('trang_thai', 'hoat_dong')->get();
        
        $this->loadProductionBatches($batchId);
    }
    
    public function loadProductionBatches($defaultBatchId = null)
    {
        // Load completed production batches for this date
        $this->productionBatches = ProductionBatch::with(['details.product', 'distributions'])
            ->whereDate('ngay_san_xuat', $this->date)
            ->where('trang_thai', 'hoan_thanh')
            ->orderBy('buoi')
            ->orderBy('created_at')
            ->get();
            
        if ($this->productionBatches->isNotEmpty()) {
            if ($defaultBatchId) {
                $this->selectProductionBatch($defaultBatchId);
            } elseif (!$this->selectedProductionBatchId) {
                // Select the first batch by default
                $this->selectProductionBatch($this->productionBatches->first()->id);
            }
        } else {
            $this->currentBatch = null;
        }
    }
    
    public function selectProductionBatch($batchId)
    {
        $this->selectedProductionBatchId = $batchId;
        $this->currentBatch = ProductionBatch::with(['details.product', 'distributions'])->find($batchId);
        
        // Reset distribution data
        $this->distributionData = [];
        $this->selectedAgencyId = null;
    }
    
    public function saveAgencyDistribution()
    {
        if (!$this->currentBatch || !$this->selectedAgencyId) {
            session()->flash('error', 'Vui lòng chọn điểm bán!');
            return;
        }
        
        // Filter out empty values
        $toDistribute = array_filter($this->distributionData, fn($qty) => $qty > 0);
        
        if (empty($toDistribute)) {
            session()->flash('error', 'Vui lòng nhập số lượng cho ít nhất 1 sản phẩm!');
            return;
        }
        
        try {
            DB::transaction(function () use ($toDistribute) {
                foreach ($toDistribute as $productId => $quantity) {
                    // Find the detail for this product
                    $detail = $this->currentBatch->details()->where('san_pham_id', $productId)->first();
                    
                    if (!$detail) {
                        continue;
                    }
                    
                    // Check available quantity
                    $distributed = $this->currentBatch->distributions()
                        ->where('san_pham_id', $productId)
                        ->sum('so_luong');
                    $available = $detail->so_luong_thuc_te - $distributed;
                    
                    if ($quantity > $available) {
                        throw new \Exception("Sản phẩm {$detail->product->ten_san_pham} chỉ còn {$available} khả dụng!");
                    }
                    
                    // Check if distribution exists
                    $existing = PhanBoHangDiemBan::where('me_san_xuat_id', $this->currentBatch->id)
                        ->where('diem_ban_id', $this->selectedAgencyId)
                        ->where('san_pham_id', $productId)
                        ->where('buoi', $this->selectedSession)
                        ->first();
                    
                    if ($existing) {
                        // Update existing
                        $existing->update(['so_luong' => $quantity]);
                    } else {
                        // Create new
                        PhanBoHangDiemBan::create([
                            'me_san_xuat_id' => $this->currentBatch->id,
                            'diem_ban_id' => $this->selectedAgencyId,
                            'buoi' => $this->selectedSession,
                            'so_luong' => $quantity,
                            'san_pham_id' => $productId,
                            'nguoi_nhan_id' => null,
                            'trang_thai' => 'chua_nhan',
                        ]);
                    }
                }
            });
            
            // Get agency name before reset
            $agencyName = $this->agencies->find($this->selectedAgencyId)->ten_diem_ban;
            
            // Reload batch
            $this->currentBatch->refresh();
            
            // Only reset distributionData, keep agency selected for next distribution
            $this->distributionData = [];
            // DO NOT reset selectedAgencyId - user may want to continue with same agency
            
            session()->flash('success', "✅ Đã lưu phân bổ cho {$agencyName} thành công!");
            
        } catch (\Exception $e) {
            session()->flash('error', '❌ Lỗi: ' . $e->getMessage());
            \Log::error('Distribution save error: ' . $e->getMessage());
        }
    }
    
    public function render()
    {
        return view('livewire.admin.distribution.daily-distribution');
    }
}
