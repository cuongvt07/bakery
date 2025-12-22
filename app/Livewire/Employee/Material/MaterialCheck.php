<?php

namespace App\Livewire\Employee\Material;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\AgencyNote; // The model used for Materials (loai = vat_dung)
use Illuminate\Support\Facades\Auth;
use App\Models\Agency;

#[Layout('components.layouts.mobile')]
class MaterialCheck extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedAgencyId = null;

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function mount()
    {
        $user = Auth::user();
        // Default to first assigned agency
        $firstAgency = $user->diemBan->first();
        if ($firstAgency) {
            $this->selectedAgencyId = $firstAgency->id;
        } else {
            // Fallback for demo or admin-employee
            $this->selectedAgencyId = Agency::first()->id ?? null;
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $user = Auth::user();
        $agencies = $user->diemBan; // Or all agencies if needed? Better restrict to assigned.
        
        // If no assigned agencies, maybe show all?
        if ($agencies->isEmpty()) {
             $agencies = Agency::where('trang_thai', 'hoat_dong')->get();
        }

        $query = AgencyNote::query()
            ->where('loai', 'vat_dung')
            ->with(['location', 'agency']);

        if ($this->selectedAgencyId) {
            $query->where('diem_ban_id', $this->selectedAgencyId);
        }

        if ($this->search) {
            $query->where(function($q) {
                $q->where('tieu_de', 'like', '%' . $this->search . '%')
                  ->orWhere('metadata->ma_vat_dung', 'like', '%' . $this->search . '%');
            });
        }

        $materials = $query->orderBy('tieu_de')->paginate(10);

        return view('livewire.employee.material.material-check', [
            'materials' => $materials,
            'agencies' => $agencies
        ]);
    }
}
