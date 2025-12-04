<?php

namespace App\Livewire\Admin\Distribution;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\PhieuXuatHangTong;
use Carbon\Carbon;

#[Layout('components.layouts.app')]
class DistributionList extends Component
{
    use WithPagination;

    public $dateFilter;

    public function mount()
    {
        // Default to no filter (show all) or maybe current month?
        // Let's leave it empty to show all history by default
    }

    public function delete($id)
    {
        $phieu = PhieuXuatHangTong::find($id);
        
        if (!$phieu) {
            return;
        }

        // Check if distributed
        if ($phieu->phanBo()->exists()) {
            session()->flash('error', 'Không thể xóa mẻ hàng đã được phân bổ cho điểm bán!');
            return;
        }

        $phieu->delete();
        session()->flash('success', 'Đã xóa mẻ hàng thành công!');
    }

    public function render()
    {
        // 1. Get Distinct Dates (Paginated)
        $dateQuery = PhieuXuatHangTong::select('ngay_xuat')
            ->distinct()
            ->orderBy('ngay_xuat', 'desc');

        if ($this->dateFilter) {
            $dateQuery->whereDate('ngay_xuat', $this->dateFilter);
        }

        $dates = $dateQuery->paginate(10);

        // 2. Get Batches for these dates
        $dateStrings = $dates->pluck('ngay_xuat');
        
        $batches = PhieuXuatHangTong::whereIn('ngay_xuat', $dateStrings)
            ->with('nguoiXuat')
            ->withCount('phanBo')
            ->orderBy('ngay_xuat', 'desc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function($item) {
                return $item->ngay_xuat; // Group by date string
            });

        return view('livewire.admin.distribution.distribution-list', [
            'dates' => $dates,
            'groupedBatches' => $batches
        ]);
    }
}
