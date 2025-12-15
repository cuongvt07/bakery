<?php

namespace App\Livewire\Admin\System;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\NhatKyHoatDong;
use App\Models\User;

#[Layout('components.layouts.app')]
class ActivityLog extends Component
{
    use WithPagination;

    public $search = '';
    public $actionFilter = '';
    public $userFilter = '';

    public function updatingSearch() { $this->resetPage(); }
    public function updatingActionFilter() { $this->resetPage(); }
    public function updatingUserFilter() { $this->resetPage(); }

    public function render()
    {
        $query = NhatKyHoatDong::with('nguoiDung')
            ->orderBy('created_at', 'desc');

        if ($this->search) {
            $query->where('mo_ta', 'like', '%' . $this->search . '%')
                  ->orWhere('hanh_dong', 'like', '%' . $this->search . '%');
        }

        if ($this->actionFilter) {
            $query->where('hanh_dong', $this->actionFilter);
        }

        if ($this->userFilter) {
            $query->where('nguoi_dung_id', $this->userFilter);
        }

        return view('livewire.admin.system.activity-log', [
            'logs' => $query->paginate(20),
            'users' => User::all(),
            'actions' => NhatKyHoatDong::select('hanh_dong')->distinct()->pluck('hanh_dong'),
        ]);
    }
}
