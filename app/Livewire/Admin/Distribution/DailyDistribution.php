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
    
    // Distribution Data
    public $selectedAgencyId = null;
    public $selectedSession = 'sang'; // Morning/Afternoon
    public $distributionQuantity = 0;
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
        $this->productionBatches = ProductionBatch::with(['recipe.product', 'distributions'])
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
        $this->currentBatch = ProductionBatch::with(['details.recipe.product', 'details.product', 'distributions'])->find($batchId);
        
        // Reset distribution data
        $this->distributionQuantity = 0;
        $this->selectedAgencyId = null;
        $this->selectedProductId = null;
    }
    
    public $selectedProductId = null;
    
    public function selectAgency($agencyId)
    {
        $this->selectedAgencyId = $agencyId;
        
        //Load existing distribution for this agency if any
        if ($this->currentBatch) {
            $existing = $this->currentBatch->distributions()
                ->where('diem_ban_id', $agencyId)
                ->where('buoi', $this->selectedSession)
                ->first();
                
            $this->distributionQuantity = $existing ? $existing->so_luong : 0;
        }
    }
    
    public function saveAgencyDistribution()
    {
        if (!$this->currentBatch || !$this->selectedAgencyId || !$this->selectedProductId) {
            session()->flash('error', 'Vui lòng chọn mẻ sản xuất, sản phẩm và điểm bán!');
            return;
        }
        
        $this->validate([
            'distributionQuantity' => 'required|numeric|min:0',
        ]);
        
        // Find the detail for selected product
        $detail = $this->currentBatch->details()->where('san_pham_id', $this->selectedProductId)->first();
        
        if (!$detail) {
            session()->flash('error', 'Không tìm thấy sản phẩm trong mẻ!');
            return;
        }
        
        // Check available quantity for this specific product
        $availableQty = $detail->available_quantity;
        $existing = $this->currentBatch->distributions()
            ->where('diem_ban_id', $this->selectedAgencyId)
            ->where('san_pham_id', $this->selectedProductId)
            ->where('buoi', $this->selectedSession)
            ->first();
        
        $currentlyDistributed = $existing ? $existing->so_luong : 0;
        $newTotal = $availableQty + $currentlyDistributed;
        
        if ($this->distributionQuantity > $newTotal) {
            session()->flash('error', 'Vượt quá số lượng khả dụng! Còn lại: ' . $newTotal);
            return;
        }
        
        DB::transaction(function () use ($existing, $detail) {
            if ($existing) {
                // Update existing distribution
                $existing->update([
                    'so_luong' => $this->distributionQuantity,
                ]);
            } else {
                // Create new distribution
                PhanBoHangDiemBan::create([
                    'me_san_xuat_id' => $this->currentBatch->id,
                    'diem_ban_id' => $this->selectedAgencyId,
                    'buoi' => $this->selectedSession,
                    'so_luong' => $this->distributionQuantity,
                    'san_pham_id' => $this->selectedProductId,
                    'nguoi_nhan_id' => null,
                    'trang_thai' => 'chua_nhan',
                ]);
            }
        });
        
        // Reload batch to update available quantity
        $this->currentBatch->refresh();
        
        session()->flash('success', 'Đã lưu phân bổ cho ' . $this->agencies->find($this->selectedAgencyId)->ten_diem_ban);
    }
    
    public function render()
    {
        return view('livewire.admin.distribution.daily-distribution');
    }
}
