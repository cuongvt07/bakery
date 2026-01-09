<?php

namespace App\Livewire\Employee\Shift;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\ShiftSchedule as ShiftScheduleModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.mobile')]
class ShiftSchedule extends Component
{
    public $currentMonth;
    public $viewMode = 'calendar'; // calendar | list
    public $filterStatus = '';
    public $shifts = [];
    public $monthlyStats = [];
    public $calendarDays = [];
    
    // Request modal
    public $showRequestModal = false;
    public $requestType = ''; // 'doi_ca' or 'xin_nghi'
    public $selectedScheduleId = null;
    public $requestReason = '';
    
    // For shift change
    public $agencies = [];
    public $newAgencyId = null;
    public $newShiftDate = '';
    public $availableShiftsForChange = [];
    public $selectedNewShiftId = null;
    
    // Track pending requests
    public $pendingRequests = [];

    public function mount()
    {
        $this->currentMonth = Carbon::now();
        $this->agencies = \App\Models\Agency::get();
        $this->loadShifts();
        $this->loadPendingRequests();
    }

    public function loadShifts()
    {
        $startOfMonth = $this->currentMonth->copy()->startOfMonth();
        $endOfMonth = $this->currentMonth->copy()->endOfMonth();
        
        // 1. Fetch Schedules (No more Pending Requests needed since we auto-approve)
        $query = ShiftScheduleModel::where('nguoi_dung_id', Auth::id())
            ->whereBetween('ngay_lam', [$startOfMonth, $endOfMonth])
            ->with('agency');
        
        if ($this->filterStatus) {
            $query->where('trang_thai', $this->filterStatus);
        }
        
        // Sort by Date + Time
        $this->shifts = $query->orderBy('ngay_lam')->orderBy('gio_bat_dau')->get();
        
        // Calculate stats using DB ENUMs
        $this->monthlyStats = [
            'total' => $this->shifts->count(),
            'completed' => $this->shifts->where('trang_thai', 'completed')->count(), // was da_ket_thuc
            'upcoming' => $this->shifts->where('trang_thai', 'approved')->count(),   // was chua_bat_dau, now approved
            // 'pending' => 0, // No more pending requests in schedule
        ];
        
        // Build calendar grid
        $this->buildCalendar();
    }

    private function buildCalendar()
    {
        $startOfMonth = $this->currentMonth->copy()->startOfMonth();
        $endOfMonth = $this->currentMonth->copy()->endOfMonth();
        
        $startDate = $startOfMonth->copy()->startOfWeek(Carbon::SUNDAY);
        $endDate = $endOfMonth->copy()->endOfWeek(Carbon::SATURDAY);
        
        $this->calendarDays = [];
        $current = $startDate->copy();
        
        while ($current <= $endDate) {
            $dayShifts = $this->shifts->filter(function($shift) use ($current) {
                return Carbon::parse($shift->ngay_lam)->isSameDay($current);
            });
            
            $this->calendarDays[] = [
                'date' => $current->copy(),
                'isCurrentMonth' => $current->month === $this->currentMonth->month,
                'isToday' => $current->isToday(),
                'shifts' => $dayShifts,
            ];
            
            $current->addDay();
        }
    }

    public function previousMonth()
    {
        $this->currentMonth->subMonth();
        $this->loadShifts();
    }

    public function nextMonth()
    {
        $this->currentMonth->addMonth();
        $this->loadShifts();
    }

    public function toggleViewMode()
    {
        $this->viewMode = $this->viewMode === 'calendar' ? 'list' : 'calendar';
    }

    public function setFilter($status)
    {
        $this->filterStatus = $status;
        $this->loadShifts();
    }

    // Keep these for requesting changes to approved shifts
    public function requestChange($shiftId)
    {
        $this->openRequestModal('doi_ca', $shiftId);
    }

    public function requestOff($shiftId)
    {
        $this->openRequestModal('xin_nghi', $shiftId);
    }
    
    public function loadPendingRequests()
    {
        // Get all pending requests for current user's shifts
        $requests = \App\Models\YeuCauCaLam::where('nguoi_dung_id', Auth::id())
            ->where('trang_thai', 'cho_duyet')
            ->get();
        
        // Map shift_schedule_id => request
        $this->pendingRequests = [];
        foreach ($requests as $request) {
            $lyDoData = json_decode($request->ly_do, true);
            if (isset($lyDoData['shift_schedule_id'])) {
                $this->pendingRequests[$lyDoData['shift_schedule_id']] = [
                    'type' => $request->loai_yeu_cau,
                    'id' => $request->id,
                ];
            }
        }
    }
    
    public function openRequestModal($type, $scheduleId)
    {
        $this->requestType = $type;
        $this->selectedScheduleId = $scheduleId;
        $this->requestReason = '';
        
        // For shift change, initialize with current agency and load available shifts
        if ($type === 'doi_ca') {
            $currentSchedule = ShiftScheduleModel::find($scheduleId);
            $this->newAgencyId = $currentSchedule->diem_ban_id ?? null;
            $this->newShiftDate = $currentSchedule->ngay_lam->format('Y-m-d');
            $this->selectedNewShiftId = null;
            $this->loadAvailableShiftsForChange();
        }
        
        $this->showRequestModal = true;
    }
    
    public function updatedNewAgencyId()
    {
        $this->loadAvailableShiftsForChange();
    }
    
    public function updatedNewShiftDate()
    {
        $this->loadAvailableShiftsForChange();
    }
    
    public function loadAvailableShiftsForChange()
    {
        if (!$this->newAgencyId || !$this->newShiftDate) {
            $this->availableShiftsForChange = [];
            return;
        }
        
        $this->availableShiftsForChange = \App\Models\ShiftTemplate::where('diem_ban_id', $this->newAgencyId)
            ->where('is_active', true)
            ->orderBy('start_time')
            ->get();
    }
    
    public function closeRequestModal()
    {
        $this->showRequestModal = false;
        $this->reset(['requestType', 'selectedScheduleId', 'requestReason', 'newAgencyId', 'newShiftDate', 'availableShiftsForChange', 'selectedNewShiftId']);
    }
    
    public function submitRequest()
    {
        $rules = [
            'requestReason' => 'nullable|min:10',
        ];
        
        // For shift change, require new shift selection
        if ($this->requestType === 'doi_ca') {
            $rules['selectedNewShiftId'] = 'required';
        }
        
        $this->validate($rules, [
            'requestReason.min' => 'Lý do phải có ít nhất 10 ký tự',
            'selectedNewShiftId.required' => 'Vui lòng chọn ca muốn đổi sang',
        ]);
        
        // Create request with new shift info if changing
        $data = [
            'nguoi_dung_id' => Auth::id(),
            'loai_yeu_cau' => $this->requestType,
            'ca_lam_viec_id' => null, // Not related to shift templates
            'trang_thai' => 'cho_duyet',
        ];
        
        // Store shift schedule info and reason in ly_do as JSON
        $lyDoData = [
            'shift_schedule_id' => $this->selectedScheduleId,
            'reason' => $this->requestReason,
        ];
        
        // Store new shift info for shift change
        if ($this->requestType === 'doi_ca' && $this->selectedNewShiftId) {
            $newShift = \App\Models\ShiftTemplate::find($this->selectedNewShiftId);
            $lyDoData['new_agency_id'] = $this->newAgencyId;
            $lyDoData['new_shift_date'] = $this->newShiftDate;
            $lyDoData['new_shift_template_id'] = $this->selectedNewShiftId;
            $lyDoData['new_shift_name'] = $newShift->name ?? 'Ca tùy chỉnh';
            $lyDoData['new_shift_time'] = $newShift->start_time . ' - ' . $newShift->end_time;
        }
        
        $data['ly_do'] = json_encode($lyDoData);
        
        $request = \App\Models\YeuCauCaLam::create($data);
        
        // Log activity
        \App\Models\NhatKyHoatDong::logActivity(
            action: 'gui_yeu_cau',
            newData: $request->toArray(),
            description: 'Gửi yêu cầu: ' . $this->requestType
        );
        
        // Send Lark notification
        $this->sendLarkNotification($request);
        
        session()->flash('success', 'Đã gửi yêu cầu thành công!');
        $this->closeRequestModal();
        $this->loadShifts(); // Reload to show updated data
        $this->loadPendingRequests(); // Reload to show badge
    }
    
    private function sendLarkNotification($request)
    {
        try {
            $user = Auth::user();
            
            // Parse ly_do to get shift_schedule_id and reason
            $lyDoData = json_decode($request->ly_do, true);
            $schedule = ShiftScheduleModel::with(['shiftTemplate', 'agency'])->find($lyDoData['shift_schedule_id'] ?? null);
            
            if (!$schedule) return;
            
            if ($request->loai_yeu_cau === 'xin_nghi') {
                $card = [
                    'msg_type' => 'interactive',
                    'card' => [
                        'header' => [
                            'title' => [
                                'tag' => 'plain_text',
                                'content' => '🛑 YÊU CẦU XIN NGHỈ CA LÀM VIỆC',
                            ],
                            'template' => 'red',
                        ],
                        'elements' => [
                            [
                                'tag' => 'div',
                                'text' => [
                                    'tag' => 'lark_md',
                                    'content' => sprintf(
                                        "**👤 Nhân viên:** %s\n**🆔 Mã NV:** %s\n\n**📆 Ngày:** %s (%s)\n**⏰ Ca làm:** %s\n\n**📝 Lý do xin nghỉ:**\n%s",
                                        $user->ho_ten ?? $user->name,
                                        $user->ma_nhan_vien ?? 'N/A',
                                        $schedule->ngay_lam->format('d/m/Y'),
                                        $schedule->ngay_lam->locale('vi')->isoFormat('dddd'),
                                        $schedule->shiftTemplate->name ?? ($schedule->gio_bat_dau . ' - ' . $schedule->gio_ket_thuc),
                                        $lyDoData['reason'] ?: '(Không có lý do)'
                                    ),
                                ],
                            ],
                        ],
                    ],
                ];
            } else {
                // Shift change - use lyDoData
                $newAgency = \App\Models\Agency::find($lyDoData['new_agency_id'] ?? null);
                
                $card = [
                    'msg_type' => 'interactive',
                    'card' => [
                        'header' => [
                            'title' => [
                                'tag' => 'plain_text',
                                'content' => '🔁 YÊU CẦU ĐỔI CA LÀM VIỆC',
                            ],
                            'template' => 'orange',
                        ],
                        'elements' => [
                            [
                                'tag' => 'div',
                                'text' => [
                                    'tag' => 'lark_md',
                                    'content' => sprintf(
                                        "**👤 Nhân viên:** %s\n**🆔 Mã NV:** %s\n\n**🔹 Ca hiện tại:** 📆 %s (%s) | ⏰ %s | 📍 %s\n\n**🔸 Ca đề xuất đổi sang:** 📆 %s (%s) | ⏰ %s | 📍 %s\n\n**📝 Lý do xin đổi ca:**\n%s",
                                        $user->ho_ten ?? $user->name,
                                        $user->ma_nhan_vien ?? 'N/A',
                                        $schedule->ngay_lam->format('d/m/Y'),
                                        $schedule->ngay_lam->locale('vi')->isoFormat('dddd'),
                                        $schedule->shiftTemplate->name ?? ($schedule->gio_bat_dau . ' - ' . $schedule->gio_ket_thuc),
                                        $schedule->agency->ten_diem_ban ?? 'N/A',
                                        \Carbon\Carbon::parse($lyDoData['new_shift_date'])->format('d/m/Y'),
                                        \Carbon\Carbon::parse($lyDoData['new_shift_date'])->locale('vi')->isoFormat('dddd'),
                                        $lyDoData['new_shift_name'],
                                        $newAgency->ten_diem_ban ?? 'N/A',
                                        $lyDoData['reason'] ?: '(Không có lý do)'
                                    ),
                                ],
                            ],
                        ],
                    ],
                ];
            }
            
            // Send to Lark webhook
            \Log::info('Sending Lark notification', ['type' => $request->loai_yeu_cau, 'card' => $card]);
            
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post(
                'https://open.larksuite.com/open-apis/bot/v2/hook/0cab81f9-e31f-4604-98e5-bf5b356251e3',
                $card
            );
            
            \Log::info('Lark notification response', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
        } catch (\Exception $e) {
            \Log::error('Lark notification failed: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.employee.shift.shift-schedule');
    }
}
