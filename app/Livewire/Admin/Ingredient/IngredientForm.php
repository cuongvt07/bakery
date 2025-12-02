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
    public $don_vi_tinh = 'kg';
    public $ton_kho_hien_tai = 0;
    public $ton_kho_toi_thieu = 0;

    public function mount($id = null)
    {
        if ($id) {
            $this->ingredient = Ingredient::findOrFail($id);
            $this->ma_nguyen_lieu = $this->ingredient->ma_nguyen_lieu;
            $this->ten_nguyen_lieu = $this->ingredient->ten_nguyen_lieu;
            $this->don_vi_tinh = $this->ingredient->don_vi_tinh;
            $this->ton_kho_hien_tai = $this->ingredient->ton_kho_hien_tai;
            $this->ton_kho_toi_thieu = $this->ingredient->ton_kho_toi_thieu;
        }
    }

    public function save()
    {
        $this->validate([
            'ma_nguyen_lieu' => 'required|unique:nguyen_lieu,ma_nguyen_lieu,' . ($this->ingredient->id ?? 'NULL'),
            'ten_nguyen_lieu' => 'required|min:2',
            'don_vi_tinh' => 'required',
            'ton_kho_hien_tai' => 'required|numeric|min:0',
            'ton_kho_toi_thieu' => 'required|numeric|min:0',
        ]);

        $data = [
            'ma_nguyen_lieu' => $this->ma_nguyen_lieu,
            'ten_nguyen_lieu' => $this->ten_nguyen_lieu,
            'don_vi_tinh' => $this->don_vi_tinh,
            'ton_kho_hien_tai' => $this->ton_kho_hien_tai,
            'ton_kho_toi_thieu' => $this->ton_kho_toi_thieu,
        ];

        if ($this->ingredient) {
            $this->ingredient->update($data);
            session()->flash('message', 'Cập nhật nguyên liệu thành công.');
        } else {
            Ingredient::create($data);
            session()->flash('message', 'Thêm mới nguyên liệu thành công.');
        }

        return redirect()->route('admin.ingredients.index');
    }

    public function render()
    {
        return view('livewire.admin.ingredient.ingredient-form');
    }
}
