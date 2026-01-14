<?php

namespace App\Livewire\Employee\Shift;

use App\Models\ShiftSchedule;
use App\Models\Agency;
use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Carbon\Carbon;

#[Layout('components.layouts.mobile')]
class EmployeeSystemSchedule extends Component
{
    // Filters
    public $dateFrom = '';
    public $dateTo = '';
    public $agencyFilter = '';
    public $employeeFilter = '';
    public $statusFilter = '';
    public $search = '';
    
    // Detail Modal (Read-only)
    public $showDetailModal = false;
    public $selectedShift = null;

    public function mount()
    {
        // Default to This Week + Next Week
        $this->dateFrom = Carbon::now()->startOfWeek()->format('Y-m-d');
        $this->dateTo = Carbon::now()->addWeeks(1)->endOfWeek()->format('Y-m-d');
    }

    // Reset pagination when filters change
    public function updatedDateFrom() { }
    public function updatedDateTo() { }
    public function updatedAgencyFilter() { }
    public function updatedEmployeeFilter() { }
    public function updatedStatusFilter() { }
    public function updatedSearch() { }

    public function openDetail($shiftId)
    {
        // First get the ShiftSchedule to know which shift to find
        $shiftSchedule = ShiftSchedule::findOrFail($shiftId);
        
        // Find the corresponding CaLamViec (actual shift work)
        $this->selectedShift = \App\Models\CaLamViec::with(['diemBan', 'nguoiDung', 'phieuChotCa'])
            ->where('nguoi_dung_id', $shiftSchedule->nguoi_dung_id)
            ->whereDate('ngay_lam', $shiftSchedule->ngay_lam)
            ->where('diem_ban_id', $shiftSchedule->diem_ban_id)
            ->first();
        
        // If no CaLamViec found, use ShiftSchedule data
        if (!$this->selectedShift) {
            $this->selectedShift = $shiftSchedule->load(['agency', 'user']);
        }
        
        $this->showDetailModal = true;
    }

    public function closeDetail()
    {
        $this->showDetailModal = false;
        $this->selectedShift = null;
    }

    public function render()
    {
        // Get all agencies
        $agencyQuery = Agency::query();
        $agencyIds = $agencyQuery->pluck('id')->toArray();

        // Prepare Stats
        $stats = [
            'total' => ShiftSchedule::whereIn('diem_ban_id', $agencyIds)
                ->whereBetween('ngay_lam', [$this->dateFrom, $this->dateTo])->count(),
            'approved' => ShiftSchedule::whereIn('diem_ban_id', $agencyIds)
                ->where('trang_thai', 'approved')
                ->whereBetween('ngay_lam', [$this->dateFrom, $this->dateTo])
                ->count(),
            'completed' => ShiftSchedule::whereIn('diem_ban_id', $agencyIds)
                ->where('trang_thai', 'completed')
                ->whereBetween('ngay_lam', [$this->dateFrom, $this->dateTo])
                ->count(),
            'pending' => ShiftSchedule::whereIn('diem_ban_id', $agencyIds)
                ->where('trang_thai', 'pending')
                ->whereBetween('ngay_lam', [$this->dateFrom, $this->dateTo])
                ->count(),
        ];
        
        // Monitoring View: Grouped by Agency
        $agencies = $agencyQuery->with([
            'shiftTemplates' => function($q) {
                $q->where('is_active', true)
                  ->orderBy('start_time');
            },
            'shiftSchedules' => function($q) {
                // Eager load everything needed for the view
                $q->with(['user.diemBan', 'user.department'])
                  ->whereBetween('ngay_lam', [$this->dateFrom, $this->dateTo]);
                  
                // Apply internal filters
                if ($this->employeeFilter) $q->where('nguoi_dung_id', $this->employeeFilter);
                if ($this->statusFilter) $q->where('trang_thai', $this->statusFilter);
                if ($this->search) {
                     $q->whereHas('user', function($sq) {
                        $sq->where('ho_ten', 'like', '%' . $this->search . '%');
                     });
                }
            }
        ])->orderBy('ten_diem_ban')->get();
        
        // Sort shifts within each agency by department and name
        foreach ($agencies as $agency) {
            $agency->shiftSchedules = $agency->shiftSchedules->sortBy(function($shift) {
                $date = $shift->ngay_lam->format('Y-m-d');
                $time = $shift->gio_bat_dau instanceof \Carbon\Carbon ? $shift->gio_bat_dau->format('H:i') : $shift->gio_bat_dau;
                $deptId = str_pad($shift->user->phong_ban_id ?? 99999, 10, '0', STR_PAD_LEFT);
                $name = $shift->user->ho_ten;
                
                return $date . '|' . $time . '|' . $deptId . '|' . $name;
            })->values();
        }

        return view('livewire.employee.shift.employee-system-schedule', [
            'groupedAgencies' => $agencies,
            'agencies' => Agency::with('shiftTemplates')->orderBy('ten_diem_ban')->get(),
            'employees' => User::where('vai_tro', 'nhan_vien')->orderBy('ho_ten')->get(),
            'stats' => $stats,
        ]);
    }
}
