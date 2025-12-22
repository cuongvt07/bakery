<?php

namespace App\Livewire\Admin\Shift;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Agency;
use App\Models\ShiftSchedule;
use App\Models\ShiftTemplate;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ShiftManagementNew extends Component
{
    use WithPagination;

    // Tab & Filter State
    public $selectedTab = 'all'; // 'all' or agency_id
    public $availableLocations = [];
    public $canViewAllTab = false;

    // Filters
    public $search = '';
    public $dateFrom;
    public $dateTo;
    public $statusFilter = '';

    // View Mode (Admin only in "Tất Cả" tab)
    public $viewMode = 'list'; // 'list', 'grid', 'timeline'

    // Weekly Shift Registration Modal
    public $showRegistrationModal = false;
    public $weekStartDate; // Monday of the week
    public $weekDays = []; // Array of 7 days with data
    public $selectedDays = []; // Array of selected day indices

    // Template Management Modal
    public $showTemplateModal = false;
    public $templateForm = [
        'id' => null,
        'name' => '',
        'start_time' => '',
        'end_time' => '',
        'diem_ban_id' => null,
        'is_default' => false,
        'is_active' => true,
    ];
    public $editingTemplateId = null;

    protected $queryString = [
        'selectedTab' => ['except' => 'all'],
        'search' => ['except' => ''],
    ];

    public function mount()
    {
        // Initialize date range (current week)
        $this->dateFrom = now()->startOfWeek()->format('Y-m-d');
        $this->dateTo = now()->endOfWeek()->format('Y-m-d');

        // Load available locations based on role
        if (Auth::user()->isAdmin()) {
            $this->availableLocations = Agency::where('ten_diem_ban', 'not like', '%Xưởng%')->get();
            $this->canViewAllTab = true;
        } else {
            // Employee: See all locations but filtered data
            $this->availableLocations = Agency::where('ten_diem_ban', 'not like', '%Xưởng%')->get();
            $this->canViewAllTab = false;
        }

        // Default to first location if not admin
        if (!$this->canViewAllTab && $this->availableLocations->isNotEmpty()) {
            $this->selectedTab = $this->availableLocations->first()->id;
        }
    }

    public function switchTab($tab)
    {
        $this->selectedTab = $tab;
        $this->resetPage();
    }

    public function getShiftsProperty()
    {
        $query = ShiftSchedule::with(['nguoiDung', 'agency', 'shiftTemplate'])
            ->whereBetween('ngay_lam', [$this->dateFrom, $this->dateTo]);

        // Filter by selected tab/location
        if ($this->selectedTab !== 'all') {
            $query->where('diem_ban_id', $this->selectedTab);
        }

        // Employee only sees own shifts
        if (!Auth::user()->isAdmin()) {
            $query->where('nguoi_dung_id', Auth::id());
        }

        // Search filter
        if ($this->search) {
            $query->where(function($q) {
                $q->whereHas('nguoiDung', function($nq) {
                    $nq->where('ho_ten', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('agency', function($dq) {
                    $dq->where('ten_diem_ban', 'like', '%' . $this->search . '%');
                });
            });
        }

        // Status filter
        if ($this->statusFilter) {
            $query->where('trang_thai', $this->statusFilter);
        }

        return $query->latest('ngay_lam')->latest('gio_bat_dau')->paginate(15);
    }

    public function getStatsForLocationProperty()
    {
        if ($this->selectedTab === 'all') {
            return null;
        }

        $agencyId = $this->selectedTab;

        return [
            'total_shifts' => ShiftSchedule::where('diem_ban_id', $agencyId)
                ->whereBetween('ngay_lam', [$this->dateFrom, $this->dateTo])
                ->count(),
            'active_shifts' => ShiftSchedule::where('diem_ban_id', $agencyId)
                ->where('trang_thai', 'approved')
                ->whereBetween('ngay_lam', [$this->dateFrom, $this->dateTo])
                ->count(),
            'employees' => ShiftSchedule::where('diem_ban_id', $agencyId)
                ->whereBetween('ngay_lam', [$this->dateFrom, $this->dateTo])
                ->distinct('nguoi_dung_id')
                ->count('nguoi_dung_id'),
        ];
    }

    public function openRegistrationModal()
    {
        $this->resetRegistrationForm();
        $this->initializeWeek();
        $this->showRegistrationModal = true;
    }

    public function resetRegistrationForm()
    {
        $this->selectedDays = [];
        $this->weekDays = [];
    }

    public function initializeWeek()
    {
        // Start from next Monday
        $startDate = now()->startOfWeek()->addWeek();
        $this->weekStartDate = $startDate->format('Y-m-d');
        
        // Initialize 7 days
        $this->weekDays = [];
        for ($i = 0; $i < 7; $i++) {
            $date = $startDate->copy()->addDays($i);
            $this->weekDays[$i] = [
                'date' => $date->format('Y-m-d'),
                'dayName' => $date->locale('vi')->dayName,
                'dayShort' => $date->locale('vi')->shortDayName,
                'agencyId' => null,
                'templateId' => null,
            ];
        }
    }

    public function toggleDay($dayIndex)
    {
        if (in_array($dayIndex, $this->selectedDays)) {
            $this->selectedDays = array_diff($this->selectedDays, [$dayIndex]);
        } else {
            $this->selectedDays[] = $dayIndex;
        }
    }

    public function previousWeek()
    {
        $this->weekStartDate = \Carbon\Carbon::parse($this->weekStartDate)->subWeek()->format('Y-m-d');
        $this->initializeWeek();
    }

    public function nextWeek()
    {
        $this->weekStartDate = \Carbon\Carbon::parse($this->weekStartDate)->addWeek()->format('Y-m-d');
        $this->initializeWeek();
    }

    public function registerShift()
    {
        // Validate at least one day is selected
        if (empty($this->selectedDays)) {
            session()->flash('error', 'Vui lòng chọn ít nhất một ngày!');
            return;
        }

        $registeredCount = 0;
        $errors = [];

        // Process each selected day
        foreach ($this->selectedDays as $dayIndex) {
            $dayData = $this->weekDays[$dayIndex];
            
            // Validate day data
            if (!$dayData['agencyId']) {
                $errors[] = "Ngày {$dayData['dayShort']} chưa chọn điểm bán";
                continue;
            }
            
            if (!$dayData['templateId']) {
                $errors[] = "Ngày {$dayData['dayShort']} chưa chọn mẫu ca";
                continue;
            }

            // Get template details
            $template = ShiftTemplate::find($dayData['templateId']);
            if (!$template) {
                continue;
            }

            // Create shift
            ShiftSchedule::create([
                'nguoi_dung_id' => Auth::id(),
                'diem_ban_id' => $dayData['agencyId'],
                'shift_template_id' => $template->id,
                'ngay_lam' => $dayData['date'],
                'gio_bat_dau' => $template->start_time,
                'gio_ket_thuc' => $template->end_time,
                'trang_thai' => 'chua_bat_dau',
            ]);

            $registeredCount++;
        }

        // Show results
        if ($registeredCount > 0) {
            $message = "Đã đăng ký {$registeredCount} ca làm việc!";
            if (!empty($errors)) {
                $message .= " (Có " . count($errors) . " lỗi)";
            }
            session()->flash('success', $message);
        }
        
        if (!empty($errors) && $registeredCount == 0) {
            session()->flash('error', implode(", ", $errors));
            return;
        }

        $this->showRegistrationModal = false;
        $this->resetPage();
    }

    public function getTemplatesForAgencyProperty()
    {
        if (!$this->registrationAgencyId) {
            return collect();
        }

        return ShiftTemplate::where('diem_ban_id', $this->registrationAgencyId)
            ->where('is_active', true)
            ->get();
    }

    // ========== TEMPLATE MANAGEMENT METHODS ==========
    
    public function openTemplateManagement()
    {
        $this->resetTemplateForm();
        $this->showTemplateModal = true;
    }

    public function closeTemplateModal()
    {
        $this->showTemplateModal = false;
        $this->resetTemplateForm();
    }

    public function resetTemplateForm()
    {
        $this->templateForm = [
            'id' => null,
            'name' => '',
            'start_time' => '',
            'end_time' => '',
            'diem_ban_id' => $this->selectedTab !== 'all' ? $this->selectedTab : null,
            'is_default' => false,
            'is_active' => true,
        ];
        $this->editingTemplateId = null;
        $this->resetErrorBag();
    }

    public function createTemplate()
    {
        $this->resetTemplateForm();
        $this->editingTemplateId = null;
    }

    public function editTemplate($templateId)
    {
        $template = ShiftTemplate::findOrFail($templateId);
        
        $this->templateForm = [
            'id' => $template->id,
            'name' => $template->name,
            'start_time' => $template->start_time->format('H:i'),
            'end_time' => $template->end_time->format('H:i'),
            'diem_ban_id' => $template->diem_ban_id,
            'is_default' => $template->is_default,
            'is_active' => $template->is_active,
        ];
        $this->editingTemplateId = $templateId;
    }

    public function saveTemplate()
    {
        $this->validate([
            'templateForm.name' => 'required|string|max:100',
            'templateForm.start_time' => 'required|date_format:H:i',
            'templateForm.end_time' => 'required|date_format:H:i|after:templateForm.start_time',
            'templateForm.diem_ban_id' => 'required|exists:diem_ban,id',
        ], [
            'templateForm.name.required' => 'Vui lòng nhập tên ca',
            'templateForm.start_time.required' => 'Vui lòng nhập giờ bắt đầu',
            'templateForm.end_time.required' => 'Vui lòng nhập giờ kết thúc',
            'templateForm.end_time.after' => 'Giờ kết thúc phải sau giờ bắt đầu',
            'templateForm.diem_ban_id.required' => 'Vui lòng chọn điểm bán',
        ]);

        if ($this->editingTemplateId) {
            // Update existing template
            $template = ShiftTemplate::findOrFail($this->editingTemplateId);
            $template->update([
                'name' => $this->templateForm['name'],
                'start_time' => $this->templateForm['start_time'],
                'end_time' => $this->templateForm['end_time'],
                'diem_ban_id' => $this->templateForm['diem_ban_id'],
                'is_default' => $this->templateForm['is_default'],
                'is_active' => $this->templateForm['is_active'],
            ]);
            
            session()->flash('success', 'Cập nhật mẫu ca thành công!');
        } else {
            // Create new template
            ShiftTemplate::create([
                'name' => $this->templateForm['name'],
                'start_time' => $this->templateForm['start_time'],
                'end_time' => $this->templateForm['end_time'],
                'diem_ban_id' => $this->templateForm['diem_ban_id'],
                'is_default' => $this->templateForm['is_default'],
                'is_active' => $this->templateForm['is_active'],
            ]);
            
            session()->flash('success', 'Tạo mẫu ca thành công!');
        }

        $this->resetTemplateForm();
    }

    public function deleteTemplate($templateId)
    {
        $template = ShiftTemplate::findOrFail($templateId);
        
        // Check if template is being used
        $usageCount = ShiftSchedule::where('shift_template_id', $templateId)->count();
        
        if ($usageCount > 0) {
            session()->flash('error', "Không thể xóa mẫu ca này vì đang được sử dụng trong {$usageCount} ca làm việc!");
            return;
        }
        
        $template->delete();
        session()->flash('success', 'Xóa mẫu ca thành công!');
    }

    public function getAllTemplatesProperty()
    {
        $query = ShiftTemplate::with('agency');
        
        // Filter by selected tab if not 'all'
        if ($this->selectedTab !== 'all') {
            $query->where('diem_ban_id', $this->selectedTab);
        }
        
        return $query->orderBy('diem_ban_id')->orderBy('start_time')->get();
    }

    public function render()
    {
        return view('livewire.admin.shift.shift-management-new', [
            'shifts' => $this->shifts,
            'stats' => $this->statsForLocation,
            'allTemplates' => $this->allTemplates,
        ]);
    }
}
