<?php

namespace App\Livewire\Admin\Ingredient;

use App\Models\Ingredient;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class IngredientForm extends Component
{
    public ?Ingredient $ingredient = null;
    public $ma_nguyen_lieu = '';
    public $ten_nguyen_lieu = '';
    public $don_vi_tinh = '';
    public $ton_kho_hien_tai = '';
    public $ton_kho_toi_thieu = '';
    public $gia_nhap = '';
    public $tong_tien_nhap = ''; // Temporary field for input

    public function mount($id = null)
    {
        if ($id) {
            $this->ingredient = Ingredient::findOrFail($id);
            $this->ma_nguyen_lieu = $this->ingredient->ma_nguyen_lieu;
            $this->ten_nguyen_lieu = $this->ingredient->ten_nguyen_lieu;
            $this->don_vi_tinh = $this->ingredient->don_vi_tinh;
            $this->ton_kho_hien_tai = $this->ingredient->ton_kho_hien_tai;
            $this->ton_kho_toi_thieu = $this->ingredient->ton_kho_toi_thieu;
            $this->gia_nhap = $this->ingredient->gia_nhap;
            // Calculate reverse for editing
            $this->tong_tien_nhap = $this->gia_nhap * $this->ton_kho_hien_tai;
        }
    }

    public function save()
    {
        $this->validate([
            'ten_nguyen_lieu' => 'required|min:2',
            'don_vi_tinh' => 'required',
            'ton_kho_hien_tai' => 'required|numeric|min:0.01',
            'ton_kho_toi_thieu' => 'required|numeric|min:0',
            'tong_tien_nhap' => 'required|numeric|min:0',
        ]);
        
        // Tự động tính giá nhập = Tổng tiền / Số lượng
        $this->gia_nhap = $this->ton_kho_hien_tai > 0 
            ? round($this->tong_tien_nhap / $this->ton_kho_hien_tai, 2)
            : 0;

        $data = [
            'ten_nguyen_lieu' => $this->ten_nguyen_lieu,
            'don_vi_tinh' => $this->don_vi_tinh,
            'ton_kho_hien_tai' => $this->ton_kho_hien_tai,
            'ton_kho_toi_thieu' => $this->ton_kho_toi_thieu,
            'gia_nhap' => $this->gia_nhap,
        ];

        if ($this->ingredient) {
            // Update existing - keep existing code
            $this->ingredient->update($data);
            session()->flash('message', 'Cập nhật nguyên liệu thành công.');
        } else {
            // Create new - auto-generate code
            $ingredient = new Ingredient($data);
            $ingredient->save(); // HasUniqueCode trait will auto-generate ma_nguyen_lieu
            session()->flash('message', 'Thêm mới nguyên liệu thành công.');
        }

        return redirect()->route('admin.ingredients.index');
    }

    public function render()
    {
        return view('livewire.admin.ingredient.ingredient-form');
    }
}
