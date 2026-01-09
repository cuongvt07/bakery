<?php

namespace App\Livewire\Employee\Shift;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\ShiftTemplate;
use App\Models\ShiftSchedule;
use App\Models\Agency;
use App\Models\NhatKyHoatDong;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

#[Layout('components.layouts.mobile')]
class ShiftRegistration extends Component
{
    public $currentMonth;
    public $agencies = [];
    public $selectedAgencyId;
    public $selectedAgency;
    public $availableSlots = [];
    public $days = [];
    
    public $registeredShifts = [];
    
    // View mode
    public $viewMode = 'calendar'; // 'calendar' or 'list'
    
    // Request modal
    public $showRequestModal = false;
    public $requestType = ''; // 'doi_ca' or 'xin_nghi'
    public $selectedScheduleId = null;
    public $requestReason = '';
    
    // For shift change
    public $newAgencyId = null;
    public $newShiftDate = '';
    public $availableShiftsForChange = [];
    public $selectedNewShiftId = null;
    
    // Track pending requests
    public $pendingRequests = [];

    public function mount()
    {
        $this->currentMonth = Carbon::now()->startOfMonth();
        $this->agencies = Agency::get();
        $this->selectedAgency = Agency::first();
        $this->selectedAgencyId = $this->selectedAgency?->id;
        
        $this->loadData();
        $this->loadPendingRequests();
    }

    public function updatedSelectedAgencyId($value)
    {
        $this->selectedAgency = Agency::find($value);
        $this->loadData();
    }

    public function loadData()
    {
        $this->days = [];
        $start = $this->currentMonth->copy();
        $end = $this->currentMonth->copy()->endOfMonth();
        $daysInMonth = $end->day;

        for ($i = 1; $i <= $daysInMonth; $i++) {
            $this->days[] = $start->copy()->day($i);
        }
        
        if ($this->selectedAgency) {
            $this->availableSlots = ShiftTemplate::where('diem_ban_id', $this->selectedAgency->id)
                ->where('is_active', true)
                ->orderBy('start_time')
                ->get();
        } else {
            $this->availableSlots = [];
        }

        $schedules = ShiftSchedule::where('nguoi_dung_id', Auth::id())
            ->whereBetween('ngay_lam', [$start->format('Y-m-d'), $end->format('Y-m-d')])
            ->where('diem_ban_id', $this->selectedAgencyId)
            ->get();

        $this->registeredShifts = [];
        foreach ($schedules as $sched) {
            $date = $sched->ngay_lam->format('Y-m-d');
            $start = Carbon::parse($sched->gio_bat_dau)->format('H:i');
            
            $key = $date . '_' . $start;
            $this->registeredShifts[$key] = $sched->id;
        }
    }

    public function toggleShift($date, $templateId)
    {
        $template = ShiftTemplate::find($templateId);
        if (!$template) return;

        $startTime = Carbon::parse($template->start_time)->format('H:i');
        $key = $date . '_' . $startTime;

        if (isset($this->registeredShifts[$key])) {
            $scheduleId = $this->registeredShifts[$key];
            $schedule = ShiftSchedule::find($scheduleId);
            
            if ($schedule) {
                NhatKyHoatDong::logActivity(
                    action: 'huy_lich_lam',
                    oldData: $schedule->toArray(),
                    description: "Hủy lịch làm việc {$date} ({$startTime})"
                );
                
                $schedule->delete();
            }
            
            unset($this->registeredShifts[$key]);
        } else {
            // Using 'approved' as default CONFIRMED status per table definition
            $schedule = ShiftSchedule::create([
                'nguoi_dung_id' => Auth::id(),
                'diem_ban_id' => $this->selectedAgency->id,
                'shift_template_id' => $template->id,
                'ngay_lam' => $date,
                'gio_bat_dau' => $template->start_time,
                'gio_ket_thuc' => $template->end_time,
                'trang_thai' => 'approved', // FIXED: Use valid ENUM value
                'ghi_chu' => 'Đăng ký nhanh',
            ]);

            NhatKyHoatDong::logActivity(
                action: 'dang_ky_lich',
                newData: $schedule->toArray(),
                description: "Đăng ký lịch làm việc {$date} ({$startTime})"
            );

            $this->registeredShifts[$key] = $schedule->id;
        }
    }

    public function previousMonth()
    {
        $this->currentMonth->subMonth();
        $this->loadData();
    }

    public function nextMonth()
    {
        $this->currentMonth->addMonth();
        $this->loadData();
    }
    
    public function toggleViewMode()
    {
        $this->viewMode = $this->viewMode === 'calendar' ? 'list' : 'calendar';
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
    
    public function openRequestModal($type, $scheduleId = null)
    {
        $this->requestType = $type;
        $this->selectedScheduleId = $scheduleId;
        $this->requestReason = '';
        
        // For shift change, initialize with current agency and load available shifts
        if ($type === 'doi_ca' && $scheduleId) {
            $currentSchedule = ShiftSchedule::find($scheduleId);
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
        
        $this->availableShiftsForChange = ShiftTemplate::where('diem_ban_id', $this->newAgencyId)
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
        \Log::info('=== submitRequest START ===', ['type' => $this->requestType]);
        
        $rules = [
            'requestReason' => 'nullable|min:10',
        ];
        
        // For shift change, require new shift selection
        if ($this->requestType === 'doi_ca') {
            $rules['selectedNewShiftId'] = 'required';
        }
        
        // For ticket, reason is required
        if ($this->requestType === 'ticket') {
            $rules['requestReason'] = 'required|min:10';
        }
        
        \Log::info('Validation rules', $rules);
        
        $this->validate($rules, [
            'requestReason.min' => 'Lý do phải có ít nhất 10 ký tự',
            'requestReason.required' => 'Vui lòng nhập yêu cầu',
            'selectedNewShiftId.required' => 'Vui lòng chọn ca muốn đổi sang',
        ]);
        
        \Log::info('Validation passed');
        
        $user = Auth::user();
        
        // Create request with new shift info if changing
        $data = [
            'nguoi_dung_id' => $user->id,
            'loai_yeu_cau' => $this->requestType,
            'ca_lam_viec_id' => null, // Not related to shift templates
            'trang_thai' => 'cho_duyet',
        ];
        
        // Handle different request types
        if ($this->requestType === 'ticket') {
            // Ticket: store agency info
            $agency = Agency::find($user->diem_ban_id);
            $lyDoData = [
                'message' => $this->requestReason,
                'agency_id' => $user->diem_ban_id,
                'agency_name' => $agency->ten_diem_ban ?? 'N/A',
            ];
        } else {
            // Shift change or leave: store shift schedule info and reason
            $lyDoData = [
                'shift_schedule_id' => $this->selectedScheduleId,
                'reason' => $this->requestReason,
            ];
            
            // Store new shift info for shift change
            if ($this->requestType === 'doi_ca' && $this->selectedNewShiftId) {
                $newShift = ShiftTemplate::find($this->selectedNewShiftId);
                $lyDoData['new_agency_id'] = $this->newAgencyId;
                $lyDoData['new_shift_date'] = $this->newShiftDate;
                $lyDoData['new_shift_template_id'] = $this->selectedNewShiftId;
                $lyDoData['new_shift_name'] = $newShift->name ?? 'Ca tùy chỉnh';
                $lyDoData['new_shift_time'] = $newShift->start_time . ' - ' . $newShift->end_time;
            }
        }
        
        $data['ly_do'] = json_encode($lyDoData);
        
        $request = \App\Models\YeuCauCaLam::create($data);
        
        \Log::info('Request created', ['id' => $request->id, 'ly_do' => $request->ly_do]);
        
        // Log activity
        NhatKyHoatDong::logActivity(
            action: 'gui_yeu_cau',
            newData: $request->toArray(),
            description: 'Gửi yêu cầu: ' . $this->requestType
        );
        
        \Log::info('About to send Lark notification');
        
        // Send Lark notification
        try {
            if ($this->requestType === 'ticket') {
                $this->sendTicketNotification($request, $lyDoData);
            } else {
                $this->sendLarkNotification($request);
            }
            \Log::info('After sendLarkNotification - SUCCESS');
        } catch (\Exception $e) {
            \Log::error('sendLarkNotification ERROR: ' . $e->getMessage());
            
            // Write to file for debugging
            $errorLog = storage_path('logs/lark_error.log');
            file_put_contents($errorLog, "\n\n=== " . date('Y-m-d H:i:s') . " ===\n", FILE_APPEND);
            file_put_contents($errorLog, "Error: {$e->getMessage()}\n", FILE_APPEND);
            file_put_contents($errorLog, "File: {$e->getFile()}:{$e->getLine()}\n", FILE_APPEND);
        }
        
        \Log::info('After sendLark notification');
        
        $successMessage = $this->requestType === 'ticket' ? '🚨 Đã gửi ticket khẩn cấp!' : 'Đã gửi yêu cầu thành công!';
        session()->flash('success', $successMessage);
        $this->closeRequestModal();
        $this->loadPendingRequests(); // Reload to show badge
    }
    
    public function openTicketModal()
    {
        $this->ticketMessage = '';
        $this->showTicketModal = true;
    }
    
    public function closeTicketModal()
    {
        $this->showTicketModal = false;
        $this->ticketMessage = '';
    }
    
    public function submitTicket()
    {
        \Log::info('=== submitTicket START ===');
        
        $this->validate([
            'ticketMessage' => 'required|min:10',
        ], [
            'ticketMessage.required' => 'Vui lòng nhập yêu cầu',
            'ticketMessage.min' => 'Yêu cầu phải có ít nhất 10 ký tự',
        ]);
        
        $user = Auth::user();
        $agency = Agency::find($user->diem_ban_id);
        
        \Log::info('User and agency loaded', ['user_id' => $user->id, 'agency_id' => $user->diem_ban_id]);
        
        // Create ticket request
        $data = [
            'nguoi_dung_id' => $user->id,
            'loai_yeu_cau' => 'ticket',
            'ca_lam_viec_id' => null,
            'trang_thai' => 'cho_duyet',
        ];
        
        $lyDoData = [
            'message' => $this->ticketMessage,
            'agency_id' => $user->diem_ban_id,
            'agency_name' => $agency->ten_diem_ban ?? 'N/A',
        ];
        
        $data['ly_do'] = json_encode($lyDoData);
        
        \Log::info('About to create ticket', $data);
        
        $ticket = \App\Models\YeuCauCaLam::create($data);
        
        \Log::info('Ticket created', ['id' => $ticket->id]);
        
        // Send to Lark ticket webhook
        $this->sendTicketNotification($ticket, $lyDoData);
        
        session()->flash('success', '🚨 Đã gửi ticket khẩn cấp!');
        $this->closeTicketModal();
    }
    
    private function sendTicketNotification($ticket, $lyDoData)
    {
        try {
            $user = Auth::user();
            
            $card = [
                'msg_type' => 'interactive',
                'card' => [
                    'header' => [
                        'title' => [
                            'tag' => 'plain_text',
                            'content' => '🚨 TICKET KHẨN CẤP TỪ ĐIỂM BÁN',
                        ],
                        'template' => 'red',
                    ],
                    'elements' => [
                        [
                            'tag' => 'div',
                            'text' => [
                                'tag' => 'lark_md',
                                'content' => sprintf(
                                    "**🏪 Điểm bán:** %s\n**👤 Nhân viên:** %s\n**🆔 Mã NV:** %s\n\n**📢 Yêu cầu giúp đỡ:**\n%s",
                                    $lyDoData['agency_name'],
                                    $user->ho_ten ?? $user->name,
                                    $user->ma_nhan_vien ?? 'N/A',
                                    $lyDoData['message']
                                ),
                            ],
                        ],
                    ],
                ],
            ];
            
            \Illuminate\Support\Facades\Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post(
                'https://open.larksuite.com/open-apis/bot/v2/hook/1f6c319b-bfef-4b9d-a4e4-3972fbdcc4ae',
                $card
            );
        } catch (\Exception $e) {
            \Log::error('Ticket notification failed: ' . $e->getMessage());
        }
    }
    
    private function sendLarkNotification($request)
    {
        try {
            $user = Auth::user();
            
            // Parse ly_do to get shift_schedule_id and reason
            $lyDoData = json_decode($request->ly_do, true);
            $schedule = ShiftSchedule::with(['shiftTemplate', 'agency'])->find($lyDoData['shift_schedule_id'] ?? null);
            
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
            
            // Save to separate file for debugging
            $logFile = storage_path('logs/lark.log');
            file_put_contents($logFile, "\n\n=== " . date('Y-m-d H:i:s') . " ===\n", FILE_APPEND);
            file_put_contents($logFile, "Request Type: {$request->loai_yeu_cau}\n", FILE_APPEND);
            file_put_contents($logFile, "Card JSON:\n" . json_encode($card, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n", FILE_APPEND);
            
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post(
                'https://open.larksuite.com/open-apis/bot/v2/hook/0cab81f9-e31f-4604-98e5-bf5b356251e3',
                $card
            );
            
            // Log response
            file_put_contents($logFile, "Response Status: {$response->status()}\n", FILE_APPEND);
            file_put_contents($logFile, "Response Body: {$response->body()}\n", FILE_APPEND);
            
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
        return view('livewire.employee.shift.shift-registration');
    }
}
