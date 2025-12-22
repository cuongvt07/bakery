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
    public $requests = [];
    public $filterStatus = '';
    public $showRequestModal = false;
    public $requestType = ''; // xin_ca | doi_ca | xin_nghi
    public $selectedShiftId = null;
    public $requestDate = '';
    public $requestNote = '';

    public function mount()
    {
        $this->loadRequests();
    }

    public function loadRequests()
    {
        $query = YeuCauCaLam::where('nguoi_dung_id', Auth::id())
            ->with('caLamViec');
        
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
    }

    public function closeModal()
    {
        $this->showRequestModal = false;
        $this->reset(['requestType', 'selectedShiftId', 'requestDate', 'requestNote']);
    }

    public function submitRequest()
    {
        $this->validate([
            'requestType' => 'required|in:xin_ca,doi_ca,xin_nghi',
            'requestNote' => 'required',
        ], [
            'requestNote.required' => 'Vui lòng nhập lý do',
        ]);

        $data = [
            'nguoi_dung_id' => Auth::id(),
            'loai_yeu_cau' => $this->requestType,
            'ly_do' => $this->requestNote,
            'trang_thai' => 'cho_duyet',
        ];

        if ($this->selectedShiftId) {
            $data['ca_lam_viec_id'] = $this->selectedShiftId;
        }

        if ($this->requestType === 'xin_ca') {
            $data['ngay_mong_muon'] = $this->requestDate;
        }

        $request = YeuCauCaLam::create($data);

        // Log activity
        NhatKyHoatDong::logActivity(
            action: 'gui_yeu_cau',
            newData: $request->toArray(),
            description: 'Gửi yêu cầu: ' . $this->requestType . ' (' . $this->requestNote . ')'
        );

        session()->flash('message', 'Đã gửi yêu cầu thành công!');
        
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

    public function render()
    {
        return view('livewire.employee.shift.shift-requests');
    }
}
