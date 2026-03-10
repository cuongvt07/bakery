<?php

namespace App\Livewire\Admin\Shift;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\YeuCauCaLam;
use App\Models\ShiftSchedule;
use App\Models\Agency;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

#[Layout('components.layouts.app')]
class AdminShiftRequests extends Component
{
    use WithPagination;

    public $filterStatus = 'cho_duyet'; // Default to pending
    public $filterType = '';
    public $searchTerm = '';

    public $selectedRequest = null;
    public $showDetailModal = false;
    public $approvalNote = '';
    public $actionType = ''; // approve | reject

    // Lark webhook for admin approval notifications
    private const LARK_APPROVAL_WEBHOOK = 'https://open.larksuite.com/open-apis/bot/v2/hook/6ce00d25-5ae9-4bd9-8e74-a45b0773cf3b';

    public function mount()
    {
        //
    }

    public function setFilter($status)
    {
        $this->filterStatus = $status;
        $this->resetPage();
    }

    public function setTypeFilter($type)
    {
        $this->filterType = $type;
        $this->resetPage();
    }

    public function openDetailModal($requestId)
    {
        $this->selectedRequest = YeuCauCaLam::with(['nguoiDung', 'caLamViec'])->find($requestId);
        $this->showDetailModal = true;
        $this->approvalNote = '';
    }

    public function closeModal()
    {
        $this->showDetailModal = false;
        $this->selectedRequest = null;
        $this->approvalNote = '';
        $this->actionType = '';
    }

    public function approveRequest()
    {
        if (!$this->selectedRequest)
            return;

        DB::transaction(function () {
            // Update request status
            $this->selectedRequest->update([
                'trang_thai' => 'da_duyet',
                'nguoi_duyet_id' => Auth::id(),
                'ngay_duyet' => now(),
                'ghi_chu_duyet' => $this->approvalNote ?: 'Đã duyệt',
            ]);

            // If it's a shift registration request, create the shift
            if ($this->selectedRequest->loai_yeu_cau === 'xin_ca' && $this->selectedRequest->ngay_mong_muon) {
                ShiftSchedule::create([
                    'diem_ban_id' => $this->selectedRequest->nguoiDung->diem_ban_id ?? 1,
                    'nguoi_dung_id' => $this->selectedRequest->nguoi_dung_id,
                    'ngay_lam' => $this->selectedRequest->ngay_mong_muon,
                    'gio_bat_dau' => $this->selectedRequest->gio_bat_dau,
                    'gio_ket_thuc' => $this->selectedRequest->gio_ket_thuc,
                    'trang_thai' => 'chua_bat_dau',
                    'ghi_chu' => 'Tạo từ yêu cầu #' . $this->selectedRequest->id,
                ]);
            }

            // Send Notification (Broadcast to ALL)
            try {
                $typeMap = [
                    'xin_ca' => 'Xin ca',
                    'doi_ca' => 'Đổi ca',
                    'xin_nghi' => 'Nghỉ ca',
                    'ticket' => 'Ticket hỗ trợ',
                ];
                $typeName = $typeMap[$this->selectedRequest->loai_yeu_cau] ?? $this->selectedRequest->loai_yeu_cau;

                $title = "Yêu cầu '{$typeName}' đã được duyệt";
                $content = "Yêu cầu '{$typeName}' của {$this->selectedRequest->nguoiDung->ho_ten} đã được duyệt.";

                app(\App\Services\NotificationService::class)->sendToAll(
                    Auth::id(), // Sender
                    $title,
                    $content,
                    'canh_bao'
                );
            } catch (\Exception $e) {
                \Log::error('Error sending notification: ' . $e->getMessage());
            }
        });

        // Send Lark notification
        $this->sendApprovalLarkNotification($this->selectedRequest, 'approved');

        session()->flash('message', 'Đã duyệt yêu cầu thành công');
        $this->closeModal();
    }

    public function rejectRequest()
    {
        if (!$this->selectedRequest)
            return;

        $this->selectedRequest->update([
            'trang_thai' => 'tu_choi',
            'nguoi_duyet_id' => Auth::id(),
            'ngay_duyet' => now(),
            'ghi_chu_duyet' => $this->approvalNote ?: 'Từ chối',
        ]);

        // Send Notification (Broadcast to ALL)
        try {
            $typeMap = [
                'xin_ca' => 'Xin ca',
                'doi_ca' => 'Đổi ca',
                'xin_nghi' => 'Nghỉ ca',
                'ticket' => 'Ticket hỗ trợ',
            ];
            $typeName = $typeMap[$this->selectedRequest->loai_yeu_cau] ?? $this->selectedRequest->loai_yeu_cau;

            $title = "Yêu cầu '{$typeName}' bị từ chối";
            $content = "Yêu cầu '{$typeName}' của {$this->selectedRequest->nguoiDung->ho_ten} đã bị từ chối.";

            app(\App\Services\NotificationService::class)->sendToAll(
                Auth::id(), // Sender
                $title,
                $content,
                'canh_bao'
            );
        } catch (\Exception $e) {
            \Log::error('Error sending notification: ' . $e->getMessage());
        }

        // Send Lark notification
        $this->sendApprovalLarkNotification($this->selectedRequest, 'rejected');

        session()->flash('message', 'Đã từ chối yêu cầu');
        $this->closeModal();
    }

    public function bulkApprove($requestIds)
    {
        $requests = YeuCauCaLam::whereIn('id', $requestIds)
            ->where('trang_thai', 'cho_duyet')
            ->get();

        foreach ($requests as $request) {
            $request->update([
                'trang_thai' => 'da_duyet',
                'nguoi_duyet_id' => Auth::id(),
                'ngay_duyet' => now(),
                'ghi_chu_duyet' => 'Duyệt hàng loạt',
            ]);

            // Notification for bulk approval (Broadcast to ALL)
            try {
                // Determine title based on request type
                $typeMap = [
                    'xin_ca' => 'Xin ca',
                    'doi_ca' => 'Đổi ca',
                    'xin_nghi' => 'Nghỉ ca',
                    'ticket' => 'Ticket hỗ trợ',
                ];
                $typeName = $typeMap[$request->loai_yeu_cau] ?? $request->loai_yeu_cau;

                $title = "Yêu cầu '{$typeName}' đã được duyệt";
                $content = "Yêu cầu '{$typeName}' của {$request->nguoiDung->ho_ten} đã được duyệt.";

                app(\App\Services\NotificationService::class)->sendToAll(
                    Auth::id(), // Sender
                    $title,
                    $content,
                    'canh_bao'
                );
            } catch (\Exception $e) {
                \Log::error('Error sending notification: ' . $e->getMessage());
            }

            $this->sendApprovalLarkNotification($request, 'approved');
        }

        session()->flash('message', 'Đã duyệt ' . count($requestIds) . ' yêu cầu');
    }

    /**
     * Send Lark notification when request is approved/rejected
     */
    private function sendApprovalLarkNotification($request, $action = 'approved')
    {
        try {
            $user = $request->nguoiDung;
            $approver = Auth::user();

            // Parse ly_do JSON for all types
            $lyDoData = json_decode($request->ly_do, true);
            $isJsonLyDo = is_array($lyDoData);

            // Determine header and color based on action
            $isApproved = $action === 'approved';
            $headerIcon = $isApproved ? '✅' : '❌';
            $headerColor = $isApproved ? 'green' : 'red';
            $actionText = $isApproved ? 'ĐÃ DUYỆT' : 'ĐÃ TỪ CHỐI';

            // Build type label
            $typeLabels = [
                'xin_ca' => 'XIN CA',
                'doi_ca' => 'CHUYỂN CA',
                'xin_nghi' => 'NGHỈ CA',
                'ticket' => 'TICKET HỖ TRỢ',
            ];
            $typeLabel = $typeLabels[$request->loai_yeu_cau] ?? strtoupper($request->loai_yeu_cau);

            // Common employee info line (compact)
            $employeeLine = sprintf(
                "**👤 %s** (%s) | **✍️ Duyệt bởi:** %s",
                $user->ho_ten ?? $user->name ?? 'N/A',
                $user->ma_nhan_vien ?? 'N/A',
                $approver->ho_ten ?? $approver->name ?? 'Admin'
            );

            // Build content based on request type
            $contentLines = [$employeeLine];

            if ($request->loai_yeu_cau === 'doi_ca' && $isJsonLyDo) {
                // Chuyển ca - parse full JSON
                $contentLines[] = ''; // empty line
                $contentLines[] = '**🔄 THÔNG TIN CHUYỂN CA:**';

                // Ca cũ
                if (!empty($lyDoData['shift_schedule_id'])) {
                    $oldSchedule = ShiftSchedule::with(['shiftTemplate', 'agency'])->find($lyDoData['shift_schedule_id']);
                    if ($oldSchedule) {
                        $contentLines[] = sprintf(
                            "📍 **Ca cũ:** %s - %s (%s)",
                            $oldSchedule->ngay_lam ? Carbon::parse($oldSchedule->ngay_lam)->format('d/m/Y') : 'N/A',
                            $oldSchedule->shiftTemplate->name ?? ($oldSchedule->gio_bat_dau . '-' . $oldSchedule->gio_ket_thuc),
                            $oldSchedule->agency->ten_diem_ban ?? 'N/A'
                        );
                    }
                }

                // Ca mới
                if (!empty($lyDoData['new_shift_name'])) {
                    $newAgency = Agency::find($lyDoData['new_agency_id'] ?? null);
                    $contentLines[] = sprintf(
                        "📍 **Ca mới:** %s - %s (%s)",
                        !empty($lyDoData['new_shift_date']) ? Carbon::parse($lyDoData['new_shift_date'])->format('d/m/Y') : 'N/A',
                        $lyDoData['new_shift_name'] ?? 'N/A',
                        $newAgency->ten_diem_ban ?? 'N/A'
                    );
                }



            } elseif ($request->loai_yeu_cau === 'xin_nghi' && $isJsonLyDo) {
                // Nghỉ ca - parse JSON
                $contentLines[] = '';
                $contentLines[] = '**🛑 THÔNG TIN NGHỈ CA:**';

                if (!empty($lyDoData['shift_schedule_id'])) {
                    $schedule = ShiftSchedule::with(['shiftTemplate', 'agency'])->find($lyDoData['shift_schedule_id']);
                    if ($schedule) {
                        $contentLines[] = sprintf(
                            "📆 **Ca nghỉ:** %s (%s) - %s | %s",
                            $schedule->ngay_lam ? Carbon::parse($schedule->ngay_lam)->format('d/m/Y') : 'N/A',
                            $schedule->ngay_lam ? Carbon::parse($schedule->ngay_lam)->locale('vi')->isoFormat('dddd') : '',
                            $schedule->shiftTemplate->name ?? ($schedule->gio_bat_dau . '-' . $schedule->gio_ket_thuc),
                            $schedule->agency->ten_diem_ban ?? 'N/A'
                        );
                    }
                }



            } elseif ($request->loai_yeu_cau === 'ticket' && $isJsonLyDo) {
                // Ticket hỗ trợ
                $contentLines[] = sprintf("**🏪 Điểm bán:** %s", $lyDoData['agency_name'] ?? 'N/A');
                $contentLines[] = '';
                $contentLines[] = '**📢 NỘI DUNG YÊU CẦU:**';
                $contentLines[] = $lyDoData['message'] ?? 'Không có nội dung';

            } elseif ($request->loai_yeu_cau === 'xin_ca') {
                // Xin ca
                $contentLines[] = '';
                $contentLines[] = sprintf(
                    "**📅 Ngày mong muốn:** %s",
                    $request->ngay_mong_muon ? Carbon::parse($request->ngay_mong_muon)->format('d/m/Y') : 'N/A'
                );
                if ($request->gio_bat_dau && $request->gio_ket_thuc) {
                    $contentLines[] = sprintf("**⏰ Giờ:** %s - %s", $request->gio_bat_dau, $request->gio_ket_thuc);
                }


            } else {
                // Fallback for unknown format
                $contentLines[] = sprintf("**📝 Nội dung:** %s", $request->ly_do ?? 'Không có');
            }

            // Add approval note
            $contentLines[] = '';
            $contentLines[] = sprintf("**💬 Ghi chú duyệt:** %s", $request->ghi_chu_duyet ?: 'Không có');

            // Build final content
            $content = implode("\n", $contentLines);

            $card = [
                'msg_type' => 'interactive',
                'card' => [
                    'header' => [
                        'title' => [
                            'tag' => 'plain_text',
                            'content' => sprintf('%s %s YÊU CẦU %s', $headerIcon, $actionText, $typeLabel),
                        ],
                        'template' => $headerColor,
                    ],
                    'elements' => [
                        [
                            'tag' => 'div',
                            'text' => [
                                'tag' => 'lark_md',
                                'content' => $content,
                            ],
                        ],
                    ],
                ],
            ];

            Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post(self::LARK_APPROVAL_WEBHOOK, $card);

        } catch (\Exception $e) {
            \Log::error('Admin approval Lark notification failed: ' . $e->getMessage());
        }
    }

    public function render()
    {
        // IMPORTANT: Filter OUT tickets - they are now shown in Agency Dashboard
        $query = YeuCauCaLam::with(['nguoiDung', 'caLamViec'])
            ->whereIn('loai_yeu_cau', ['xin_ca', 'doi_ca', 'xin_nghi', 'ticket']);

        if ($this->filterStatus) {
            $query->where('trang_thai', $this->filterStatus);
        }

        if ($this->filterType) {
            $query->where('loai_yeu_cau', $this->filterType);
        }

        if ($this->searchTerm) {
            $query->whereHas('nguoiDung', function ($q) {
                $q->where('ho_ten', 'like', '%' . $this->searchTerm . '%')
                    ->orWhere('ma_nhan_vien', 'like', '%' . $this->searchTerm . '%');
            });
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(20);

        // Statistics (exclude tickets)
        $stats = [
            'pending' => YeuCauCaLam::where('trang_thai', 'cho_duyet')
                ->whereIn('loai_yeu_cau', ['xin_ca', 'doi_ca', 'xin_nghi', 'ticket'])
                ->count(),
            'approved' => YeuCauCaLam::where('trang_thai', 'da_duyet')
                ->whereIn('loai_yeu_cau', ['xin_ca', 'doi_ca', 'xin_nghi', 'ticket'])
                ->whereDate('ngay_duyet', Carbon::today())
                ->count(),
            'rejected' => YeuCauCaLam::where('trang_thai', 'tu_choi')
                ->whereIn('loai_yeu_cau', ['xin_ca', 'doi_ca', 'xin_nghi', 'ticket'])
                ->whereDate('ngay_duyet', Carbon::today())
                ->count(),
            'total_today' => YeuCauCaLam::whereDate('created_at', Carbon::today())
                ->whereIn('loai_yeu_cau', ['xin_ca', 'doi_ca', 'xin_nghi', 'ticket'])
                ->count(),
        ];

        return view('livewire.admin.shift.admin-shift-requests', [
            'requests' => $requests,
            'stats' => $stats,
        ]);
    }
}
