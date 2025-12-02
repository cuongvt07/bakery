<?php

namespace App\Livewire\Admin\DiemBan;

use Livewire\Component;
use App\Models\Agency;
use App\Models\CaLamViec;

class ChiTiet extends Component
{
    public $agency;
    public $activeTab = 'thong_tin'; // thong_tin, nhan_vien, ca_lam
    
    public function mount($id)
    {
        $this->agency = Agency::with('nhanVien')->findOrFail($id);
    }
    
    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }
    
    public function render()
    {
        $caLamViecs = [];
        
        if ($this->activeTab === 'ca_lam') {
            $caLamViecs = CaLamViec::with('nguoiDung')
                                   ->where('diem_ban_id', $this->agency->id)
                                   ->whereDate('ngay_lam', '>=', now())
                                   ->orderBy('ngay_lam')
                                   ->limit(10)
                                   ->get();
        }
        
        return view('livewire.admin.diem-ban.chi-tiet', [
            'caLamViecs' => $caLamViecs,
        ])->layout('components.layouts.admin', [
            'pageTitle' => 'Chi tiết điểm bán',
            'breadcrumbs' => [
                ['label' => 'Điểm bán', 'url' => route('admin.diemban.index')],
                ['label' => 'Chi tiết', 'url' => '#'],
                ['label' => $this->agency->ten_diem_ban],
            ]
        ]);
    }
}
