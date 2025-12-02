<?php

namespace App\Livewire\Admin\Agency;

use App\Models\Agency;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class AgencyForm extends Component
{
    public ?Agency $agency = null;
    public $ma_diem_ban = '';
    public $ten_diem_ban = '';
    public $dia_chi = '';
    public $so_dien_thoai = '';
    public $trang_thai = 'hoat_dong';
    public $ghi_chu = '';

    public function mount($id = null)
    {
        if ($id) {
            $this->agency = Agency::findOrFail($id);
            $this->ma_diem_ban = $this->agency->ma_diem_ban;
            $this->ten_diem_ban = $this->agency->ten_diem_ban;
            $this->dia_chi = $this->agency->dia_chi;
            $this->so_dien_thoai = $this->agency->so_dien_thoai;
            $this->trang_thai = $this->agency->trang_thai;
            $this->ghi_chu = $this->agency->ghi_chu;
        }
    }

    public function save()
    {
        $this->validate([
            'ma_diem_ban' => 'required|unique:diem_ban,ma_diem_ban,' . ($this->agency->id ?? 'NULL'),
            'ten_diem_ban' => 'required',
            'dia_chi' => 'required',
            'trang_thai' => 'required|in:hoat_dong,dong_cua',
        ]);

        $data = [
            'ma_diem_ban' => $this->ma_diem_ban,
            'ten_diem_ban' => $this->ten_diem_ban,
            'dia_chi' => $this->dia_chi,
            'so_dien_thoai' => $this->so_dien_thoai,
            'trang_thai' => $this->trang_thai,
            'ghi_chu' => $this->ghi_chu,
        ];

        if ($this->agency) {
            $this->agency->update($data);
            session()->flash('message', 'Cập nhật điểm bán thành công.');
        } else {
            Agency::create($data);
            session()->flash('message', 'Thêm mới điểm bán thành công.');
        }

        return redirect()->route('admin.agencies.index');
    }

    public function render()
    {
        return view('livewire.admin.agency.agency-form');
    }
}
