<?php

namespace App\Livewire\Admin\DiemBan;

use Livewire\Component;
use App\Models\Agency;
use Illuminate\Validation\Rule;

class ChinhSua extends Component
{
    public $agency;
    public $ma_diem_ban = '';
    public $ten_diem_ban = '';
    public $dia_chi = '';
    public $so_dien_thoai = '';
    public $vi_do = '';
    public $kinh_do = '';
    public $trang_thai = 'hoat_dong';
    public $ghi_chu = '';
    
    public $vat_dungs = [];
    
    public function mount($id)
    {
        $this->agency = Agency::findOrFail($id);
        
        $this->ma_diem_ban = $this->agency->ma_diem_ban;
        $this->ten_diem_ban = $this->agency->ten_diem_ban;
        $this->dia_chi = $this->agency->dia_chi;
        $this->so_dien_thoai = $this->agency->so_dien_thoai;
        $this->vi_do = $this->agency->vi_do;
        $this->kinh_do = $this->agency->kinh_do;
        $this->trang_thai = $this->agency->trang_thai;
        $this->ghi_chu = $this->agency->ghi_chu;
        
        $this->vat_dungs = $this->agency->thong_tin_vat_dung ?? [];
        
        if (empty($this->vat_dungs)) {
            $this->vat_dungs = [['ten' => '', 'so_luong' => 1, 'tinh_trang' => 'tot']];
        }
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
    
    protected function rules()
    {
        return [
            'ma_diem_ban' => ['required', 'max:50', Rule::unique('diem_ban', 'ma_diem_ban')->ignore($this->agency->id)],
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
    }
    
    public function submit()
    {
        $this->validate();
        
        $cleanVatDungs = collect($this->vat_dungs)
            ->filter(fn($item) => !empty($item['ten']))
            ->values()
            ->toArray();
            
        $this->agency->update([
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
        
        session()->flash('success', 'Cập nhật điểm bán thành công!');
        return redirect()->route('admin.diemban.index');
    }
    
    public function render()
    {
        return view('livewire.admin.diem-ban.chinh-sua')
            ->layout('components.layouts.admin', [
                'pageTitle' => 'Chỉnh sửa điểm bán',
                'breadcrumbs' => [
                    ['label' => 'Điểm bán', 'url' => route('admin.diemban.index')],
                    ['label' => 'Chỉnh sửa', 'url' => '#'],
                    ['label' => $this->ten_diem_ban],
                ]
            ]);
    }
}
