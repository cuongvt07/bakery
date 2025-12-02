<?php

namespace App\Livewire\Admin\ProductCategory;

use App\Models\ProductCategory;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class CategoryForm extends Component
{
    public ?ProductCategory $category = null;
    public $ten_danh_muc = '';
    public $mo_ta = '';
    public $thu_tu = 0;

    public function mount($id = null)
    {
        if ($id) {
            $this->category = ProductCategory::findOrFail($id);
            $this->ten_danh_muc = $this->category->ten_danh_muc;
            $this->mo_ta = $this->category->mo_ta;
            $this->thu_tu = $this->category->thu_tu;
        }
    }

    public function save()
    {
        $this->validate([
            'ten_danh_muc' => 'required|min:2',
            'thu_tu' => 'required|integer|min:0',
        ]);

        $data = [
            'ten_danh_muc' => $this->ten_danh_muc,
            'mo_ta' => $this->mo_ta,
            'thu_tu' => $this->thu_tu,
        ];

        if ($this->category) {
            $this->category->update($data);
            session()->flash('message', 'Cập nhật danh mục thành công.');
        } else {
            ProductCategory::create($data);
            session()->flash('message', 'Thêm mới danh mục thành công.');
        }

        return redirect()->route('admin.categories.index');
    }

    public function render()
    {
        return view('livewire.admin.product-category.category-form');
    }
}
