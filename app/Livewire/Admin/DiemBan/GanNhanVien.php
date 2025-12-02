<?php

namespace App\Livewire\Admin\DiemBan;

use Livewire\Component;
use App\Models\Agency;
use App\Models\User;

class GanNhanVien extends Component
{
    public $agency;
    public $selectedUsers = [];
    public $ngay_bat_dau;
    
    public function mount($id)
    {
        $this->agency = Agency::findOrFail($id);
        $this->ngay_bat_dau = now()->format('Y-m-d');
        
        // Load currently assigned users
        $this->selectedUsers = $this->agency->nhanVien->pluck('id')->toArray();
    }
    
    public function submit()
    {
        $this->validate([
            'selectedUsers' => 'required|array|min:1',
            'ngay_bat_dau' => 'required|date',
        ]);
        
        // Sync users with pivot data
        $pivotData = [];
        foreach ($this->selectedUsers as $userId) {
            $pivotData[$userId] = [
                'ngay_bat_dau' => $this->ngay_bat_dau,
                'trang_thai' => 'dang_lam_viec'
            ];
        }
        
        $this->agency->nhanVien()->sync($pivotData);
        
        session()->flash('success', 'Cập nhật nhân viên điểm bán thành công!');
        return redirect()->route('admin.diemban.show', $this->agency->id);
    }
    
    public function render()
    {
        // Get all staff users
        $users = User::where('vai_tro', 'nhan_vien')
                     ->where('trang_thai', 'hoat_dong')
                     ->orderBy('ho_ten')
                     ->get();
                     
        return view('livewire.admin.diem-ban.gan-nhan-vien', [
            'users' => $users
        ])->layout('components.layouts.admin', [
            'pageTitle' => 'Gán nhân viên',
            'breadcrumbs' => [
                ['label' => 'Điểm bán', 'url' => route('admin.diemban.index')],
                ['label' => $this->agency->ten_diem_ban, 'url' => route('admin.diemban.show', $this->agency->id)],
                ['label' => 'Gán nhân viên'],
            ]
        ]);
    }
}
