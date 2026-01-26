<?php

namespace App\Livewire\Employee\Shift;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\YeuCauCaLam;
use App\Models\ShiftSchedule;
use App\Models\NhatKyHoatDong; // Added for logging
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.mobile')]
class ShiftRequests extends Component
{
    public $upcomingShifts = [];

    public function mount()
    {
        // Handle URL parameters for quick actions
        $type = request()->query('type');
        $shiftId = request()->query('shift');

        if ($type && in_array($type, ['doi_ca', 'xin_nghi'])) {
            $this->openRequestModal($type, $shiftId);
        }

        $this->loadRequests();
    }

    public function updatedRequestType($value)
    {
        // Auto-select current shift for "doi_ca"
        if ($value === 'doi_ca') {
            $currentShift = \App\Models\CaLamViec::where('nguoi_dung_id', Auth::id())
                ->where('trang_thai', 'dang_lam')
                ->latest()
                ->first();

            if ($currentShift) {
                $this->selectedShiftId = $currentShift->id;
            }
        }

        // Load upcoming shifts for "xin_nghi"
        if ($value === 'xin_nghi' || $value === 'doi_ca') {
            $this->upcomingShifts = ShiftSchedule::with(['shiftTemplate', 'agency'])
                ->where('nguoi_dung_id', Auth::id())
                ->where('ngay_lam', '>=', today())
                ->orderBy('ngay_lam')
                ->limit(10)
                ->get();
        }
    }

    public function loadRequests()
    {
        $query = YeuCauCaLam::where('nguoi_dung_id', Auth::id())
            ->with('caLamViec'); // Note: This relation might point to CaLamViec, but ID could be Schedule

        if ($this->filterStatus) {
            $query->where('trang_thai', $this->filterStatus);
        }

        $this->requests = $query->orderBy('created_at', 'desc')->get();
    }

    public function setFilter($status)
    {
        $this->filterStatus = $status;
        $this->loadRequests();
    }

    public function openRequestModal($type, $shiftId = null)
    {
        $this->requestType = $type;
        $this->selectedShiftId = $shiftId;
        $this->requestDate = Carbon::today()->format('Y-m-d');
        $this->requestNote = '';
        $this->showRequestModal = true;

        // Trigger update logic
        $this->updatedRequestType($type);
    }

    public function closeModal()
    {
        $this->showRequestModal = false;
        $this->reset(['requestType', 'selectedShiftId', 'requestDate', 'requestNote', 'upcomingShifts']);
    }

    public function submitRequest()
    {
        $rules = [
            'requestType' => 'required|in:xin_ca,doi_ca,xin_nghi,ticket',
            'requestNote' => 'required',
        ];

        if (in_array($this->requestType, ['doi_ca', 'xin_nghi'])) {
            $rules['selectedShiftId'] = 'required';
        }

        $this->validate($rules, [
            'requestNote.required' => 'Vui lòng nhập lý do',
            'selectedShiftId.required' => 'Vui lòng chọn ca làm việc',
        ]);

        $user = Auth::user();

        $data = [
            'nguoi_dung_id' => $user->id,
            'loai_yeu_cau' => $this->requestType,
            'trang_thai' => 'cho_duyet',
        ];

        // Handle different request types
        if ($this->requestType === 'ticket') {
            // Ticket: store agency info
            $agency = \App\Models\Agency::find($user->diem_ban_id);
            $lyDoData = [
                'message' => $this->requestNote,
                'agency_id' => $user->diem_ban_id,
                'agency_name' => $agency->ten_diem_ban ?? 'N/A',
            ];
            $data['ly_do'] = json_encode($lyDoData);
            $data['ca_lam_viec_id'] = null;
            if (isset($user->diem_ban_id)) {
                $data['diem_ban_id'] = $user->diem_ban_id;
            }
        } else {
            // Regular request
            if ($this->selectedShiftId) {
                // Determine agency ID from the selected shift (Schedule or Active Shift)
                $agencyId = null;
                if ($this->requestType === 'doi_ca') {
                    // Could be Active Shift (CaLamViec) or Schedule
                    $activeShift = \App\Models\CaLamViec::find($this->selectedShiftId);
                    if ($activeShift)
                        $agencyId = $activeShift->diem_ban_id;
                    else {
                        $sch = ShiftSchedule::find($this->selectedShiftId);
                        if ($sch)
                            $agencyId = $sch->diem_ban_id;
                    }
                } elseif ($this->requestType === 'xin_nghi') {
                    $sch = ShiftSchedule::find($this->selectedShiftId);
                    if ($sch)
                        $agencyId = $sch->diem_ban_id;
                }

                $data['ca_lam_viec_id'] = $this->selectedShiftId;
                if ($agencyId)
                    $data['diem_ban_id'] = $agencyId;
            }

            if ($this->requestType === 'xin_ca') {
                $data['ngay_mong_muon'] = $this->requestDate;
            }
            $data['ly_do'] = $this->requestNote;
        }

        $request = YeuCauCaLam::create($data);

        // Log activity
        NhatKyHoatDong::logActivity(
            action: 'gui_yeu_cau',
            newData: $request->toArray(),
            description: 'Gửi yêu cầu: ' . $this->requestType . ' (' . $this->requestNote . ')'
        );

        session()->flash('message', $this->requestType === 'ticket' ? '🚨 Đã gửi ticket khẩn cấp!' : 'Đã gửi yêu cầu thành công!');

        // Send Lark notification
        if ($this->requestType === 'ticket') {
            $this->sendTicketNotification($request, json_decode($request->ly_do, true));
        } else {
            $this->sendLarkNotification($request);
        }

        $this->closeModal();
        $this->loadRequests();
    }

    public function cancelRequest($requestId)
    {
        $request = YeuCauCaLam::where('id', $requestId)
            ->where('nguoi_dung_id', Auth::id())
            ->where('trang_thai', 'cho_duyet')
            ->first();

        if ($request) {
            $oldData = $request->toArray();

            $request->update(['trang_thai' => 'tu_choi']); // User cancels -> status = rejected/cancelled (using tu_choi as cancel for now)

            // Log activity
            NhatKyHoatDong::logActivity(
                action: 'huy_yeu_cau',
                oldData: $oldData,
                newData: $request->fresh()->toArray(),
                description: 'Hủy yêu cầu #' . $requestId
            );

            session()->flash('message', 'Đã hủy yêu cầu');
            $this->loadRequests();
        }
    }

    private function sendLarkNotification($request)
    {
        try {
            $user = Auth::user();
            $schedule = null;
            $shiftName = 'N/A';
            $date = 'N/A';

            if ($request->ca_lam_viec_id) {
                // Try to find as Schedule first
                $schedule = ShiftSchedule::with(['shiftTemplate', 'agency'])->find($request->ca_lam_viec_id);

                if (!$schedule) {
                    // Try to find as Active Shift (CaLamViec)
                    $activeShift = \App\Models\CaLamViec::with(['shiftTemplate', 'diemBan'])->find($request->ca_lam_viec_id);
                    if ($activeShift) {
                        // Mock schedule-like object or extract data
                        $date = $activeShift->ngay_lam->format('d/m/Y');
                        $shiftName = $activeShift->shiftTemplate->name ?? 'Ca không tên';
                        $schedule = $activeShift; // Polymorphic usage for notification
                    }
                } else {
                    $date = $schedule->ngay_lam->format('d/m/Y');
                    $shiftName = $schedule->shiftTemplate->name ?? ($schedule->gio_bat_dau . ' - ' . $schedule->gio_ket_thuc);
                }
            }

            if ($request->loai_yeu_cau === 'xin_nghi' && $schedule) {
                // Leave request card
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
                                        "**👤 Nhân viên:** %s\n**🆔 Mã NV:** %s\n\n**📆 Ngày:** %s\n**⏰ Ca làm:** %s\n\n**📝 Lý do xin nghỉ:**\n%s",
                                        $user->ho_ten ?? $user->name,
                                        $user->ma_nhan_vien ?? 'N/A',
                                        $date,
                                        $shiftName,
                                        $request->ly_do
                                    ),
                                ],
                            ],
                        ],
                    ],
                ];
            } elseif ($request->loai_yeu_cau === 'doi_ca' && $schedule) {
                // Shift change request card
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
                                        "**👤 Nhân viên:** %s\n**🆔 Mã NV:** %s\n\n**🔹 Ca hiện tại**\n📆 %s\n⏰ %s\n\n**📝 Lý do xin đổi ca:**\n%s",
                                        $user->ho_ten ?? $user->name,
                                        $user->ma_nhan_vien ?? 'N/A',
                                        $date,
                                        $shiftName,
                                        $request->ly_do
                                    ),
                                ],
                            ],
                        ],
                    ],
                ];
            } else {
                return; // No notification for xin_ca or if no schedule
            }

            // Send to Lark webhook
            // \Illuminate\Support\Facades\Http::post(
            //     'https://open.larksuite.com/open-apis/bot/v2/hook/0cab81f9-e31f-4604-98e5-bf5b356251e3',
            //     $card
            // );
        } catch (\Exception $e) {
            // Log error but don't fail the request
            \Log::error('Lark notification failed: ' . $e->getMessage());
        }
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

    public function render()
    {
        return view('livewire.employee.shift.shift-requests', [
            'requests' => $this->requests,
        ]);
    }
}
