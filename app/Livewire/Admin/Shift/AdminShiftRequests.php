<?php

namespace App\Livewire\Admin\Shift;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\YeuCauCaLam;
use App\Models\ShiftSchedule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        $this->selectedRequest = YeuCauCaLam::with(['user', 'shift.agency'])->find($requestId);
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
        if (!$this->selectedRequest) return;

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
                    'diem_ban_id' => $this->selectedRequest->user->diem_ban_id ?? 1, // TODO: Get from assignment
                    'nguoi_dung_id' => $this->selectedRequest->nguoi_dung_id,
                    'ngay_lam' => $this->selectedRequest->ngay_mong_muon,
                    'gio_bat_dau' => $this->selectedRequest->gio_bat_dau,
                    'gio_ket_thuc' => $this->selectedRequest->gio_ket_thuc,
                    'trang_thai' => 'chua_bat_dau',
                    'ghi_chu' => 'Tạo từ yêu cầu #' . $this->selectedRequest->id,
                ]);
            }

            // TODO: Send notification to employee
        });

        session()->flash('message', 'Đã duyệt yêu cầu thành công');
        $this->closeModal();
    }

    public function rejectRequest()
    {
        if (!$this->selectedRequest) return;

        $this->validate([
            'approvalNote' => 'required|min:10',
        ], [
            'approvalNote.required' => 'Vui lòng nhập lý do từ chối',
            'approvalNote.min' => 'Lý do phải ít nhất 10 ký tự',
        ]);

        $this->selectedRequest->update([
            'trang_thai' => 'tu_choi',
            'nguoi_duyet_id' => Auth::id(),
            'ngay_duyet' => now(),
            'ghi_chu_duyet' => $this->approvalNote,
        ]);

        // TODO: Send notification to employee

        session()->flash('message', 'Đã từ chối yêu cầu');
        $this->closeModal();
    }

    public function bulkApprove($requestIds)
    {
        YeuCauCaLam::whereIn('id', $requestIds)
            ->where('trang_thai', 'cho_duyet')
            ->update([
                'trang_thai' => 'da_duyet',
                'nguoi_duyet_id' => Auth::id(),
                'ngay_duyet' => now(),
                'ghi_chu_duyet' => 'Duyệt hàng loạt',
            ]);

        session()->flash('message', 'Đã duyệt ' . count($requestIds) . ' yêu cầu');
    }

    public function render()
    {
        $query = YeuCauCaLam::with(['user', 'shift.agency']);

        if ($this->filterStatus) {
            $query->where('trang_thai', $this->filterStatus);
        }

        if ($this->filterType) {
            $query->where('loai_yeu_cau', $this->filterType);
        }

        if ($this->searchTerm) {
            $query->whereHas('user', function($q) {
                $q->where('ho_ten', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('ma_nhan_vien', 'like', '%' . $this->searchTerm . '%');
            });
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(20);

        // Statistics
        $stats = [
            'pending' => YeuCauCaLam::where('trang_thai', 'cho_duyet')->count(),
            'approved' => YeuCauCaLam::where('trang_thai', 'da_duyet')->whereDate('ngay_duyet', Carbon::today())->count(),
            'rejected' => YeuCauCaLam::where('trang_thai', 'tu_choi')->whereDate('ngay_duyet', Carbon::today())->count(),
            'total_today' => YeuCauCaLam::whereDate('created_at', Carbon::today())->count(),
        ];

        return view('livewire.admin.shift.admin-shift-requests', [
            'requests' => $requests,
            'stats' => $stats,
        ]);
    }
}
