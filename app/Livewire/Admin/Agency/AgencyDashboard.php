<?php

namespace App\Livewire\Admin\Agency;

use App\Models\Agency;
use App\Models\AgencyNote;
use App\Models\NoteType;
use App\Models\AgencyLocation;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class AgencyDashboard extends Component
{
    public $statusFilter = 'all'; // all, ok, warning, critical
    
    // Note form modal
    public $showNoteModal = false;
    public $editingNoteId = null;
    public $selectedAgencyId = null;
    
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

    public function openAddNoteModal($agencyId)
    {
        $this->reset(['editingNoteId', 'loai', 'tieu_de', 'noi_dung', 'ngay_nhac_nho', 'muc_do_quan_trong', 'vi_tri_id', 'mo_ta_vi_tri', 'dia_diem', 'ma_vat_dung']);
        $this->selectedAgencyId = $agencyId;
        $this->showNoteModal = true;
    }

    public function saveNote()
    {
        $this->validate([
            'loai' => 'required',
            'tieu_de' => 'required|string|max:200',
        ]);

        $data = [
            'diem_ban_id' => $this->selectedAgencyId,
            'loai' => $this->loai,
            'tieu_de' => $this->tieu_de,
            'noi_dung' => $this->noi_dung,
            'ngay_nhac_nho' => $this->ngay_nhac_nho,
            'muc_do_quan_trong' => $this->muc_do_quan_trong,
            'da_xu_ly' => false,
            'nguoi_tao_id' => auth()->id(),
        ];

        // Add location-specific fields if location selected
        if ($this->vi_tri_id) {
            $data['vi_tri_id'] = $this->vi_tri_id;
            $data[' metadata'] = [
                'mo_ta_vi_tri' => $this->mo_ta_vi_tri,
                'dia_diem' => $this->dia_diem,
                'ma_vat_dung' => $this->ma_vat_dung,
            ];
        }

        AgencyNote::create($data);
        
        $this->showNoteModal = false;
        session()->flash('message', 'Thêm ghi chú thành công');
    }

    public function render()
    {
        $agencies = Agency::with(['notes' => function($q) {
            $q->where('da_xu_ly', false)
              ->orderBy('muc_do_quan_trong', 'desc')
              ->orderBy('ngay_nhac_nho');
        }])->where('trang_thai', 'hoat_dong')
          ->get();

        // Calculate status for each agency
        $agencies = $agencies->map(function($agency) {
            $overdueCount = $agency->notes->filter(fn($note) => $note->isOverdue())->count();
            $urgentCount = $agency->notes->where('muc_do_quan_trong', 'khan_cap')->count();
            $highCount = $agency->notes->where('muc_do_quan_trong', 'cao')->count();
            
            // Determine overall status
            if ($overdueCount > 0 || $urgentCount > 0) {
                $agency->status = 'critical'; // Red
                $agency->statusColor = 'red';
                $agency->statusLabel = '🔴 Cần xử lý';
            } elseif ($highCount > 0) {
                $agency->status = 'warning'; // Yellow
                $agency->statusColor = 'yellow';
                $agency->statusLabel = '🟡 Cảnh báo';
            } else {
                $agency->status = 'ok'; // Green
                $agency->statusColor = 'green';
                $agency->statusLabel = '🟢 Ổn định';
            }
            
            $agency->overdueCount = $overdueCount;
            $agency->pendingCount = $agency->notes->count();
            
            return $agency;
        });

        // Apply filter
        if ($this->statusFilter !== 'all') {
            $agencies = $agencies->where('status', $this->statusFilter);
        }

        // Get note types and locations for modal form
        $noteTypes = $this->selectedAgencyId 
            ? NoteType::where('diem_ban_id', $this->selectedAgencyId)->get() 
            : collect();
        
        $locations = $this->selectedAgencyId 
            ? AgencyLocation::where('diem_ban_id', $this->selectedAgencyId)->orderBy('ma_vi_tri')->get()
            : collect();

        return view('livewire.admin.agency.agency-dashboard', [
            'agencies' => $agencies,
            'noteTypes' => $noteTypes,
            'locations' => $locations,
        ]);
    }
}
