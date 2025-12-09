<?php

namespace App\Livewire\Admin\Agency;

use App\Models\AgencyLocation;
use App\Models\Agency;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class LocationList extends Component
{
    use WithPagination;

    public Agency $agency;
    public $search = '';
    public $showModal = false;
    public $editingLocation = null;

    // Form fields
    public $ma_vi_tri = '';
    public $ten_vi_tri = '';
    public $mo_ta = '';
    public $dia_chi = '';

    public function mount($agencyId)
    {
        $this->agency = Agency::findOrFail($agencyId);
        $this->dia_chi = $this->agency->dia_chi; // Default address
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openAddModal()
    {
        $this->reset(['ma_vi_tri', 'ten_vi_tri', 'mo_ta']);
        $this->dia_chi = $this->agency->dia_chi;
        $this->editingLocation = null;
        $this->showModal = true;
    }

    public function openEditModal($id)
    {
        $location = AgencyLocation::findOrFail($id);
        $this->editingLocation = $location;
        $this->ma_vi_tri = $location->ma_vi_tri;
        $this->ten_vi_tri = $location->ten_vi_tri;
        $this->mo_ta = $location->mo_ta;
        $this->dia_chi = $location->dia_chi;
        $this->showModal = true;
    }

    public function updatedTenViTri($value)
    {
        // Auto-generate ma_vi_tri from ten_vi_tri if not editing
        if (!$this->editingLocation && $value) {
            $this->ma_vi_tri = $this->generateLocationCode($value);
        }
    }

    private function generateLocationCode($text)
    {
        // Extract first characters and numbers
        preg_match_all('/[A-Z]+|[0-9]+/', strtoupper($text), $matches);
        $code = implode('', $matches[0]);
        
        if (empty($code)) {
            // Fallback: use first 3 chars
            $code = strtoupper(substr($text, 0, 3));
        }
        
        // Ensure uniqueness by adding counter
        $base = substr($code, 0, 10);
        $counter = 1;
        $finalCode = $base;
        
        while (AgencyLocation::where('diem_ban_id', $this->agency->id)
                             ->where('ma_vi_tri', $finalCode)
                             ->exists()) {
            $finalCode = $base . $counter;
            $counter++;
        }
        
        return $finalCode;
    }

    public function save()
    {
        $this->validate([
            'ma_vi_tri' => 'required|string|max:20',
            'ten_vi_tri' => 'required|string|max:100',
        ]);

        $data = [
            'diem_ban_id' => $this->agency->id,
            'ma_vi_tri' => strtoupper(trim($this->ma_vi_tri)),
            'ten_vi_tri' => $this->ten_vi_tri,
            'mo_ta' => $this->mo_ta,
            'dia_chi' => $this->dia_chi,
        ];

        if ($this->editingLocation) {
            $this->editingLocation->update($data);
            session()->flash('message', 'Cập nhật vị trí thành công.');
        } else {
            AgencyLocation::create($data);
            session()->flash('message', 'Thêm vị trí mới thành công.');
        }

        $this->showModal = false;
        $this->reset(['ma_vi_tri', 'ten_vi_tri', 'mo_ta']);
    }

    public function delete($id)
    {
        $location = AgencyLocation::findOrFail($id);
        
        // Check if location is being used
        if ($location->notes()->count() > 0) {
            session()->flash('error', 'Không thể xóa vị trí đang được sử dụng.');
            return;
        }

        $location->delete();
        session()->flash('message', 'Xóa vị trí thành công.');
    }

    public function render()
    {
        $locations = AgencyLocation::where('diem_ban_id', $this->agency->id)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('ma_vi_tri', 'like', '%' . $this->search . '%')
                      ->orWhere('ten_vi_tri', 'like', '%' . $this->search . '%')
                      ->orWhere('mo_ta', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('ma_vi_tri')
            ->paginate(20);

        return view('livewire.admin.agency.location-list', [
            'locations' => $locations,
        ]);
    }
}
