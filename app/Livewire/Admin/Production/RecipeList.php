<?php

namespace App\Livewire\Admin\Production;

use App\Models\Recipe;
use App\Models\Product;
use App\Models\ProductCategory;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class RecipeList extends Component
{
    use WithPagination;

    public $search = '';
    public $categoryId = '';
    public $trangThai = '';
    public $perPage = 15;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function delete($id)
    {
        $recipe = Recipe::find($id);
        
        if (!$recipe) {
            return;
        }

        // Check if recipe is being used in batches
        if ($recipe->batches()->exists()) {
            session()->flash('error', 'Không thể xóa công thức đang được sử dụng!');
            return;
        }

        $recipe->delete();
        session()->flash('message', 'Đã xóa công thức thành công.');
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->categoryId = '';
        $this->trangThai = '';
        $this->resetPage();
    }

    public function render()
    {
        $query = Recipe::with(['product.category', 'details']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('ma_cong_thuc', 'like', '%' . $this->search . '%')
                  ->orWhere('ten_cong_thuc', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->categoryId) {
            $query->whereHas('product', function ($q) {
                $q->where('danh_muc_id', $this->categoryId);
            });
        }

        if ($this->trangThai) {
            $query->where('trang_thai', $this->trangThai);
        }

        $recipes = $query->orderBy('created_at', 'desc')->paginate($this->perPage);
        $categories = ProductCategory::orderBy('ten_danh_muc')->get();

        return view('livewire.admin.production.recipe-list', [
            'recipes' => $recipes,
            'categories' => $categories,
        ]);
    }
}
