<?php

namespace App\Livewire\Admin\Agency;

use App\Models\Agency;
use App\Models\AgencyNote;
use App\Models\NoteType;
use App\Models\AgencyLocation;
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
        if (!$this->agency) abort(404);
    }
    
    // Reset pagination when filtering
    public function updatedSearch() { $this->resetPage(); }
    public function updatedStatusFilter() { $this->resetPage(); }
    public function updatedActiveTab() { $this->resetPage(); }

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
        $this->ngay_nhac_nho = $note->ngay_nhac_nho?->format('Y-m-d');
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
        $note = AgencyNote::findOrFail($noteId);
        if ($note->diem_ban_id === $this->agency->id) {
            $note->delete();
            session()->flash('success', 'Đã xóa ghi chú');
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
            $query->where(function($q) {
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

        $locations = AgencyLocation::where('diem_ban_id', $this->agency->id)
            ->orderBy('ma_vi_tri')
            ->get();

        return view('livewire.admin.agency.agency-detail', [
            'notes' => $notes,
            'noteTypes' => $noteTypes,
            'locations' => $locations,
        ]);
    }
}
