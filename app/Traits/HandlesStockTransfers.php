<?php

namespace App\Traits;

use App\Models\LuanChuyenHang;
use App\Models\PhanBoHangDiemBan;
use App\Models\ChiTietCaLam;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait HandlesStockTransfers
{
    public $showReceiveModal = false;
    public $showSourceRefreshModal = false;
    public $pendingDistributions = [];
    public $activeTransfers = [];
    public $activeSourceTransfers = [];

    public function checkPendingTransfers($agencyId)
    {
        // 1. Get detailed items (PhanBoHangDiemBan) - used for stock updates
        $this->pendingDistributions = PhanBoHangDiemBan::with(['product'])
            ->where('diem_ban_id', $agencyId)
            ->where('trang_thai', 'chua_nhan')
            ->get();

        // 2. Get high-level transfers (LuanChuyenHang) - used for blocking UI (Receiver side)
        $this->activeTransfers = LuanChuyenHang::with(['diemBanNguon', 'chiTiet.sanPham'])
            ->where('diem_ban_dich_id', $agencyId)
            ->where('trang_thai', 'dang_chuyen')
            ->get();

        if ($this->activeTransfers->isNotEmpty()) {
            $this->showReceiveModal = true;
        }

        // 3. Check for recently initiated transfers where this agency is the SOURCE (Sender side)
        // We use session to keep track of seen/dismissed transfers to avoid DB schema changes
        $dismissed = session()->get('dismissed_source_transfers', []);

        $this->activeSourceTransfers = LuanChuyenHang::where('diem_ban_nguon_id', $agencyId)
            ->where('trang_thai', 'dang_chuyen')
            ->whereNotIn('id', $dismissed)
            ->where('created_at', '>=', now()->subHours(1)) // Only check last hour to keep it relevant
            ->get();

        if ($this->activeSourceTransfers->isNotEmpty()) {
            $this->showSourceRefreshModal = true;
        }
    }

    public function confirmReceiveStock()
    {
        if (!$this->todayAttendance) {
            $this->dispatch('show-alert', ['type' => 'error', 'message' => 'Bạn không trong ca làm việc!']);
            return;
        }

        DB::transaction(function () {
            // Update individual items
            foreach ($this->pendingDistributions as $dist) {
                // Re-fetch with lock to prevent race conditions
                $item = PhanBoHangDiemBan::where('id', $dist->id)->lockForUpdate()->find($dist->id);

                // Skip if initialized or already processed by another request
                if (!$item || $item->trang_thai !== 'chua_nhan') {
                    continue;
                }

                $item->update([
                    'trang_thai' => 'da_nhan',
                    'nguoi_nhan_id' => Auth::id(),
                    'ngay_nhan' => now(),
                ]);

                // Update Shift Details
                $detail = ChiTietCaLam::firstOrNew([
                    'ca_lam_viec_id' => $this->todayAttendance->id,
                    'san_pham_id' => $item->san_pham_id,
                ]);

                $detail->so_luong_nhan_ca = ($detail->so_luong_nhan_ca ?? 0) + $item->so_luong;
                $detail->save();
            }

            // Update matching Transfers
            foreach ($this->activeTransfers as $transfer) {
                $transfer->update([
                    'trang_thai' => 'da_nhan',
                    'nguoi_nhan_id' => Auth::id(),
                    'ngay_nhan' => now(),
                ]);
            }
        });

        $this->showReceiveModal = false;
        $this->pendingDistributions = [];
        $this->activeTransfers = [];

        // Notify of success
        session()->flash('success', 'Đã xác nhận nhận hàng luân chuyển!');

        // Dispatch event for components that need to refresh (like QuickSale)
        $this->dispatch('inventory-updated');

        if (method_exists($this, 'loadShiftProducts')) {
            $this->loadShiftProducts();
        }
    }

    public function confirmSourceRefresh()
    {
        $dismissed = session()->get('dismissed_source_transfers', []);

        foreach ($this->activeSourceTransfers as $transfer) {
            if (!in_array($transfer->id, $dismissed)) {
                $dismissed[] = $transfer->id;
            }
        }

        session()->put('dismissed_source_transfers', $dismissed);
        $this->showSourceRefreshModal = false;

        // Full page reload to ensure all stock data (POS, counters) are refreshed
        return redirect(request()->header('Referer') ?: route('employee.pos'));
    }
}
