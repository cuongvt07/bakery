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
    public $pendingDistributions = [];
    public $activeTransfers = [];

    public function checkPendingTransfers($agencyId)
    {
        // 1. Get detailed items (PhanBoHangDiemBan) - used for stock updates
        $this->pendingDistributions = PhanBoHangDiemBan::with(['product'])
            ->where('diem_ban_id', $agencyId)
            ->where('trang_thai', 'chua_nhan')
            ->get();

        // 2. Get high-level transfers (LuanChuyenHang) - used for blocking UI
        $this->activeTransfers = LuanChuyenHang::with(['diemBanNguon', 'chiTiet.sanPham'])
            ->where('diem_ban_dich_id', $agencyId)
            ->where('trang_thai', 'dang_chuyen')
            ->get();

        if ($this->activeTransfers->isNotEmpty()) {
            $this->showReceiveModal = true;
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
                $dist->update([
                    'trang_thai' => 'da_nhan',
                    'nguoi_nhan_id' => Auth::id(),
                    'ngay_nhan' => now(),
                ]);

                // Update Shift Details
                $detail = ChiTietCaLam::firstOrNew([
                    'ca_lam_viec_id' => $this->todayAttendance->id,
                    'san_pham_id' => $dist->san_pham_id,
                ]);

                $detail->so_luong_nhan_ca = ($detail->so_luong_nhan_ca ?? 0) + $dist->so_luong;
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
}
