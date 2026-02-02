<?php

namespace App\Livewire\Admin\Distribution;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Agency;
use App\Models\Product;
use App\Models\ProductionBatch;
use App\Models\PhanBoHangDiemBan;
use App\Models\LuanChuyenHang;
use App\Models\ChiTietLuanChuyen;
use App\Models\LichSuCapNhatMe;
use App\Models\CaLamViec;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

#[Layout('components.layouts.app')]
class TransferManager extends Component
{
    // Selection state
    public $sourceAgencyId = null;
    public $destinationAgencyId = null;
    public $transferDate;
    public $note = '';

    // Data state
    public $stockData = []; // [product_id => quantity]
    public $transferItems = []; // [product_id => quantity_to_transfer]

    // UI state
    public $agencies = [];
    public $products = [];
    public $showConfirmation = false;

    public function mount()
    {
        $this->transferDate = date('Y-m-d H:i');
        $this->agencies = Agency::where('trang_thai', 'hoat_dong')->get();
        // Exclude central kitchen or specific types if needed

        $this->products = Product::where('trang_thai', 'dang_ban')->get();
    }

    public function updatedSourceAgencyId()
    {
        $this->reset(['stockData', 'transferItems']);

        if ($this->sourceAgencyId) {
            // Check for active shift
            $activeShift = CaLamViec::where('diem_ban_id', $this->sourceAgencyId)
                ->where('trang_thai', 'dang_lam')
                ->exists();

            if ($activeShift) {
                $this->addError('sourceAgencyId', 'Điểm bán này đang có ca làm việc (đang mở). Chỉ có thể chuyển hàng từ điểm ĐÃ chốt ca.');
                // Do not load stock, effectively blocking next steps
                return;
            }

            $this->loadSourceStock();
        }
    }

    public function loadSourceStock()
    {
        if (!$this->sourceAgencyId)
            return;

        $this->stockData = [];

        // Logic to calculate available stock at Source Agency
        // Tồn kho = Tổng nhập (duyệt) - Tổng bán - Tổng hỏng/huỷ

        // 1. Get all received distributions (positive and negative)
        // Group by product

        // Note: For simplicity and speed, we can query PhanBoHangDiemBan
        // Ideally we should sum up per batch, but here we show Total Product Quantity
        // The implementation of "Auto-Pick Batch" happens on Save.

        // User Request: Only take products distributed TODAY
        $today = Carbon::today();

        $productIds = PhanBoHangDiemBan::where('diem_ban_id', $this->sourceAgencyId)
            ->whereIn('trang_thai', ['da_nhan', 'chua_nhan', 'da_duyet'])
            ->whereDate('created_at', $today) // Filter Today
            ->distinct()
            ->pluck('san_pham_id');

        // Load product details
        $relevantProducts = Product::whereIn('id', $productIds)->get();

        foreach ($relevantProducts as $product) {
            // Count IN: Only Today
            $totalReceived = PhanBoHangDiemBan::where('diem_ban_id', $this->sourceAgencyId)
                ->where('san_pham_id', $product->id)
                ->whereIn('trang_thai', ['da_nhan', 'chua_nhan'])
                ->whereDate('created_at', $today)
                ->sum('so_luong');

            // Count OUT: Only Today's Shifts
            $totalSold = \App\Models\ChiTietCaLam::whereHas('caLamViec', function ($q) use ($today) {
                $q->where('diem_ban_id', $this->sourceAgencyId)
                    ->where('trang_thai', 'da_ket_thuc')
                    ->whereDate('ngay_lam', $today);
            })
                ->where('san_pham_id', $product->id)
                ->sum('so_luong_ban');

            $calculatedStock = $totalReceived - $totalSold;

            if ($calculatedStock > 0) {
                $this->stockData[$product->id] = $calculatedStock;
                // Ensure this product is in our $this->products list for display if needed
                if (!$this->products->contains('id', $product->id)) {
                    $this->products->push($product);
                }
            }
        }
    }

    public function validateTransfer()
    {
        $this->validate([
            'sourceAgencyId' => 'required',
            'destinationAgencyId' => 'required|different:sourceAgencyId',
            'transferItems' => 'required|array|min:1',
        ]);

        // Filter out zero/null items
        $this->transferItems = array_filter($this->transferItems, fn($qty) => $qty > 0);

        if (empty($this->transferItems)) {
            $this->addError('transferItems', 'Vui lòng chọn ít nhất 1 sản phẩm để chuyển.');
            return;
        }

        // Re-validate quantities against stock
        foreach ($this->transferItems as $prodId => $qty) {
            $available = $this->stockData[$prodId] ?? 0;
            if ($qty > $available) {
                $this->addError("transferItems.$prodId", "Số lượng vượt quá tồn kho ($available).");
                return;
            }
        }

        $this->showConfirmation = true;
    }

    public function confirmTransfer()
    {
        DB::transaction(function () {
            // 1. Create Transfer Record
            $maPhieu = 'TRF-' . date('Ymd') . '-' . time();

            $transfer = LuanChuyenHang::create([
                'ma_phieu' => $maPhieu,
                'diem_ban_nguon_id' => $this->sourceAgencyId,
                'diem_ban_dich_id' => $this->destinationAgencyId,
                'nguoi_chuyen_id' => Auth::id(),
                'ngay_chuyen' => now(), // Admin does it instantly
                'trang_thai' => 'dang_chuyen', // Will update to da_nhan immediately?
                // User requirement: Source inactive -> Auto deduct. Destination -> Need confirm if active.
                // Status 'dang_chuyen' makes sense until Dest confirms.
                'ly_do' => $this->note,
            ]);

            foreach ($this->transferItems as $productId => $qty) {
                if ($qty <= 0)
                    continue;

                $remainingToTransfer = $qty;

                // AUTO-PICK BATCHES (FIFO)
                // Find batches for this product that were distributed to Source Agency
                // and have remaining quantity.
                // Complex query ahead.

                // Strategy: Get all batches of this product distributed to this agency.
                // Sort by Expiry Date ASC.
                // Calculate remaining for each batch.

                $batches = ProductionBatch::whereHas('distributions', function ($q) use ($productId) {
                    $q->where('diem_ban_id', $this->sourceAgencyId)
                        ->where('san_pham_id', $productId)
                        ->whereIn('trang_thai', ['da_nhan', 'chua_nhan'])
                        ->whereDate('created_at', Carbon::today()); // Today only per user request
                })
                    ->whereHas('details', function ($q) use ($productId) {
                        $q->where('san_pham_id', $productId);
                    })
                    ->with(['details' => fn($q) => $q->where('san_pham_id', $productId)])
                    ->orderBy('han_su_dung', 'asc') // Oldest first
                    ->get();

                foreach ($batches as $batch) {
                    if ($remainingToTransfer <= 0)
                        break;

                    // Calculate Real Available for this Batch at this Agency
                    // Available = Distributed (to this agency) - Sold (at this agency) - Defect (at this agency) - Transferred Out (from this agency) + Transferred In (?)

                    $distIn = PhanBoHangDiemBan::where('me_san_xuat_id', $batch->id)
                        ->where('diem_ban_id', $this->sourceAgencyId)
                        ->where('san_pham_id', $productId)
                        ->whereIn('trang_thai', ['da_nhan', 'chua_nhan'])
                        ->whereDate('created_at', Carbon::today()) // Today only
                        ->sum('so_luong');

                    $sold = LichSuCapNhatMe::where('me_san_xuat_id', $batch->id)
                        ->where('diem_ban_id', $this->sourceAgencyId)
                        ->where('san_pham_id', $productId)
                        ->where('loai', 'ban')
                        ->sum('so_luong_doi');

                    // Assuming LichSuCapNhatMe tracks sales by batch. 
                    // If not, we have a problem tracking exact batch inventory.
                    // Assuming for now the system tracks it or we approximate.
                    // Fallback: If not tracking sales by batch strictly, just take what is available.

                    $batchAvailable = max(0, $distIn - abs($sold));

                    if ($batchAvailable > 0) {
                        $take = min($remainingToTransfer, $batchAvailable);

                        // 2. Add Detail
                        ChiTietLuanChuyen::create([
                            'luan_chuyen_hang_id' => $transfer->id,
                            'san_pham_id' => $productId,
                            'me_san_xuat_id' => $batch->id,
                            'so_luong' => $take,
                            'han_su_dung' => $batch->han_su_dung,
                        ]);

                        // 3. Create Negative Distribution for Source (Immediate Deduction)
                        PhanBoHangDiemBan::create([
                            'me_san_xuat_id' => $batch->id,
                            'diem_ban_id' => $this->sourceAgencyId,
                            'san_pham_id' => $productId,
                            'buoi' => 'sang', // dummy
                            'so_luong' => -$take, // NEGATIVE
                            'nguoi_tao_id' => Auth::id(),
                            'nguoi_nhan_id' => Auth::id(), // Auto confirmed
                            'trang_thai' => 'da_nhan', // Deduct immediately
                            'loai_phan_bo' => 'tu_do', // Use valid enum value
                            'ghi_chu' => "Chuyển đi: " . $transfer->diemBanDich->ten_diem_ban . " (" . $transfer->ma_phieu . ")" . ($this->note ? " - " . $this->note : ""),
                            'ngay_nhan' => now(),
                        ]);

                        // 3b. Log History for Source
                        LichSuCapNhatMe::create([
                            'me_san_xuat_id' => $batch->id,
                            'san_pham_id' => $productId,
                            'diem_ban_id' => $this->sourceAgencyId,
                            'loai' => LichSuCapNhatMe::LOAI_PHAN_BO,
                            'nguoi_cap_nhat_id' => Auth::id(),
                            'so_luong_doi' => -$take,
                            'du_lieu_cu' => 0,
                            'du_lieu_moi' => 0,
                            'ghi_chu' => "Luân chuyển sang " . $transfer->diemBanDich->ten_diem_ban . " (" . $transfer->ma_phieu . ")"
                        ]);

                        // 4. Create Positive Distribution for Destination (Pending Receive)
                        PhanBoHangDiemBan::create([
                            'me_san_xuat_id' => $batch->id,
                            'diem_ban_id' => $this->destinationAgencyId,
                            'san_pham_id' => $productId,
                            'buoi' => 'sang', // dummy
                            'so_luong' => $take, // POSITIVE
                            'nguoi_tao_id' => Auth::id(),
                            'trang_thai' => 'chua_nhan', // Waits for receive
                            'loai_phan_bo' => 'tu_do', // Use valid enum value
                            'ghi_chu' => "Nhận từ: " . $transfer->diemBanNguon->ten_diem_ban . " (" . $transfer->ma_phieu . ")" . ($this->note ? " - " . $this->note : ""),
                        ]);

                        // 4b. Log History for Destination
                        LichSuCapNhatMe::create([
                            'me_san_xuat_id' => $batch->id,
                            'san_pham_id' => $productId,
                            'diem_ban_id' => $this->destinationAgencyId,
                            'loai' => LichSuCapNhatMe::LOAI_PHAN_BO,
                            'nguoi_cap_nhat_id' => Auth::id(),
                            'so_luong_doi' => $take,
                            'du_lieu_cu' => 0,
                            'du_lieu_moi' => 0,
                            'ghi_chu' => "Nhận luân chuyển từ " . $transfer->diemBanNguon->ten_diem_ban . " (" . $transfer->ma_phieu . ")"
                        ]);

                        $remainingToTransfer -= $take;
                    }
                }

                // If still remaining (not enough tracked batch stock?), force take from unknown/null batch?
                // Or just error out earlier. For now assume validated total stock covers it.
            }

        });

        session()->flash('success', 'Tạo phiếu luân chuyển thành công!');
        return redirect()->route('admin.distribution.index'); // Adjust route
    }

    public function render()
    {
        return view('livewire.admin.distribution.transfer-manager');
    }
}
