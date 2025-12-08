<?php

namespace App\Livewire\Admin\Distribution;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Product;
use App\Models\Agency;
use App\Models\ProductionBatch;
use App\Models\PhanBoHangDiemBan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

#[Layout('components.layouts.app')]
class DailyDistribution extends Component
{
    public $date;
    public $selectedAgencyId = null;
    public $selectedSession = 'sang';
    
    // Distribution data: [batchId][productId] = quantity
    public $distributionData = [];
    
    // Collapsed state for each batch: [batchId] = true/false
    public $collapsedBatches = [];
    
    public $agencies = [];
    
    public function mount()
    {
        $this->date = request()->query('date', Carbon::today()->format('Y-m-d'));
        
        // Load active agencies
        $this->agencies = Agency::where('trang_thai', 'hoat_dong')->get();
        
        // Default to first agency
        if ($this->agencies->isNotEmpty() && !$this->selectedAgencyId) {
            $this->selectedAgencyId = $this->agencies->first()->id;
        }
    }
    
    public function toggleBatch($batchId)
    {
        $this->collapsedBatches[$batchId] = !($this->collapsedBatches[$batchId] ?? false);
    }
    
    
    public function updatedDistributionData($value, $key)
    {
        // Validate input: key format is "batchId.productId"
        $parts = explode('.', $key);
        if (count($parts) !== 2) return;
        
        [$batchId, $productId] = $parts;
        
        // Get availability
        $batch = ProductionBatch::with('details')->find($batchId);
        if (!$batch) return;
        
        $detail = $batch->details->where('san_pham_id', $productId)->first();
        if (!$detail) return;
        
        $distributed = PhanBoHangDiemBan::where('me_san_xuat_id', $batchId)
            ->where('san_pham_id', $productId)
            ->sum('so_luong');
            
        $available = $detail->so_luong_thuc_te - $distributed;
        
        // Validate and cap value
        if (!is_numeric($value) || $value < 0) {
            $this->distributionData[$batchId][$productId] = 0;
        } elseif ($value > $available) {
            $this->distributionData[$batchId][$productId] = $available;
            session()->flash('warning', "Chỉ còn {$available} sản phẩm khả dụng!");
        }
    }
    
    public function saveDistributions()
    {
        if (!$this->selectedAgencyId) {
            session()->flash('error', 'Vui lòng chọn điểm bán!');
            return;
        }
        
        $totalSaved = 0;
        
        try {
            DB::transaction(function () use (&$totalSaved) {
                foreach ($this->distributionData as $batchId => $products) {
                    foreach ($products as $productId => $quantity) {
                        if ($quantity <= 0) continue;
                        
                        // Verify available quantity
                        $batch = ProductionBatch::with('details')->find($batchId);
                        $detail = $batch->details->where('san_pham_id', $productId)->first();
                        
                        if (!$detail) continue;
                        
                        $distributed = PhanBoHangDiemBan::where('me_san_xuat_id', $batchId)
                            ->where('san_pham_id', $productId)
                            ->sum('so_luong');
                            
                        $available = $detail->so_luong_thuc_te - $distributed;
                        
                        if ($quantity > $available) {
                            throw new \Exception("Sản phẩm {$detail->product->ten_san_pham} chỉ còn {$available} khả dụng!");
                        }
                        
                        // Check if distribution exists
                        $existing = PhanBoHangDiemBan::where('me_san_xuat_id', $batchId)
                            ->where('diem_ban_id', $this->selectedAgencyId)
                            ->where('san_pham_id', $productId)
                            ->where('buoi', $this->selectedSession)
                            ->first();
                        
                        if ($existing) {
                            $existing->update([
                                'so_luong' => $existing->so_luong + $quantity,
                                'nguoi_tao_id' => Auth::id(),
                            ]);
                        } else {
                            PhanBoHangDiemBan::create([
                                'me_san_xuat_id' => $batchId,
                                'diem_ban_id' => $this->selectedAgencyId,
                                'buoi' => $this->selectedSession,
                                'so_luong' => $quantity,
                                'san_pham_id' => $productId,
                                'nguoi_tao_id' => Auth::id(),
                                'nguoi_nhan_id' => null,
                                'trang_thai' => 'chua_nhan',
                                'loai_phan_bo' => 'tu_me_sx',
                            ]);
                        }
                        
                        $totalSaved++;
                    }
                }
            });
            
            // Reset distribution data
            $this->distributionData = [];
            
            $agencyName = $this->agencies->find($this->selectedAgencyId)->ten_diem_ban;
            session()->flash('success', "✅ Đã lưu {$totalSaved} phân bổ cho {$agencyName}!");
            
        } catch (\Exception $e) {
            session()->flash('error', '❌ Lỗi: ' . $e->getMessage());
        }
    }
    
    public function render()
    {
        // Load all completed batches for the date
        $batches = ProductionBatch::with(['details.product', 'distributions'])
            ->whereDate('ngay_san_xuat', $this->date)
            ->where('trang_thai', 'hoan_thanh')
            ->orderBy('buoi')
            ->orderBy('created_at')
            ->get();
        
        // Calculate available quantities for each batch-product
        $availability = [];
        foreach ($batches as $batch) {
            foreach ($batch->details as $detail) {
                $distributed = $batch->distributions()
                    ->where('san_pham_id', $detail->san_pham_id)
                    ->sum('so_luong');
                
                $availability[$batch->id][$detail->san_pham_id] = [
                    'total' => $detail->so_luong_thuc_te,
                    'distributed' => $distributed,
                    'available' => $detail->so_luong_thuc_te - $distributed,
                ];
            }
        }
        
        return view('livewire.admin.distribution.daily-distribution', [
            'batches' => $batches,
            'availability' => $availability,
        ]);
    }
}
