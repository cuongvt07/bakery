<?php

namespace App\Livewire\Admin\Shift;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\PhieuChotCa;
use App\Models\Agency;
use Carbon\Carbon;

class ShiftReportList extends Component
{
    use WithPagination;

    public $dateFrom;
    public $dateTo;
    public $agencyId;
    
    public $selectedReport = null;
    public $showDetailModal = false;

    public function mount()
    {
        $this->dateFrom = Carbon::today()->toDateString();
        $this->dateTo = Carbon::today()->toDateString();
    }

    public function render()
    {
        $query = PhieuChotCa::with(['diemBan', 'nguoiChot', 'caLamViec', 'caLamViec.shiftTemplate'])
            ->orderBy('created_at', 'desc');

        if ($this->agencyId) {
            $query->where('diem_ban_id', $this->agencyId);
        }

        if ($this->dateFrom) {
            $query->whereDate('ngay_chot', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('ngay_chot', '<=', $this->dateTo);
        }

        $reports = $query->paginate(10);
        $agencies = Agency::all();

        return view('livewire.admin.shift.shift-report-list', [
            'reports' => $reports,
            'agencies' => $agencies
        ])->layout('components.layouts.app');
    }

    public function viewDetail($id)
    {
        $this->selectedReport = PhieuChotCa::with(['diemBan', 'nguoiChot', 'chiTietCaLam' => function($q) {
                // PhieuChotCa doesn't have direct chiTietCaLam relation usually, it's on CaLamViec
                // But we can get it via caLamViec relation
            }, 'caLamViec.chiTietCaLam.sanPham'])
            ->findOrFail($id);
            
        $this->showDetailModal = true;
    }

    public function closeDetail()
    {
        $this->showDetailModal = false;
        $this->selectedReport = null;
    }
}
