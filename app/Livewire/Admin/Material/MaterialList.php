<?php

namespace App\Livewire\Admin\Material;

use App\Models\AgencyNote;
use App\Models\Agency;
use App\Models\AgencyLocation;
use Livewire\Component;
use Livewire\WithPagination;

class MaterialList extends Component
{
    use WithPagination;

    // Tab system
    public $activeTab = 'materials'; // 'materials' or 'locations'

    // Filters
    public $filterAgency = '';
    public $filterLocation = '';
    
    // Sort
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    
    // Search
    public $search = '';

    // Location form
    public $showLocationModal = false;
    public $editingLocation = null;
    public $ma_vi_tri = '';
    public $ten_vi_tri = '';
    public $location_diem_ban_id = '';
    public $mo_ta = '';
    public $dia_chi = '';

    protected $queryString = [
        'activeTab' => ['except' => 'materials'],
        'search' => ['except' => ''],
        'filterAgency' => ['except' => ''],
        'filterLocation' => ['except' => ''],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterAgency()
    {
        $this->resetPage();
        $this->filterLocation = ''; // Reset location filter when agency changes
    }

    public function updatingFilterLocation()
    {
        $this->resetPage();
    }

    public function updatingActiveTab()
    {
        $this->reset(['search', 'filterAgency', 'filterLocation']);
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function clearFilters()
    {
        $this->reset(['filterAgency', 'filterLocation', 'search']);
        $this->resetPage();
    }

    public function delete($itemId)
    {
        AgencyNote::findOrFail($itemId)->delete();
        session()->flash('message', 'Đã xóa vật tư');
    }

    // ========== Location Management Methods ==========
    
    public function openAddLocationModal()
    {
        $this->reset(['ma_vi_tri', 'ten_vi_tri', 'mo_ta', 'dia_chi', 'location_diem_ban_id']);
        $this->editingLocation = null;
        $this->showLocationModal = true;
    }

    public function openEditLocationModal($id)
    {
        $location = AgencyLocation::findOrFail($id);
        $this->editingLocation = $location->id;
        $this->ma_vi_tri = $location->ma_vi_tri;
        $this->ten_vi_tri = $location->ten_vi_tri;
        $this->mo_ta = $location->mo_ta;
        $this->dia_chi = $location->dia_chi;
        $this->location_diem_ban_id = $location->diem_ban_id;
        $this->showLocationModal = true;
    }

    public function updatedLocationDiemBanId($value)
    {
        // Auto-fill address from selected agency
        if ($value) {
            $agency = Agency::find($value);
            if ($agency) {
                $this->dia_chi = $agency->dia_chi;
            }
        }
    }

    public function updatedMaViTri($value)
    {
        // Auto uppercase
        $this->ma_vi_tri = strtoupper($value);
    }

    public function saveLocation()
    {
        $this->validate([
            'location_diem_ban_id' => 'required|exists:diem_ban,id',
            'ma_vi_tri' => 'required|string|max:20',
            'ten_vi_tri' => 'required|string|max:100',
        ]);

        $data = [
            'diem_ban_id' => $this->location_diem_ban_id,
            'ma_vi_tri' => strtoupper(trim($this->ma_vi_tri)),
            'ten_vi_tri' => $this->ten_vi_tri,
            'mo_ta' => $this->mo_ta,
            'dia_chi' => $this->dia_chi,
        ];

        if ($this->editingLocation) {
            AgencyLocation::find($this->editingLocation)->update($data);
            session()->flash('message', 'Cập nhật vị trí thành công');
        } else {
            AgencyLocation::create($data);
            session()->flash('message', 'Thêm vị trí mới thành công');
        }

        $this->showLocationModal = false;
        $this->reset(['ma_vi_tri', 'ten_vi_tri', 'mo_ta', 'dia_chi', 'location_diem_ban_id', 'editingLocation']);
    }

    public function deleteLocation($id)
    {
        $location = AgencyLocation::findOrFail($id);
        
        if ($location->notes()->count() > 0) {
            session()->flash('error', 'Không thể xóa vị trí đang được sử dụng');
            return;
        }

        $location->delete();
        session()->flash('message', 'Xóa vị trí thành công');
    }


    public function render()
    {
        $agencies = Agency::orderBy('ten_diem_ban')->get();
        
        if ($this->activeTab === 'locations') {
            // Locations tab
            $query = AgencyLocation::query()->with('agency');

            // Search
            if ($this->search) {
                $query->where(function ($q) {
                    $q->where('ma_vi_tri', 'like', '%' . $this->search . '%')
                      ->orWhere('ten_vi_tri', 'like', '%' . $this->search . '%')
                      ->orWhere('mo_ta', 'like', '%' . $this->search . '%');
                });
            }

            // Filter by agency
            if ($this->filterAgency) {
                $query->where('diem_ban_id', $this->filterAgency);
            }

            // Sort
            if ($this->sortField === 'dai_ly') {
                $query->join('diem_ban', 'vi_tri_diem_ban.diem_ban_id', '=', 'diem_ban.id')
                      ->orderBy('diem_ban.ten_diem_ban', $this->sortDirection)
                      ->select('vi_tri_diem_ban.*');
            } else {
                $query->orderBy($this->sortField, $this->sortDirection);
            }

            $locations = $query->paginate(20);

            return view('livewire.admin.material.material-list', [
                'materials' => null,
                'locations' => $locations,
                'agencies' => $agencies,
            ]);
        }

        // Materials tab (default)
        $query = AgencyNote::where('loai', 'vat_dung')
            ->with(['agency', 'location']);

        // Apply search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('tieu_de', 'like', '%' . $this->search . '%')
                  ->orWhere('metadata->ma_vat_dung', 'like', '%' . $this->search . '%');
            });
        }

        // Apply filters (AND logic)
        if ($this->filterAgency) {
            $query->where('diem_ban_id', $this->filterAgency);
        }

        if ($this->filterLocation) {
            $query->where('vi_tri_id', $this->filterLocation);
        }

        // Apply sorting
        if ($this->sortField === 'ten_vat_tu') {
            $query->orderBy('tieu_de', $this->sortDirection);
        } elseif ($this->sortField === 'ma_vat_tu') {
            $query->orderBy('metadata->ma_vat_dung', $this->sortDirection);
        } elseif ($this->sortField === 'dai_ly') {
            $query->join('diem_ban', 'ghi_chu_dai_ly.diem_ban_id', '=', 'diem_ban.id')
                  ->orderBy('diem_ban.ten_diem_ban', $this->sortDirection)
                  ->select('ghi_chu_dai_ly.*');
        } else {
            $query->orderBy($this->sortField, $this->sortDirection);
        }

        $materials = $query->paginate(20);

        $locations = AgencyLocation::query()
            ->when($this->filterAgency, function ($q) {
                $q->where('diem_ban_id', $this->filterAgency);
            })
            ->orderBy('ma_vi_tri')
            ->get();

        return view('livewire.admin.material.material-list', [
            'materials' => $materials,
            'locations' => $locations,
            'agencies' => $agencies,
        ]);
    }
}
