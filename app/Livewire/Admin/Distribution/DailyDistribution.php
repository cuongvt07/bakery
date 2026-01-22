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
        $this->agencies = Agency::where('trang_thai', 'hoat_dong')
            ->where('ten_diem_ban', 'not like', '%Xưởng%')
            ->get();
        
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
        
        // Initialize the array if not exists
        if (!isset($this->distributionData[$batchId])) {
            $this->distributionData[$batchId] = [];
        }
        
        // Allow empty input
        if ($value === '' || $value === null) {
            $this->distributionData[$batchId][$productId] = null;
            return;
        }
        
        // Convert to integer
        $intValue = (int)$value;
        
        // Reject negative numbers
        if ($intValue < 0) {
            $this->distributionData[$batchId][$productId] = 0;
            return;
        }
        
        // Get batch and detail
        $batch = ProductionBatch::with('details')->find($batchId);
        if (!$batch) {
            $this->distributionData[$batchId][$productId] = $intValue;
            return;
        }
        
        $detail = $batch->details->where('san_pham_id', $productId)->first();
        if (!$detail) {
            $this->distributionData[$batchId][$productId] = $intValue;
            return;
        }
        
        // Calculate available quantity
        $baseQty = $detail->so_luong_thuc_te;
        
        $failedQty = abs(\App\Models\LichSuCapNhatMe::where('me_san_xuat_id', $batchId)
            ->where('san_pham_id', $productId)
            ->where('loai', 'hong')
            ->sum('so_luong_doi') ?? 0);
        
        $adjustedQty = abs(\App\Models\LichSuCapNhatMe::where('me_san_xuat_id', $batchId)
            ->where('san_pham_id', $productId)
            ->where('loai', 'dieu_chinh')
            ->sum('so_luong_doi') ?? 0);
        
        $returnedQty = abs(\App\Models\LichSuCapNhatMe::where('me_san_xuat_id', $batchId)
            ->where('san_pham_id', $productId)
            ->where('loai', 'hoan')
            ->sum('so_luong_doi') ?? 0);
        
        $soldAtAgency = 0;
        if ($this->selectedAgencyId) {
            $soldAtAgency = abs(\App\Models\LichSuCapNhatMe::where('me_san_xuat_id', $batchId)
                ->where('san_pham_id', $productId)
                ->where('diem_ban_id', $this->selectedAgencyId)
                ->where('loai', 'ban')
                ->sum('so_luong_doi') ?? 0);
        }
        
        // Calculate maximum available
        $maxAvailable = $baseQty - $failedQty - $adjustedQty - $soldAtAgency + $returnedQty;
        $maxAvailable = max(0, $maxAvailable); // Never negative
        
        // Auto-cap to max available
        if ($intValue > $maxAvailable) {
            $this->distributionData[$batchId][$productId] = $maxAvailable;
        } else {
            $this->distributionData[$batchId][$productId] = $intValue;
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
                        
                        // Verify available quantity using same calculation as render()
                        $batch = ProductionBatch::with('details')->find($batchId);
                        $detail = $batch->details->where('san_pham_id', $productId)->first();
                        
                        if (!$detail) continue;
                        
                        // Calculate global available (accounting for failures and adjustments)
                        $baseQty = $detail->so_luong_thuc_te;
                        
                        $failedQty = abs(\App\Models\LichSuCapNhatMe::where('me_san_xuat_id', $batchId)
                            ->where('san_pham_id', $productId)
                            ->where('loai', 'hong')
                            ->sum('so_luong_doi'));
                        
                        $adjustedQty = abs(\App\Models\LichSuCapNhatMe::where('me_san_xuat_id', $batchId)
                            ->where('san_pham_id', $productId)
                            ->where('loai', 'dieu_chinh')
                            ->sum('so_luong_doi'));
                        
                        $returnedQty = abs(\App\Models\LichSuCapNhatMe::where('me_san_xuat_id', $batchId)
                            ->where('san_pham_id', $productId)
                            ->where('loai', 'hoan')
                            ->sum('so_luong_doi'));
                        
                        $distributedGlobal = PhanBoHangDiemBan::where('me_san_xuat_id', $batchId)
                            ->where('san_pham_id', $productId)
                            ->sum('so_luong');
                        
                        $available = $baseQty - $failedQty - $adjustedQty - $distributedGlobal + $returnedQty;
                        
                        if ($quantity > $available) {
                            throw new \Exception("Sản phẩm {$detail->product->ten_san_pham} chỉ còn {$available} khả dụng!");
                        }
                        
                        // Always create new distribution record
                        // Each distribution is a separate record, not accumulated
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
                        
                        // Note: Don't log to history here - phan_bo_hang_diem_ban already tracks this
                        // History is only for: hong, hoan, dieu_chinh
                        
                        $totalSaved++;
                    }
                }
            });
            
            $agencyName = $this->agencies->find($this->selectedAgencyId)->ten_diem_ban;
            session()->flash('success', "✅ Đã lưu {$totalSaved} phân bổ cho {$agencyName}!");
            
            // Redirect to distribution list
            return $this->redirect(route('admin.distribution.index'), navigate: true);
            
        } catch (\Exception $e) {
            session()->flash('error', '❌ Lỗi: ' . $e->getMessage());
        }
    }
    
    public function render()
    {
        // Load all completed batches that are still valid (not expired)
        // Changed from: only batches produced on selected date
        // To: all batches with expiry date >= today
        $batches = ProductionBatch::with(['details.product', 'distributions'])
            ->where('trang_thai', 'hoan_thanh')
            ->where(function($query) {
                $query->whereDate('han_su_dung', '>=', Carbon::today())
                      ->orWhereNull('han_su_dung'); // Include batches without expiry date
            })
            ->orderBy('ngay_san_xuat', 'desc')
            ->orderBy('buoi')
            ->orderBy('created_at')
            ->get();
        
        // Calculate available quantities for each batch-product
        $availability = [];
        foreach ($batches as $batch) {
            foreach ($batch->details as $detail) {
                // Base quantity = After QC (thực tế)
                $baseQty = $detail->so_luong_thuc_te;
                
                // Method 1: Get all distributed quantity for this product from this batch to ALL locations
                $distributedGlobal = \App\Models\PhanBoHangDiemBan::where('me_san_xuat_id', $batch->id)
                    ->where('san_pham_id', $detail->san_pham_id)
                    ->sum('so_luong');
                
                // Method 2: Get distributed quantity to ONLY selected agency
                $distributedToAgency = 0;
                if ($this->selectedAgencyId) {
                    $distributedToAgency = \App\Models\PhanBoHangDiemBan::where('me_san_xuat_id', $batch->id)
                        ->where('san_pham_id', $detail->san_pham_id)
                        ->where('diem_ban_id', $this->selectedAgencyId)
                        ->sum('so_luong');
                }
                
                // Method 3: Get failure/loss quantity from batch history (GLOBAL - affects all locations)
                // 'hong' (defect) and 'dieu_chinh' (adjustment) reduce available quantity globally
                // 'hoan' (return) increases available quantity
                $failedQty = abs(\App\Models\LichSuCapNhatMe::where('me_san_xuat_id', $batch->id)
                    ->where('san_pham_id', $detail->san_pham_id)
                    ->where('loai', 'hong') // Defect
                    ->sum('so_luong_doi'));
                
                $adjustedQty = abs(\App\Models\LichSuCapNhatMe::where('me_san_xuat_id', $batch->id)
                    ->where('san_pham_id', $detail->san_pham_id)
                    ->where('loai', 'dieu_chinh') // Adjustment
                    ->sum('so_luong_doi'));
                
                $returnedQty = abs(\App\Models\LichSuCapNhatMe::where('me_san_xuat_id', $batch->id)
                    ->where('san_pham_id', $detail->san_pham_id)
                    ->where('loai', 'hoan') // Return
                    ->sum('so_luong_doi'));
                
                // Method 4: Get sold quantity from ONLY selected agency
                $soldAtAgency = 0;
                if ($this->selectedAgencyId) {
                    $soldAtAgency = abs(\App\Models\LichSuCapNhatMe::where('me_san_xuat_id', $batch->id)
                        ->where('san_pham_id', $detail->san_pham_id)
                        ->where('diem_ban_id', $this->selectedAgencyId)
                        ->where('loai', 'ban')
                        ->sum('so_luong_doi'));
                }
                
                // For display: show what's available globally (accounting for failures, adjustments, and sales)
                // Available at factory = Base - Failed - Adjusted - Sold + Returned
                // NOTE: Do NOT subtract distributed - that's what we're about to do now!
                $availableGlobal = $baseQty - $failedQty - $adjustedQty - $soldAtAgency + $returnedQty;
                
                // For this specific agency: what's available to distribute = Available at factory - Already distributed to agency
                // Then deduct sales at this agency
                $distributedToAgency = \App\Models\PhanBoHangDiemBan::where('me_san_xuat_id', $batch->id)
                    ->where('san_pham_id', $detail->san_pham_id)
                    ->where('diem_ban_id', $this->selectedAgencyId)
                    ->sum('so_luong');
                
                $availableToDistribute = max(0, $availableGlobal - $distributedToAgency);
                
                $availability[$batch->id][$detail->san_pham_id] = [
                    'total' => $baseQty,
                    'failed' => $failedQty,
                    'adjusted' => $adjustedQty,
                    'returned' => $returnedQty,
                    'distributed' => $distributedGlobal,
                    'distributed_to_agency' => $distributedToAgency,
                    'sold_at_agency' => $soldAtAgency,
                    'available' => $availableGlobal, // Available at factory (for display)
                    'available_to_distribute' => $availableToDistribute, // Can still distribute
                ];
            }
        }
        
        return view('livewire.admin.distribution.daily-distribution', [
            'batches' => $batches,
            'availability' => $availability,
        ]);
    }
}
