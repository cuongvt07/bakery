<?php

namespace App\Livewire\Admin\Shift;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\CaLamViec;
use App\Models\PendingSale;
use App\Models\BatchBanHang;
use Illuminate\Support\Facades\Auth;


class PendingSalesList extends Component
{
    public $shift;
    public $pendingSales = [];
    public $selected = [];
    public $selectAll = false;
    
    // For cumulative cash calculation
    public $openingCash = 0;
    public $confirmedCashTotal = 0;

    public function mount()
    {
        // Check if user has active shift
        $this->shift = CaLamViec::where('nguoi_dung_id', Auth::id())
            ->where('trang_thai', 'dang_lam')
            ->first();

        if (!$this->shift) {
            session()->flash('error', 'Bạn chưa có ca làm việc nào!');
            return redirect()->route('admin.shift.check-in');
        }
        
        // Calculate opening cash
        $this->openingCash = $this->shift->tien_mat_dau_ca ?? 0;
        
        // Calculate total confirmed cash sales (already confirmed)
        $this->confirmedCashTotal = PendingSale::where('ca_lam_viec_id', $this->shift->id)
            ->where('trang_thai', 'confirmed')
            ->where('phuong_thuc_thanh_toan', 'tien_mat')
            ->sum('tong_tien');

        $this->loadPendingSales();
    }

    public function loadPendingSales()
    {
        $this->pendingSales = PendingSale::where('ca_lam_viec_id', $this->shift->id)
            ->where('trang_thai', 'pending')
            ->orderBy('created_at', 'asc')
            ->get()
            ->toArray();
    }

    public function toggleSelect($saleId)
    {
        if (in_array($saleId, $this->selected)) {
            $this->selected = array_diff($this->selected, [$saleId]);
        } else {
            $this->selected[] = $saleId;
        }

        $this->selectAll = count($this->selected) === count($this->pendingSales);
    }

    public function toggleSelectAll()
    {
        if ($this->selectAll) {
            $this->selected = array_column($this->pendingSales, 'id');
        } else {
            $this->selected = [];
        }
    }

    public function updatedSelectAll()
    {
        $this->toggleSelectAll();
    }

    public function getSelectedTotal()
    {
        $total = 0;
        foreach ($this->pendingSales as $sale) {
            if (in_array($sale['id'], $this->selected)) {
                $total += $sale['tong_tien'];
            }
        }
        return $total;
    }

    public function getSelectedCount()
    {
        return count($this->selected);
    }

    public function confirmBatch()
    {
        if (empty($this->selected)) {
            $this->dispatch('show-alert', [
                'type' => 'warning',
                'message' => 'Vui lòng chọn ít nhất 1 đơn hàng!'
            ]);
            return;
        }

        try {
            // Create batch from selected pending sales
            BatchBanHang::createFromPending($this->selected, Auth::id());

            $this->dispatch('show-alert', [
                'type' => 'success',
                'message' => 'Đã chốt ' . count($this->selected) . ' đơn hàng!'
            ]);
            
            // Dispatch event to refresh inventory in QuickSale
            $this->dispatch('inventory-updated');

            // Reset selection
            $this->selected = [];
            $this->selectAll = false;

            // Reload
            $this->loadPendingSales();

        } catch (\Exception $e) {
            $this->dispatch('show-alert', [
                'type' => 'error',
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ]);
        }
    }
    
    // Confirm ALL pending sales at once (no selection)
    public function confirmAll()
    {
        try {
            $allIds = array_column($this->pendingSales, 'id');
            
            if (empty($allIds)) {
                session()->flash('warning', 'Không có đơn hàng nào để chốt!');
                return;
            }
            
            // Create batch from all pending sales
            BatchBanHang::createFromPending($allIds, Auth::id());

            session()->flash('success', 'Đã chốt tất cả ' . count($allIds) . ' đơn hàng!');
            
            // Dispatch event to refresh inventory (will execute on redirect target page)
            $this->dispatch('inventory-updated');

            // Redirect back to POS to continue selling
            $posRoute = (Auth::user()->vai_tro === 'nhan_vien') 
                ? route('employee.pos') 
                : route('admin.pos.quick-sale');
            return $this->redirect($posRoute, navigate: true);

        } catch (\Exception $e) {
            session()->flash('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function deleteSale($saleId)
    {
        try {
            $sale = PendingSale::find($saleId);
            if ($sale && $sale->ca_lam_viec_id == $this->shift->id) {
                // NOTE: Do NOT restore inventory here!
                // Pending sales are not counted in so_luong_ban anymore
                // Inventory is only updated when batch is confirmed
                
                $sale->update(['trang_thai' => 'cancelled']);
                
                session()->flash('success', 'Đã xóa đơn hàng!');

                // Remove from selected if it was selected
                $this->selected = array_diff($this->selected, [$saleId]);
                
                $this->loadPendingSales();
            } else {
                session()->flash('error', 'Không tìm thấy đơn hàng hoặc đơn hàng không thuộc ca của bạn!');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function deleteSelected()
    {
        if (empty($this->selected)) {
            session()->flash('warning', 'Vui lòng chọn ít nhất 1 đơn hàng!');
            return;
        }

        try {
            $count = count($this->selected);
            
            // NOTE: Do NOT restore inventory!
            // Pending sales don't affect so_luong_ban until confirmed
            
            // Mark as cancelled
            PendingSale::whereIn('id', $this->selected)
                ->where('ca_lam_viec_id', $this->shift->id)
                ->update(['trang_thai' => 'cancelled']);

            session()->flash('success', "Đã xóa $count đơn hàng!");

            $this->selected = [];
            $this->selectAll = false;
            $this->loadPendingSales();

        } catch (\Exception $e) {
            session()->flash('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
    
    // Alias for deleteSale
    public function delete($saleId)
    {
        $this->deleteSale($saleId);
    }
    
    // Alias for confirmBatch
    public function confirmSelected()
    {
        $this->confirmBatch();
    }

    public function render()
    {
        $layout = (Auth::user() && Auth::user()->vai_tro === 'nhan_vien') 
            ? 'components.layouts.mobile' 
            : 'components.layouts.app';
            
        return view('livewire.admin.shift.pending-sales-list', [
            'selectedTotal' => $this->getSelectedTotal(),
            'selectedCount' => $this->getSelectedCount(),
        ])->layout($layout);
    }
}
