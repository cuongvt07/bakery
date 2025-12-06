<?php

namespace App\Livewire\Admin\Distribution;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\PhanBoHangDiemBan;
use App\Models\ProductionBatch;
use Carbon\Carbon;

#[Layout('components.layouts.app')]
class DistributionList extends Component
{
    use WithPagination;

    public $dateFilter;
    public $selectedBatchId;

    public function mount()
    {
        // Default to today
        $this->dateFilter = Carbon::today()->format('Y-m-d');
    }

    public function delete($id)
    {
        $distribution = PhanBoHangDiemBan::find($id);
        
        if (!$distribution) {
            return;
        }

        // Check if already received
        if ($distribution->trang_thai === 'da_nhan') {
            session()->flash('error', 'Không thể xóa phân bổ đã được nhận hàng!');
            return;
        }

        $distribution->delete();
        session()->flash('success', 'Đã xóa phân bổ thành công!');
    }

    public function render()
    {
        // Get production batches for the selected date
        $batchesQuery = ProductionBatch::with(['details.product', 'distributions.diemBan', 'distributions.product'])
            ->where('trang_thai', 'hoan_thanh')
            ->orderBy('ngay_san_xuat', 'desc')
            ->orderBy('buoi', 'desc');

        if ($this->dateFilter) {
            $batchesQuery->whereDate('ngay_san_xuat', $this->dateFilter);
        }

        $batches = $batchesQuery->get();

        // Get all distributions with details
        $distributionsQuery = PhanBoHangDiemBan::with(['productionBatch', 'diemBan', 'product'])
            ->orderBy('created_at', 'desc');

        if ($this->dateFilter) {
            $distributionsQuery->whereHas('productionBatch', function($q) {
                $q->whereDate('ngay_san_xuat', $this->dateFilter);
            });
        }

        if ($this->selectedBatchId) {
            $distributionsQuery->where('me_san_xuat_id', $this->selectedBatchId);
        }

        $distributions = $distributionsQuery->paginate(20);

        return view('livewire.admin.distribution.distribution-list', [
            'batches' => $batches,
            'distributions' => $distributions
        ]);
    }
}
