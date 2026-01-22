<?php

namespace App\Livewire\Admin\Batch;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\ProductionBatch;
use App\Models\PhanBoHangDiemBan;
use App\Models\Agency;
use App\Models\ProductionBatchDetail;
use App\Models\LichSuCapNhatMe;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.app')]
class BatchMonitoring extends Component
{
    use WithPagination;

    // Filters
    public $search = '';

    // Modal State
    public $showModal = false;
    public $selectedBatch = null;
    
    // Adjustment Form
    public $adjustProductId = '';
    public $adjustQty = '';
    public $adjustNote = '';

    public function updatedSearch() { $this->resetPage(); }

    public function render()
    {
        // Query ProductionBatch directly - show all completed batches
        $query = ProductionBatch::query()
            ->with(['details.product', 'creator'])
            ->where('trang_thai', 'hoan_thanh');

        // Search filter
        if ($this->search) {
            $query->where(function($q) {
                $q->where('ma_me', 'like', '%' . $this->search . '%')
                  ->orWhereHas('details.product', function($pq) {
                      $pq->where('ten_san_pham', 'like', '%' . $this->search . '%');
                  });
            });
        }

        $batches = $query->orderBy('ngay_san_xuat', 'desc')->paginate(15);

        // Enrich each batch with calculated stats
        foreach ($batches as $batch) {
            $batch->total_expected = $batch->details->sum('so_luong_du_kien');
            $batch->total_failed = $batch->details->sum('so_luong_that_bai');
            $batch->total_actual = $batch->details->sum('so_luong_thuc_te');
            
            // Calculate total distributed
            $batch->total_distributed = PhanBoHangDiemBan::where('me_san_xuat_id', $batch->id)->sum('so_luong');
            
            // Calculate total sold (from history - negative values mean sold)
            $batch->total_sold = abs(LichSuCapNhatMe::where('me_san_xuat_id', $batch->id)
                ->where('loai', 'ban')
                ->sum('so_luong_doi'));
            
            // Calculate remaining = actual - sold
            $batch->total_remaining = $batch->total_actual - $batch->total_sold;
            
            // Get product names for display
            $batch->product_names = $batch->details->pluck('product.ten_san_pham')->unique()->implode(', ');
            
            // Get nearest expiry date
            $batch->nearest_expiry = $batch->details->min('han_su_dung');
        }
        
        return view('livewire.admin.batch.batch-monitoring', [
            'batches' => $batches,
        ]);
    }

    public function openBatchDetail($batchId)
    {
        $this->reset(['adjustProductId', 'adjustQty', 'adjustNote']);
        
        $batch = ProductionBatch::with(['details.product', 'creator', 'qcPersonnel'])->find($batchId);
        if (!$batch) return;

        // Get distributions grouped by product
        $distributions = PhanBoHangDiemBan::with(['diemBan', 'product'])
            ->where('me_san_xuat_id', $batchId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('san_pham_id');

        // Get adjustment history
        $history = LichSuCapNhatMe::with(['nguoiCapNhat', 'product'])
            ->where('me_san_xuat_id', $batchId)
            ->orderBy('created_at', 'desc')
            ->get();

        // Build products with distribution details
        $products = $batch->details->map(function($d) use ($distributions) {
            $productDists = $distributions->get($d->san_pham_id, collect());
            $totalDistributed = $productDists->sum('so_luong');
            
            return [
                'id' => $d->san_pham_id,
                'detail_id' => $d->id,
                'name' => $d->product->ten_san_pham,
                'expected' => $d->so_luong_du_kien,
                'failed' => $d->so_luong_that_bai,
                'actual' => $d->so_luong_thuc_te,
                'distributed' => $totalDistributed,
                'remaining' => $d->so_luong_thuc_te - $totalDistributed, // Còn ở xưởng
                'expiry' => $d->han_su_dung ? Carbon::parse($d->han_su_dung)->format('d/m/Y') : '-',
                'distributions' => $productDists->map(function($dist) {
                    return [
                        'shop' => $dist->diemBan->ten_diem_ban ?? 'Unknown',
                        'qty' => $dist->so_luong,
                        'date' => $dist->created_at->format('d/m H:i'),
                    ];
                })->values()->toArray(),
            ];
        });

        $this->selectedBatch = [
            'id' => $batch->id,
            'code' => $batch->ma_me,
            'date' => $batch->ngay_san_xuat->format('d/m/Y'),
            'shift' => $batch->buoi == 'sang' ? 'Sáng' : 'Chiều',
            'creator' => $batch->creator->ho_ten ?? 'Unknown',
            'qc_user' => $batch->qcPersonnel->ho_ten ?? '-',
            'status' => $batch->trang_thai,
            'products' => $products,
            'history' => $history,
            'total_expected' => $products->sum('expected'),
            'total_failed' => $products->sum('failed'),
            'total_actual' => $products->sum('actual'),
            'total_distributed' => $products->sum('distributed'),
        ];
        
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedBatch = null;
    }

    public function saveAdjustment()
    {
        $this->validate([
            'adjustProductId' => 'required',
            'adjustQty' => 'required|integer|min:1',
            'adjustNote' => 'nullable|string|max:255',
        ]);

        if (!$this->selectedBatch) return;

        $batchId = $this->selectedBatch['id'];
        
        // Find the detail
        $product = collect($this->selectedBatch['products'])->firstWhere('id', $this->adjustProductId);
        if (!$product) return;
        
        $detailId = $product['detail_id'];

        DB::transaction(function() use ($batchId, $detailId) {
            $detail = ProductionBatchDetail::find($detailId);
            if (!$detail) return;
            
            $oldFailed = $detail->so_luong_that_bai;
            $newFailed = $oldFailed + $this->adjustQty;
            
            // Update Global Detail
            $detail->so_luong_that_bai = $newFailed;
            $detail->so_luong_thuc_te = $detail->so_luong_du_kien - $newFailed;
            $detail->save();
            
            // Create Log (diem_ban_id = null for batch-level adjustment)
            LichSuCapNhatMe::create([
                'me_san_xuat_id' => $batchId,
                'san_pham_id' => $this->adjustProductId,
                'diem_ban_id' => null, // Batch-level, not shop-specific
                'loai' => LichSuCapNhatMe::LOAI_HONG, // Mark as defect
                'nguoi_cap_nhat_id' => Auth::id(),
                'so_luong_doi' => -$this->adjustQty, // Negative = reduce
                'du_lieu_cu' => $oldFailed,
                'du_lieu_moi' => $newFailed,
                'ghi_chu' => $this->adjustNote ?? 'Báo hỏng từ BatchMonitoring'
            ]);
        });

        session()->flash('message', 'Đã cập nhật số lượng hỏng thành công!');
        
        // Refresh Modal Data
        $this->openBatchDetail($batchId);
    }
}
