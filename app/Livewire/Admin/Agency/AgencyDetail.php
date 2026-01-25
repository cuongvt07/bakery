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
     * Resolve/complete a ticket
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

            // Notify the store (Broadcast)
            try {
                $agencyName = $this->agency->ten_diem_ban ?? 'Cửa hàng';
                $adminName = \Illuminate\Support\Facades\Auth::user()->ho_ten ?? 'Admin';

                $message = "Ticket '{$ticket->ly_do}' tại {$agencyName} đã được xử lý bởi {$adminName}.";
                if ($this->approvalNote) {
                    $message .= "\nGhi chú: " . $this->approvalNote;
                }

                app(\App\Services\NotificationService::class)->sendToStore(
                    $ticket->diem_ban_id,
                    'Thông báo xử lý Ticket',
                    $message,
                    'he_thong'
                );
            } catch (\Exception $e) {
                \Log::error('Error sending ticket broadcast: ' . $e->getMessage());
            }

            session()->flash('success', 'Đã xử lý ticket thành công');
            $this->closeTicketModal();
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
            $ticketsQuery->where('trang_thai', 'approved');
        } elseif ($this->statusFilter === 'pending') {
            $ticketsQuery->where('trang_thai', 'pending');
        }

        // Filter by search
        if ($this->search) {
            $ticketsQuery->where(function ($q) {
                $q->where('ly_do', 'like', '%' . $this->search . '%')
                    ->orWhere('ghi_chu', 'like', '%' . $this->search . '%');
            });
        }

        $tickets = $ticketsQuery->orderBy('created_at', 'desc')->get();

        // Count pending tickets for the badge
        $pendingTicketCount = YeuCauCaLam::where('diem_ban_id', $this->agency->id)
            ->where('loai_yeu_cau', 'ticket')
            ->where('trang_thai', 'cho_duyet')
            ->count();

        $locations = AgencyLocation::where('diem_ban_id', $this->agency->id)
            ->orderBy('ma_vi_tri')
            ->get();

        return view('livewire.admin.agency.agency-detail', [
            'notes' => $notes,
            'tickets' => $tickets,
            'noteTypes' => $noteTypes,
            'locations' => $locations,
            'pendingTicketCount' => $pendingTicketCount,
        ]);
    }
}
