<?php

namespace App\Livewire\Admin\NguoiDung;

use Livewire\Component;
use App\Models\User;
use App\Models\ChamCong;
use App\Models\BangLuong;

class ChiTiet extends Component
{
    public $user;
    public $activeTab = 'thong_tin'; // thong_tin, cham_cong, luong
    
    public function mount($id)
    {
        $this->user = User::findOrFail($id);
    }
    
    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }
    
    public function render()
    {
        $chamCongs = [];
        $bangLuongs = [];
        
        if ($this->activeTab === 'cham_cong') {
            $chamCongs = ChamCong::where('nguoi_dung_id', $this->user->id)
                                ->orderBy('ngay_cham', 'desc')
                                ->limit(10)
                                ->get();
        }
        
        if ($this->activeTab === 'luong') {
            $bangLuongs = BangLuong::where('nguoi_dung_id', $this->user->id)
                                 ->orderBy('nam', 'desc')
                                 ->orderBy('thang', 'desc')
                                 ->get();
        }
        
        return view('livewire.admin.nguoi-dung.chi-tiet', [
            'chamCongs' => $chamCongs,
            'bangLuongs' => $bangLuongs,
        ])->layout('components.layouts.admin', [
            'pageTitle' => 'Chi tiết người dùng',
            'breadcrumbs' => [
                ['label' => 'Người dùng', 'url' => route('admin.nguoidung.index')],
                ['label' => 'Chi tiết', 'url' => '#'],
                ['label' => $this->user->ho_ten],
            ]
        ]);
    }
}
