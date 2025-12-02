<?php

namespace App\Livewire\Admin\DiemBan;

use Livewire\Component;
use App\Models\Agency;
use Illuminate\Support\Str;

class ThemMoi extends Component
{
    public $ma_diem_ban = '';
    public $ten_diem_ban = '';
    public $dia_chi = '';
    public $so_dien_thoai = '';
    public $vi_do = '';
    public $kinh_do = '';
    public $trang_thai = 'hoat_dong';
    public $ghi_chu = '';
    
    // Dynamic list for equipment
    public $vat_dungs = [
        ['ten' => '', 'so_luong' => 1, 'tinh_trang' => 'tot']
    ];
    
    public function mount()
    {
        // Auto-generate code
        $this->ma_diem_ban = 'DB-' . strtoupper(Str::random(5));
    }
    
    public function addVatDung()
    {
        $this->vat_dungs[] = ['ten' => '', 'so_luong' => 1, 'tinh_trang' => 'tot'];
    }
    
    public function removeVatDung($index)
    {
        unset($this->vat_dungs[$index]);
        $this->vat_dungs = array_values($this->vat_dungs);
    }
    
    protected $rules = [
        'ma_diem_ban' => 'required|unique:diem_ban,ma_diem_ban|max:50',
        'ten_diem_ban' => 'required|string|max:100',
        'dia_chi' => 'required|string',
        'so_dien_thoai' => 'nullable|string|max:20',
        'vi_do' => 'nullable|numeric|between:-90,90',
        'kinh_do' => 'nullable|numeric|between:-180,180',
        'trang_thai' => 'required|in:hoat_dong,tam_ngung,dong_cua',
        'ghi_chu' => 'nullable|string',
        'vat_dungs.*.ten' => 'nullable|string',
        'vat_dungs.*.so_luong' => 'nullable|integer|min:1',
    ];
    
    public function submit()
    {
        $this->validate();
        
        // Filter empty equipment
        $cleanVatDungs = collect($this->vat_dungs)
            ->filter(fn($item) => !empty($item['ten']))
            ->values()
            ->toArray();
            
        Agency::create([
            'ma_diem_ban' => $this->ma_diem_ban,
            'ten_diem_ban' => $this->ten_diem_ban,
            'dia_chi' => $this->dia_chi,
            'so_dien_thoai' => $this->so_dien_thoai,
            'vi_do' => $this->vi_do ?: null,
            'kinh_do' => $this->kinh_do ?: null,
            'trang_thai' => $this->trang_thai,
            'ghi_chu' => $this->ghi_chu,
            'thong_tin_vat_dung' => $cleanVatDungs,
        ]);
        
        session()->flash('success', 'Thêm điểm bán thành công!');
        return redirect()->route('admin.diemban.index');
    }
    
    public function render()
    {
        return view('livewire.admin.diem-ban.them-moi')
            ->layout('components.layouts.admin', [
                'pageTitle' => 'Thêm điểm bán',
                'breadcrumbs' => [
                    ['label' => 'Điểm bán', 'url' => route('admin.diemban.index')],
                    ['label' => 'Thêm mới'],
                ]
            ]);
    }
}
