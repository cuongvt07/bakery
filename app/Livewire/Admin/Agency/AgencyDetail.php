<?php

namespace App\Livewire\Admin\Agency;

use App\Models\Agency;
use App\Models\AgencyNote;
use App\Models\NoteType;
use App\Models\AgencyLocation;
use App\Models\YeuCauCaLam;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class AgencyDetail extends Component
{
    use \Livewire\WithPagination;

    public Agency $agency;
    public $activeTab = 'all';
    public $showTypeModal = false;
    public $showLocationModal = false;

    // Filter & Search
    public $search = '';
    public $statusFilter = ''; // '', 'processed', 'pending'
    public $dateFilter = '';

    // Note form modal
    public $showNoteModal = false;
    public $editingNoteId = null;

    // Ticket modal
    public $showTicketModal = false;
    public $selectedTicket = null;
    public $approvalNote = '';

    // Form fields
    public $loai = '';
    public $tieu_de = '';
    public $noi_dung = '';
    public $ngay_nhac_nho = '';
    public $muc_do_quan_trong = 'trung_binh';
    public $vi_tri_id = '';
    public $mo_ta_vi_tri = '';
    public $dia_diem = '';
    public $ma_vat_dung = '';

    public function mount($id)
    {
        $this->agency = Agency::find($id); // Remove 'notes' eager load as we query manually
        if (!$this->agency)
            abort(404);
    }

    // Reset pagination when filtering
    public function updatedSearch()
    {
        $this->resetPage();
    }
    public function updatedStatusFilter()
    {
        $this->resetPage();
    }
    public function updatedActiveTab()
    {
        $this->resetPage();
    }

    public function openAddNoteModal()
    {
        $this->reset(['editingNoteId', 'loai', 'tieu_de', 'noi_dung', 'ngay_nhac_nho', 'muc_do_quan_trong', 'vi_tri_id', 'mo_ta_vi_tri', 'dia_diem', 'ma_vat_dung']);

        // Auto-select type based on active tab
        if ($this->activeTab !== 'all') {
            $this->loai = $this->activeTab;
        }

        $this->showNoteModal = true;
    }

    public function openEditNoteModal($noteId)
    {
        $note = AgencyNote::findOrFail($noteId);

        $this->editingNoteId = $note->id;
        $this->loai = $note->loai;
        $this->tieu_de = $note->tieu_de;
        $this->noi_dung = $note->noi_dung;
        $this->muc_do_quan_trong = $note->muc_do_quan_trong;
        $this->ngay_nhac_nho = $note->ngay_nhac_nho ? \Carbon\Carbon::parse($note->ngay_nhac_nho)->format('Y-m-d') : null;
        $this->vi_tri_id = $note->vi_tri_id;
        $this->mo_ta_vi_tri = $note->metadata['mo_ta_vi_tri'] ?? '';
        $this->dia_diem = $note->metadata['dia_diem'] ?? '';
        $this->ma_vat_dung = $note->metadata['ma_vat_dung'] ?? '';

        $this->showNoteModal = true;
    }

    public function saveNote()
    {
        $this->validate([
            'loai' => 'required',
            'tieu_de' => 'required|string|max:200',
        ]);

        $data = [
            'diem_ban_id' => $this->agency->id,
            'loai' => $this->loai,
            'tieu_de' => $this->tieu_de,
            'noi_dung' => $this->noi_dung,
            'ngay_nhac_nho' => $this->ngay_nhac_nho ?: null,
            'muc_do_quan_trong' => $this->muc_do_quan_trong,
            'da_xu_ly' => false,
            'nguoi_tao_id' => auth()->id(),
        ];

        // Add location-specific fields if location selected
        if ($this->vi_tri_id) {
            $data['vi_tri_id'] = $this->vi_tri_id;
            $data['metadata'] = [
                'mo_ta_vi_tri' => $this->mo_ta_vi_tri,
                'dia_diem' => $this->dia_diem,
                'ma_vat_dung' => $this->ma_vat_dung,
            ];
        }

        if ($this->editingNoteId) {
            AgencyNote::find($this->editingNoteId)->update($data);
            session()->flash('success', 'Cập nhật ghi chú thành công');
        } else {
            AgencyNote::create($data);
            session()->flash('success', 'Thêm ghi chú thành công');
        }

        $this->showNoteModal = false;
    }

    public function deleteNote($noteId)
    {
        try {
            $note = AgencyNote::findOrFail($noteId);
            if ($note->diem_ban_id === $this->agency->id) {
                $note->delete();
                session()->flash('success', 'Đã xóa ghi chú');
            }
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == 23000) {
                session()->flash('error', 'Không thể xóa ghi chú này vì đang được sử dụng.');
            } else {
                session()->flash('error', 'Có lỗi xảy ra khi xóa ghi chú.');
            }
        }
    }

    public function toggleComplete($noteId)
    {
        $note = AgencyNote::find($noteId);
        if ($note && $note->diem_ban_id === $this->agency->id) {
            $note->da_xu_ly = !$note->da_xu_ly;
            $note->save();

            session()->flash('success', 'Đã cập nhật trạng thái');
        }
    }

    public function extendReminder($noteId)
    {
        $note = AgencyNote::find($noteId);
        if ($note && $note->diem_ban_id === $this->agency->id) {
            if ($note->ngay_nhac_nho) {
                $note->ngay_nhac_nho = \Carbon\Carbon::parse($note->ngay_nhac_nho)->addMonth();
                $note->da_xu_ly = false; // Ensure it's active again if it was processed? User said "Processed -> +1 month", maybe they mean "Mark as processed AND NEXT reminder is +1 month"? 
                // "mở tab ✏️ Sửa ghi chú thì có nút đã xử lý --> thì lúc này thời gian nhắc nhở sau + 1 tháng sau"
                // It seems they want a quick action to say "I handled this recurrent task, remind me again next month".
                // So let's just update date and keep it 'da_xu_ly = false' (active) so it reminds again later? 
                // Or maybe the user implies "Processing this specific instance".
                // If I just change the date, it remains "pending" but for a future date, effectively "processed for now".
                $note->save();
                session()->flash('success', 'Đã gia hạn nhắc nhở thêm 1 tháng');
                $this->showNoteModal = false; // Close modal after quick action
            }
        }
    }

    /**
     * Show ticket detail modal
     */
    public function viewTicketDetail($ticketId)
    {
        $this->selectedTicket = YeuCauCaLam::with(['nguoiDung', 'diemBan'])->find($ticketId);
        if ($this->selectedTicket && $this->selectedTicket->diem_ban_id === $this->agency->id) {
            $this->approvalNote = '';
            $this->showTicketModal = true;
        }
    }

    /**
     * Close ticket modal
     */
    public function closeTicketModal()
    {
        $this->showTicketModal = false;
        $this->selectedTicket = null;
    }

    /**
     * Confirm/acknowledge a ticket (first step)
     */
    public function confirmTicket($ticketId)
    {
        $ticket = YeuCauCaLam::find($ticketId);
        if ($ticket && $ticket->diem_ban_id === $this->agency->id && $ticket->loai_yeu_cau === 'ticket') {
            $ticket->update([
                'trang_thai' => 'dang_xu_ly',
                'nguoi_duyet_id' => \Illuminate\Support\Facades\Auth::id(),
                'ghi_chu_duyet' => $this->approvalNote ?: 'Đang xử lý',
            ]);

            session()->flash('success', 'Đã xác nhận ticket, đang xử lý');
            $this->closeTicketModal();
        }
    }

    /**
     * Resolve/complete a ticket (final approval - sends notifications)
     */
    public function resolveTicket($ticketId)
    {
        $ticket = YeuCauCaLam::find($ticketId);
        if ($ticket && $ticket->diem_ban_id === $this->agency->id && $ticket->loai_yeu_cau === 'ticket') {
            $ticket->update([
                'trang_thai' => 'da_duyet',
                'nguoi_duyet_id' => \Illuminate\Support\Facades\Auth::id(),
                'ngay_duyet' => now(),
                'ghi_chu_duyet' => $this->approvalNote ?: 'Đã xử lý xong',
            ]);

            // Notify the specific employee who created the ticket
            try {
                $agencyName = $this->agency->ten_diem_ban ?? 'Cửa hàng';
                $adminName = \Illuminate\Support\Facades\Auth::user()->ho_ten ?? 'Admin';

                $lyDoData = json_decode($ticket->ly_do, true);
                $message = $lyDoData['message'] ?? $ticket->ly_do ?? 'Ticket';

                $title = "Ticket của bạn đã được xử lý";
                $content = "Ticket \"{$message}\" tại {$agencyName} đã được xử lý bởi {$adminName}.";
                if ($this->approvalNote) {
                    $content .= "\nGhi chú: " . $this->approvalNote;
                }

                // Send to specific user instead of all
                app(\App\Services\NotificationService::class)->sendToUser(
                    \Illuminate\Support\Facades\Auth::id(),
                    $ticket->nguoi_dung_id,
                    $title,
                    $content,
                    'canh_bao',
                    $ticket->diem_ban_id
                );
            } catch (\Exception $e) {
                \Log::error('Error sending ticket notification: ' . $e->getMessage());
            }

            // Send Lark notification
            $this->sendTicketResolutionLark($ticket);

            session()->flash('success', 'Đã xử lý ticket thành công');
            $this->closeTicketModal();
        }
    }

    /**
     * Send Lark notification when ticket is resolved
     */
    private function sendTicketResolutionLark($ticket)
    {
        try {
            $user = $ticket->nguoiDung;
            $approver = \Illuminate\Support\Facades\Auth::user();
            $lyDoData = json_decode($ticket->ly_do, true);
            $message = $lyDoData['message'] ?? $ticket->ly_do ?? 'Không có nội dung';
            $agencyName = $lyDoData['agency_name'] ?? $this->agency->ten_diem_ban ?? 'N/A';

            $card = [
                'msg_type' => 'interactive',
                'card' => [
                    'header' => [
                        'title' => [
                            'tag' => 'plain_text',
                            'content' => '✅ ĐÃ XỬ LÝ TICKET HỖ TRỢ',
                        ],
                        'template' => 'green',
                    ],
                    'elements' => [
                        [
                            'tag' => 'div',
                            'text' => [
                                'tag' => 'lark_md',
                                'content' => sprintf(
                                    "**🏪 Điểm bán:** %s\n**👤 Nhân viên:** %s (%s)\n**✍️ Xử lý bởi:** %s\n\n**📢 Nội dung yêu cầu:**\n%s\n\n**💬 Ghi chú xử lý:** %s",
                                    $agencyName,
                                    $user->ho_ten ?? $user->name ?? 'N/A',
                                    $user->ma_nhan_vien ?? 'N/A',
                                    $approver->ho_ten ?? $approver->name ?? 'Admin',
                                    $message,
                                    $ticket->ghi_chu_duyet ?: 'Không có'
                                ),
                            ],
                        ],
                    ],
                ],
            ];

            \Illuminate\Support\Facades\Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post(
                    'https://open.larksuite.com/open-apis/bot/v2/hook/6ce00d25-5ae9-4bd9-8e74-a45b0773cf3b',
                    $card
                );
        } catch (\Exception $e) {
            \Log::error('Ticket resolution Lark notification failed: ' . $e->getMessage());
        }
    }

    public function render()
    {
        // Load dynamic note types for this agency
        $noteTypes = NoteType::where('diem_ban_id', $this->agency->id)
            ->where('ma_loai', '!=', 'vat_dung') // Hide material tab as it's now a separate module
            ->where('hien_thi', true)
            ->orderBy('thu_tu')
            ->get();

        $query = $this->agency->notes();

        // Filter by tab
        if ($this->activeTab !== 'all') {
            $query->where('loai', $this->activeTab);
        }

        // Filter by search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('tieu_de', 'like', '%' . $this->search . '%')
                    ->orWhere('noi_dung', 'like', '%' . $this->search . '%');
            });
        }

        // Filter by status
        if ($this->statusFilter === 'processed') {
            $query->where('da_xu_ly', true);
        } elseif ($this->statusFilter === 'pending') {
            $query->where('da_xu_ly', false);
        }

        // Filter by date (approximate filter for created_at or reminder)
        if ($this->dateFilter) {
            $query->whereDate('created_at', $this->dateFilter);
        }
        if ($this->dateFilter) {
            $query->whereDate('created_at', $this->dateFilter);
        }

        $notes = $query->orderBy('created_at', 'desc')
            ->paginate(10);

        // Query tickets for this agency (shown in alerts tab)
        $ticketsQuery = YeuCauCaLam::where('diem_ban_id', $this->agency->id)
            ->where('loai_yeu_cau', 'ticket')
            ->with(['nguoiDung']);

        // Filter tickets by status if needed
        if ($this->statusFilter === 'processed') {
            $ticketsQuery->where('trang_thai', 'da_duyet');
        } elseif ($this->statusFilter === 'pending') {
            $ticketsQuery->where('trang_thai', 'cho_duyet');
        }

        // Filter by search
        if ($this->search) {
            $ticketsQuery->where(function ($q) {
                $q->where('ly_do', 'like', '%' . $this->search . '%')
                    ->orWhere('ghi_chu', 'like', '%' . $this->search . '%');
            });
        }

        $tickets = $ticketsQuery->orderBy('created_at', 'desc')->get();

        // Count pending tickets for the badge (not yet completed = cho_duyet or dang_xu_ly)
        $pendingTicketCount = YeuCauCaLam::where('diem_ban_id', $this->agency->id)
            ->where('loai_yeu_cau', 'ticket')
            ->whereIn('trang_thai', ['cho_duyet', 'dang_xu_ly'])
            ->count();

        $locations = AgencyLocation::where('diem_ban_id', $this->agency->id)
            ->orderBy('ma_vi_tri')
            ->get();

        // Count urgent notes (near reminder < 10 days) per type
        $urgentNoteCounts = [];
        foreach ($noteTypes as $type) {
            // We need to count notes of this type that are active (da_xu_ly = false) AND isNearReminder
            // Since isNearReminder is a model method using computed date diff, we can't easily query it directly in SQL efficienty without raw DB.
            // But we can approximate in SQL: where ngay_nhac_nho <= now + 10 days
            $count = AgencyNote::where('diem_ban_id', $this->agency->id)
                ->where('loai', $type->ma_loai)
                ->where('da_xu_ly', false)
                ->whereNotNull('ngay_nhac_nho')
                ->whereDate('ngay_nhac_nho', '<=', now()->addDays(10))
                ->count();

            if ($count > 0) {
                $urgentNoteCounts[$type->ma_loai] = $count;
            }
        }

        return view('livewire.admin.agency.agency-detail', [
            'notes' => $notes,
            'tickets' => $tickets,
            'noteTypes' => $noteTypes,
            'locations' => $locations,
            'pendingTicketCount' => $pendingTicketCount,
            'urgentNoteCounts' => $urgentNoteCounts,
        ]);
    }
}
