<?php

namespace App\Livewire\Admin\Shift;

use App\Models\ShiftSchedule;
use App\Models\Agency;
use App\Models\User;
use App\Models\ShiftClosing;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

#[Layout('components.layouts.app')]
class ShiftManagement extends Component
{
    use WithPagination;

    // Tabs
    public $activeTab = 'stores'; // 'stores' | 'workshop'

    // Filters
    public $dateFrom = '';
    public $dateTo = '';
    public $agencyFilter = '';
    public $employeeFilter = '';
    public $statusFilter = '';
    public $search = '';
    
    // View mode
    public $viewMode = 'monitoring'; // 'monitoring' (Grouped) | 'list' (Flat) | 'calendar'

    // Detail Modal
    public $showDetailModal = false;
    public $selectedShift = null;
    
    // Edit Modal
    public $showEditModal = false;
    public $editShiftId = null;
    public $editDate = '';
    public $editStartTime = '';
    public $editEndTime = '';
    public $editStatus = '';
    
    // Bulk actions
    public $selectedShifts = [];
    public $selectAll = false;

    public function mount()
    {
        // Default to This Week + Next Week (to ensure upcoming registered shifts are visible)
        // Adjust to show 2 weeks ahead by default
        $this->dateFrom = Carbon::now()->startOfWeek()->format('Y-m-d');
        $this->dateTo = Carbon::now()->addWeeks(1)->endOfWeek()->format('Y-m-d');
    }

    // Reset pagination when filters change
    public function updatedDateFrom() { $this->resetPage(); }
    public function updatedDateTo() { $this->resetPage(); }
    public function updatedAgencyFilter() { $this->resetPage(); }
    public function updatedEmployeeFilter() { $this->resetPage(); }
    public function updatedStatusFilter() { $this->resetPage(); }
    public function updatedSearch() { $this->resetPage(); }
    public function updatedActiveTab() { $this->resetPage(); $this->agencyFilter = ''; }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function toggleViewMode($mode)
    {
        $this->viewMode = $mode;
    }

    public function openDetail($shiftId)
    {
        $this->selectedShift = ShiftSchedule::with(['agency', 'user'])
            ->findOrFail($shiftId);
        $this->showDetailModal = true;
    }

    public function closeDetail()
    {
        $this->showDetailModal = false;
        $this->selectedShift = null;
    }

    public function openEditModal($shiftId)
    {
        $shift = ShiftSchedule::findOrFail($shiftId);
        $this->editShiftId = $shift->id;
        $this->editDate = Carbon::parse($shift->ngay_lam)->format('Y-m-d');
        $this->editStartTime = $shift->gio_bat_dau;
        $this->editEndTime = $shift->gio_ket_thuc;
        $this->editStatus = $shift->trang_thai;
        $this->showEditModal = true;
    }

    public function saveEdit()
    {
        $this->validate([
            'editDate' => 'required|date',
            'editStartTime' => 'required',
            'editEndTime' => 'required|after:editStartTime',
            'editStatus' => 'required|in:pending,approved,rejected,completed',
        ]);

        $shift = ShiftSchedule::findOrFail($this->editShiftId);
        $shift->update([
            'ngay_lam' => $this->editDate,
            'gio_bat_dau' => $this->editStartTime,
            'gio_ket_thuc' => $this->editEndTime,
            'trang_thai' => $this->editStatus,
        ]);

        session()->flash('message', 'Đã cập nhật ca làm việc');
        $this->closeEditModal();
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->reset(['editShiftId', 'editDate', 'editStartTime', 'editEndTime', 'editStatus']);
    }

    public function deleteShift($shiftId)
    {
        ShiftSchedule::findOrFail($shiftId)->delete();
        session()->flash('message', 'Đã xóa ca làm việc');
    }

    public function toggleSelectAll()
    {
        if ($this->selectAll) {
            // Only select shifts visible in current query context would be ideal
            // But for simplicity in Livewire without full state sync, we might just clear or select all filtered
            // For now, let's keep it simple: Select loaded IDs (tricky with pagination)
            // Or just reset
            $this->selectedShifts = [];
        } else {
            $this->selectedShifts = [];
        }
    }

    public function bulkDelete()
    {
        if (empty($this->selectedShifts)) {
            session()->flash('error', 'Chưa chọn ca nào');
            return;
        }

        ShiftSchedule::whereIn('id', $this->selectedShifts)->delete();
        session()->flash('message', 'Đã xóa ' . count($this->selectedShifts) . ' ca làm việc');
        $this->selectedShifts = [];
        $this->selectAll = false;
    }

    public function bulkUpdateStatus($status)
    {
        if (empty($this->selectedShifts)) {
            session()->flash('error', 'Chưa chọn ca nào');
            return;
        }

        ShiftSchedule::whereIn('id', $this->selectedShifts)->update(['trang_thai' => $status]);
        session()->flash('message', 'Đã cập nhật ' . count($this->selectedShifts) . ' ca làm việc');
        $this->selectedShifts = [];
        $this->selectAll = false;
    }

    public function exportExcel()
    {
        session()->flash('message', 'Tính năng xuất Excel đang được phát triển');
    }

    public function render()
    {
        // 1. Determine Agency IDs for current Tab
        $agencyQuery = Agency::query();
        
        if ($this->activeTab === 'workshop') {
            $agencyQuery->where('ma_diem_ban', 'like', 'XUONG%')
                        ->orWhere('ten_diem_ban', 'like', '%Xưởng%');
        } else {
            // Stores: Not starting with XUONG and not containing Xưởng
            $agencyQuery->where(function($q) {
                $q->where('ma_diem_ban', 'not like', 'XUONG%')
                  ->where('ten_diem_ban', 'not like', '%Xưởng%');
            });
        }
        
        $agencyIds = $agencyQuery->pluck('id')->toArray();

        // 2. Prepare Shared Stats
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
        
        // 3. Render based on Mode
        if ($this->viewMode === 'monitoring') {
            // Grouped by Agency
            $agencies = $agencyQuery->with([
                'shiftTemplates' => function($q) {
                    $q->where('is_active', true)
                      ->orderBy('start_time');
                },
                'shiftSchedules' => function($q) {
                    // Eager load everything needed for the view
                    $q->with(['user.diemBan'])
                      ->whereBetween('ngay_lam', [$this->dateFrom, $this->dateTo])
                      ->orderBy('ngay_lam')
                      ->orderBy('gio_bat_dau');
                      
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
            
            return view('livewire.admin.shift.shift-management', [
                'groupedAgencies' => $agencies,
                'shifts' => null, // Not used in monitoring mode
                'agencies' => Agency::where('ten_diem_ban', 'not like', '%Xưởng%')->orderBy('ten_diem_ban')->get(), // For filter dropdown
                'employees' => User::where('vai_tro', 'nhan_vien')->orderBy('ho_ten')->get(),
                'stats' => $stats,
            ]);
            
        } else {
            // List View (Flat)
            $query = ShiftSchedule::with(['agency', 'user'])
                ->whereIn('diem_ban_id', $agencyIds); // Filter by Tab

            // Apply filters
            if ($this->dateFrom) $query->whereDate('ngay_lam', '>=', $this->dateFrom);
            if ($this->dateTo) $query->whereDate('ngay_lam', '<=', $this->dateTo);
            if ($this->agencyFilter) $query->where('diem_ban_id', $this->agencyFilter);
            if ($this->employeeFilter) $query->where('nguoi_dung_id', $this->employeeFilter);
            if ($this->statusFilter) $query->where('trang_thai', $this->statusFilter);
            if ($this->search) {
                $query->whereHas('user', function($q) {
                    $q->where('ho_ten', 'like', '%' . $this->search . '%')
                      ->orWhere('ma_nhan_vien', 'like', '%' . $this->search . '%');
                });
            }

            $shifts = $query->orderBy('ngay_lam', 'desc')
                           ->orderBy('gio_bat_dau', 'desc')
                           ->paginate(20);

            return view('livewire.admin.shift.shift-management', [
                'shifts' => $shifts,
                'groupedAgencies' => null,
                'agencies' => Agency::where('ten_diem_ban', 'not like', '%Xưởng%')->orderBy('ten_diem_ban')->get(),
                'employees' => User::where('vai_tro', 'nhan_vien')->orderBy('ho_ten')->get(),
                'stats' => $stats,
            ]);
        }
    }
}
