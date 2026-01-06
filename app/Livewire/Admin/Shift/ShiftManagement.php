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
    
    // Edit Modal (Old - kept for compatibility)
    public $showEditModal = false;
    public $editShiftId = null;
    
    // Edit Mode (New - for detail modal)
    public $editingShift = false;
    public $editAgencyId = null;
    public $editShiftTemplateId = null;
    public $editDate = '';
    public $editStartTime = '';
    public $editEndTime = '';
    public $editStatus = '';
    
    // Delete confirmation
    public $showDeleteConfirm = false;
    public $deleteNote = '';
    
    // Add Shift Modal
    public $showAddShiftModal = false;
    public $addShiftAgencyId = null;
    public $addShiftAgencyName = '';
    public $addShiftTemplates = []; // Changed to array of templates
    public $selectedTemplates = []; // Selected template IDs
    public $addShiftDate = '';
    public $addShiftEmployeeId = null;
    
    // Employee search
    public $employeeSearch = '';
    public $selectedEmployeeName = '';
    public $selectedEmployeeCode = '';
    
    // Template Manager
    public $showTemplateManager = false;
    public $editingTemplateId = null;
    public $templateAgencyId = null;
    public $templateName = '';
    public $templateStartTime = '';
    public $templateEndTime = '';
    public $templateIsActive = true;
    
    // Bulk actions
    public $selectedShifts = [];
    public $selectAll = false;
    
    // Polling: Track previous shift count to detect new shifts
    public $previousShiftCount = 0;

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
        $this->editingShift = false;
        $this->resetEditFields();
    }
    
    // New Edit/Delete Methods
    public function startEdit()
    {
        $this->editingShift = true;
        $this->editAgencyId = $this->selectedShift->diem_ban_id;
        $this->editShiftTemplateId = $this->selectedShift->shift_template_id;
        $this->editDate = Carbon::parse($this->selectedShift->ngay_lam)->format('Y-m-d');
        $this->editStatus = $this->selectedShift->trang_thai;
    }
    
    public function cancelEdit()
    {
        $this->editingShift = false;
        $this->resetEditFields();
    }
    
    private function resetEditFields()
    {
        $this->editAgencyId = null;
        $this->editShiftTemplateId = null;
        $this->editDate = '';
        $this->editStatus = '';
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
        if ($this->editingShift) {
            // New edit mode (from detail modal)
            $this->validate([
                'editAgencyId' => 'required|exists:diem_ban,id',
                'editShiftTemplateId' => 'required|exists:shift_templates,id',
                'editDate' => 'required|date',
                'editStatus' => 'required|in:approved,completed,pending,rejected',
            ]);

            $template = \App\Models\ShiftTemplate::find($this->editShiftTemplateId);
            
            $this->selectedShift->update([
                'diem_ban_id' => $this->editAgencyId,
                'shift_template_id' => $this->editShiftTemplateId,
                'ngay_lam' => $this->editDate,
                'gio_bat_dau' => $template->start_time,
                'gio_ket_thuc' => $template->end_time,
                'trang_thai' => $this->editStatus,
            ]);

            session()->flash('message', 'Cập nhật ca làm việc thành công!');
            
            $this->editingShift = false;
            $this->resetEditFields();
            $this->closeDetail();
        } else {
            // Old edit mode (from edit modal)
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
    
    public function confirmDeleteShift()
    {
        $this->showDeleteConfirm = true;
    }
    
    public function cancelDelete()
    {
        $this->showDeleteConfirm = false;
        $this->deleteNote = '';
    }
    
    public function executeDelete()
    {
        $this->selectedShift->update([
            'trang_thai' => 'rejected',
            'ghi_chu' => $this->deleteNote ?: 'Đã xóa bởi admin',
        ]);

        session()->flash('message', 'Đã xóa ca làm việc (chuyển sang trạng thái Từ chối)');
        
        $this->showDeleteConfirm = false;
        $this->deleteNote = '';
        $this->closeDetail();
    }
    
    // Add Shift Methods
    public function openAddShiftModal($agencyId, $templateId, $date)
    {
        $agency = Agency::with('shiftTemplates')->find($agencyId);
        
        $this->addShiftAgencyId = $agencyId;
        $this->addShiftAgencyName = $agency->ten_diem_ban ?? '';
        $this->addShiftTemplates = $agency->shiftTemplates->where('is_active', true)->sortBy('start_time');
        $this->selectedTemplates = [$templateId]; // Pre-select the clicked template
        $this->addShiftDate = $date;
        $this->addShiftEmployeeId = null;
        $this->showAddShiftModal = true;
    }
    
    public function closeAddShiftModal()
    {
        $this->showAddShiftModal = false;
        $this->resetAddShiftFields();
    }
    
    private function resetAddShiftFields()
    {
        $this->addShiftAgencyId = null;
        $this->addShiftAgencyName = '';
        $this->addShiftTemplates = [];
        $this->selectedTemplates = [];
        $this->addShiftDate = '';
        $this->addShiftEmployeeId = null;
        $this->employeeSearch = '';
        $this->selectedEmployeeName = '';
        $this->selectedEmployeeCode = '';
    }
    
    public function selectEmployee($employeeId)
    {
        $employee = User::find($employeeId);
        if ($employee) {
            $this->addShiftEmployeeId = $employeeId;
            $this->selectedEmployeeName = $employee->ho_ten;
            $this->selectedEmployeeCode = $employee->ma_nhan_vien;
            $this->employeeSearch = $employee->ho_ten;
        }
    }
    
    public function clearEmployee()
    {
        $this->addShiftEmployeeId = null;
        $this->selectedEmployeeName = '';
        $this->selectedEmployeeCode = '';
        $this->employeeSearch = '';
    }
    
    public function saveAddShift()
    {
        $this->validate([
            'addShiftEmployeeId' => 'required|exists:nguoi_dung,id',
            'selectedTemplates' => 'required|array|min:1',
            'selectedTemplates.*' => 'exists:shift_templates,id',
        ]);
        
        $employee = User::find($this->addShiftEmployeeId);
        $createdCount = 0;
        
        foreach ($this->selectedTemplates as $templateId) {
            $template = \App\Models\ShiftTemplate::find($templateId);
            
            // Check if shift already exists
            $exists = ShiftSchedule::where('nguoi_dung_id', $this->addShiftEmployeeId)
                ->where('diem_ban_id', $this->addShiftAgencyId)
                ->where('shift_template_id', $templateId)
                ->where('ngay_lam', $this->addShiftDate)
                ->exists();
            
            if (!$exists) {
                ShiftSchedule::create([
                    'nguoi_dung_id' => $this->addShiftEmployeeId,
                    'diem_ban_id' => $this->addShiftAgencyId,
                    'shift_template_id' => $templateId,
                    'ngay_lam' => $this->addShiftDate,
                    'gio_bat_dau' => $template->start_time,
                    'gio_ket_thuc' => $template->end_time,
                    'trang_thai' => 'approved',
                    'ghi_chu' => 'Admin tạo ca làm cho nv ' . ($employee->ho_ten ?? ''),
                ]);
                $createdCount++;
            }
        }
        
        if ($createdCount > 0) {
            session()->flash('message', "Đã tạo {$createdCount} ca làm việc cho " . ($employee->ho_ten ?? ''));
        } else {
            session()->flash('error', 'Các ca đã tồn tại, không tạo ca mới');
        }
        
        $this->closeAddShiftModal();
    }
    
    // Template Manager Methods
    public function openTemplateManager()
    {
        $this->showTemplateManager = true;
    }
    
    public function closeTemplateManager()
    {
        $this->showTemplateManager = false;
        $this->resetTemplateForm();
    }
    
    private function resetTemplateForm()
    {
        $this->editingTemplateId = null;
        $this->templateAgencyId = null;
        $this->templateName = '';
        $this->templateStartTime = '';
        $this->templateEndTime = '';
        $this->templateIsActive = true;
    }
    
    public function saveTemplate()
    {
        $this->validate([
            'templateAgencyId' => 'required|exists:diem_ban,id',
            'templateName' => 'required|string|max:100',
            'templateStartTime' => 'required',
            'templateEndTime' => 'required|after:templateStartTime',
        ]);
        
        $data = [
            'diem_ban_id' => $this->templateAgencyId,
            'name' => $this->templateName,
            'start_time' => $this->templateStartTime,
            'end_time' => $this->templateEndTime,
            'is_active' => $this->templateIsActive,
        ];
        
        if ($this->editingTemplateId) {
            \App\Models\ShiftTemplate::find($this->editingTemplateId)->update($data);
            session()->flash('message', 'Đã cập nhật mẫu ca');
        } else {
            \App\Models\ShiftTemplate::create($data);
            session()->flash('message', 'Đã thêm mẫu ca mới');
        }
        
        $this->resetTemplateForm();
    }
    
    public function editTemplate($id)
    {
        $template = \App\Models\ShiftTemplate::find($id);
        
        $this->editingTemplateId = $id;
        $this->templateAgencyId = $template->diem_ban_id;
        $this->templateName = $template->name;
        $this->templateStartTime = $template->start_time;
        $this->templateEndTime = $template->end_time;
        $this->templateIsActive = $template->is_active;
    }
    
    public function cancelEditTemplate()
    {
        $this->resetTemplateForm();
    }
    
    public function deleteTemplate($id)
    {
        \App\Models\ShiftTemplate::find($id)->delete();
        session()->flash('message', 'Đã xóa mẫu ca');
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
        // 1. Get all agencies (no tab filtering)
        $agencyQuery = Agency::query();
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
        
        
        // Detect new shifts and dispatch notification
        $currentShiftCount = $stats['total'];
        if ($this->previousShiftCount > 0 && $currentShiftCount > $this->previousShiftCount) {
            $newShiftsCount = $currentShiftCount - $this->previousShiftCount;
            
            // Dispatch browser event
            $this->dispatch('new-shift-detected', count: $newShiftsCount);
            
            // Debug log
            \Log::info("🔔 New shifts detected: {$newShiftsCount} (from {$this->previousShiftCount} to {$currentShiftCount})");
        }
        $this->previousShiftCount = $currentShiftCount;
        
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
                    // Use phong_ban_id directly for reliable grouping
                    // Pad with zeros to ensure '10' comes after '2' in string sort
                    $deptId = str_pad($shift->user->phong_ban_id ?? 99999, 10, '0', STR_PAD_LEFT);
                    $name = $shift->user->ho_ten;
                    
                    return $date . '|' . $time . '|' . $deptId . '|' . $name;
                })->values();
            }

            
            return view('livewire.admin.shift.shift-management', [
                'groupedAgencies' => $agencies,
                'shifts' => null, // Not used in monitoring mode
                'agencies' => Agency::with('shiftTemplates')->orderBy('ten_diem_ban')->get(), // All agencies including workshops
                'employees' => User::where('vai_tro', 'nhan_vien')->orderBy('ho_ten')->get(),
                'filteredEmployees' => $this->getFilteredEmployees(),
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


            $shifts = $query->join('nguoi_dung', 'shift_schedules.nguoi_dung_id', '=', 'nguoi_dung.id')
                           ->leftJoin('phong_ban', 'nguoi_dung.phong_ban_id', '=', 'phong_ban.id')
                           ->orderBy('shift_schedules.ngay_lam', 'desc')
                           ->orderBy('shift_schedules.gio_bat_dau', 'desc')
                           ->orderBy('phong_ban.ten_phong_ban')
                           ->orderBy('nguoi_dung.ho_ten')
                           ->select('shift_schedules.*')
                           ->paginate(20);

            return view('livewire.admin.shift.shift-management', [
                'shifts' => $shifts,
                'groupedAgencies' => null,
                'agencies' => Agency::with('shiftTemplates')->orderBy('ten_diem_ban')->get(), // All agencies including workshops
                'employees' => User::where('vai_tro', 'nhan_vien')->orderBy('ho_ten')->get(),
                'filteredEmployees' => $this->getFilteredEmployees(),
                'stats' => $stats,
            ]);
        }
    }
    
    private function getFilteredEmployees()
    {
        $query = User::where('vai_tro', 'nhan_vien');
        
        if ($this->employeeSearch) {
            $query->where(function($q) {
                $q->where('ho_ten', 'like', '%' . $this->employeeSearch . '%')
                  ->orWhere('ma_nhan_vien', 'like', '%' . $this->employeeSearch . '%');
            });
        }
        
        return $query->orderBy('ho_ten')->limit(50)->get();
    }
}
