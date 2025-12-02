<?php

namespace App\Livewire\Admin\Supplier;

use App\Models\Supplier;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class SupplierForm extends Component
{
    public ?Supplier $supplier = null;
    public $ma_ncc = '';
    public $ten_ncc = '';
    public $so_dien_thoai = '';
    public $dia_chi = '';
    public $email = '';
    public $ghi_chu = '';

    public function mount($id = null)
    {
        if ($id) {
            $this->supplier = Supplier::findOrFail($id);
            $this->ma_ncc = $this->supplier->ma_ncc;
            $this->ten_ncc = $this->supplier->ten_ncc;
            $this->so_dien_thoai = $this->supplier->so_dien_thoai;
            $this->dia_chi = $this->supplier->dia_chi;
            $this->email = $this->supplier->email;
            $this->ghi_chu = $this->supplier->ghi_chu;
        }
    }

    public function save()
    {
        $this->validate([
            'ma_ncc' => 'required|unique:nha_cung_cap,ma_ncc,' . ($this->supplier->id ?? 'NULL'),
            'ten_ncc' => 'required|min:2',
            'email' => 'nullable|email',
        ]);

        $data = [
            'ma_ncc' => $this->ma_ncc,
            'ten_ncc' => $this->ten_ncc,
            'so_dien_thoai' => $this->so_dien_thoai,
            'dia_chi' => $this->dia_chi,
            'email' => $this->email,
            'ghi_chu' => $this->ghi_chu,
        ];

        if ($this->supplier) {
            $this->supplier->update($data);
            session()->flash('message', 'Cập nhật nhà cung cấp thành công.');
        } else {
            Supplier::create($data);
            session()->flash('message', 'Thêm mới nhà cung cấp thành công.');
        }

        return redirect()->route('admin.suppliers.index');
    }

    public function render()
    {
        return view('livewire.admin.supplier.supplier-form');
    }
}
