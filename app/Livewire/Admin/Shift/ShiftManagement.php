<?php

namespace App\Livewire\Admin\Shift;

use App\Models\CaLamViec;
use App\Models\Agency;
use App\Models\User;
use App\Models\PhieuChotCa;
use App\Models\ChiTietCaLam;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Carbon\Carbon;

#[Layout('components.layouts.app')]
class ShiftManagement extends Component
{
    use WithPagination;

    // Filters
    public $dateFrom = '';
    public $dateTo = '';
    public $agencyFilter = '';
    public $statusFilter = ''; // 'active', 'closed', 'not_checked_in'
    public $search = '';

    // Detail Modal
    public $showDetailModal = false;
    public $selectedShift = null;

    public function mount()
    {
        // Default to today's date
        $this->dateFrom = Carbon::today()->format('Y-m-d');
        $this->dateTo = Carbon::today()->format('Y-m-d');
    }

    // Reset pagination when filters change
    public function updatedDateFrom() { $this->resetPage(); }
    public function updatedDateTo() { $this->resetPage(); }
    public function updatedAgencyFilter() { $this->resetPage(); }
    public function updatedStatusFilter() { $this->resetPage(); }
    public function updatedSearch() { $this->resetPage(); }

    public function openDetail($shiftId)
    {
        $this->selectedShift = CaLamViec::with(['diemBan', 'nguoiDung', 'chiTietCaLam.sanPham', 'phieuChotCa'])
            ->findOrFail($shiftId);
        $this->showDetailModal = true;
    }

    public function closeDetail()
    {
        $this->showDetailModal = false;
        $this->selectedShift = null;
    }

    public function render()
    {
        $query = CaLamViec::with(['diemBan', 'nguoiDung', 'phieuChotCa']);

        // Apply date filter
        if ($this->dateFrom) {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }

        // Apply agency filter
        if ($this->agencyFilter) {
            $query->where('diem_ban_id', $this->agencyFilter);
        }

        // Apply status filter
        if ($this->statusFilter === 'active') {
            $query->where('trang_thai', 'dang_lam');
        } elseif ($this->statusFilter === 'closed') {
            $query->where('trang_thai', 'da_ket_thuc');
        } elseif ($this->statusFilter === 'not_checked_in') {
            $query->where('trang_thai_checkin', false);
        }

        // Apply search
        if ($this->search) {
            $query->where(function($q) {
                $q->whereHas('nguoiDung', function($q) {
                    $q->where('ho_ten', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('diemBan', function($q) {
                    $q->where('ten_diem_ban', 'like', '%' . $this->search . '%');
                });
            });
        }

        $shifts = $query->orderBy('created_at', 'desc')
                       ->orderBy('gio_bat_dau', 'desc')
                       ->paginate(20);

        $agencies = Agency::orderBy('ten_diem_ban')->get();

        // Calculate summary statistics
        $activeShifts = CaLamViec::where('trang_thai', 'dang_lam')->count();
        $notCheckedIn = CaLamViec::where('trang_thai', 'dang_lam')
                                  ->where('trang_thai_checkin', false)
                                  ->count();
        $notClosed = CaLamViec::whereDate('created_at', Carbon::today())
                              ->where('trang_thai', 'dang_lam')
                              ->count();

        return view('livewire.admin.shift.shift-management', [
            'shifts' => $shifts,
            'agencies' => $agencies,
            'activeShifts' => $activeShifts,
            'notCheckedIn' => $notCheckedIn,
            'notClosed' => $notClosed,
        ]);
    }
}
