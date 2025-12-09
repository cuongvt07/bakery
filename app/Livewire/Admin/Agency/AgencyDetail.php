<?php

namespace App\Livewire\Admin\Agency;

use App\Models\Agency;
use App\Models\AgencyNote;
use App\Models\NoteType;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class AgencyDetail extends Component
{
    public Agency $agency;
    public $activeTab = 'all';
    public $showTypeModal = false;
    public $showLocationModal = false;

    public function mount($id)
    {
        $this->agency = Agency::with('notes')->findOrFail($id);
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
            ->where('hien_thi', true)
            ->orderBy('thu_tu')
            ->get();

        $query = $this->agency->notes();

        // Filter by tab
        if ($this->activeTab !== 'all') {
            $query->where('loai', $this->activeTab);
        }

        $notes = $query->orderBy('da_xu_ly')
                      ->orderBy('muc_do_quan_trong', 'desc')
                      ->orderBy('ngay_nhac_nho')
                      ->get();

        return view('livewire.admin.agency.agency-detail', [
            'notes' => $notes,
            'noteTypes' => $noteTypes,
        ]);
    }
}
