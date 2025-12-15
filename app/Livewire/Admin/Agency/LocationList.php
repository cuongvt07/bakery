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
    
    // Duplicate tracking
    public $isDuplicate = false;
    public $duplicateMessage = '';

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
            // IMPORTANT: Check duplicate after auto-generation
            $this->checkDuplicate();
        }
    }
    
    public function updatedMaViTri($value)
    {
        // Debug: Log to verify this is being called
        \Log::info('updatedMaViTri called with value: ' . $value);
        
        // Normalize: trim and uppercase
        $this->ma_vi_tri = strtoupper(trim($value));
        
        // Check for duplicates
        $this->checkDuplicate();
    }
    
    private function checkDuplicate()
    {
        // If empty but was duplicate before, keep the warning
        if (empty($this->ma_vi_tri)) {
            // Don't reset isDuplicate here - let it persist until user enters valid code
            if (!$this->isDuplicate) {
                $this->duplicateMessage = '';
            }
            return;
        }
        
        $query = AgencyLocation::where('diem_ban_id', $this->agency->id)
            ->where('ma_vi_tri', $this->ma_vi_tri);
        
        // If editing, exclude current location from check
        if ($this->editingLocation) {
            $query->where('id', '!=', $this->editingLocation->id);
        }
        
        $exists = $query->exists();
        
        if ($exists) {
            $this->isDuplicate = true;
            $this->duplicateMessage = 'Mã vị trí "' . $this->ma_vi_tri . '" đã tồn tại. Vui lòng nhập mã khác.';
            // Clear the input to force user to enter a different code
            $this->ma_vi_tri = '';
        } else {
            // Only reset when user enters a valid unique code
            $this->isDuplicate = false;
            $this->duplicateMessage = '';
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
        
        // Return the generated code without auto-incrementing
        // Let the duplicate check handle if it's duplicated
        return substr($code, 0, 20); // Max 20 chars as per DB
    }

    public function save()
    {
        // Prevent save if duplicate
        if ($this->isDuplicate) {
            session()->flash('error', 'Không thể lưu: Mã vị trí đã tồn tại trong đại lý.');
            return;
        }
        
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
        $this->reset(['ma_vi_tri', 'ten_vi_tri', 'mo_ta', 'isDuplicate', 'duplicateMessage']);
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
