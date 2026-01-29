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
    public $trangThai = 'hoat_dong'; // Default to active
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

        // If active, soft delete (hide)
        if ($recipe->trang_thai === 'hoat_dong') {
            $recipe->update(['trang_thai' => 'ngung_su_dung']);
            session()->flash('message', 'Đã ẩn công thức khỏi danh sách (chuyển sang Ngừng sử dụng).');
            return;
        }

        // Check if recipe is being used in batches
        if ($recipe->batches()->exists()) {
            session()->flash('error', 'Không thể xóa công thức đang được sử dụng!');
            return;
        }

        $recipe->delete();
        session()->flash('message', 'Đã xóa hoàn toàn công thức.');
    }

    public function toggleStatus($id)
    {
        $recipe = Recipe::find($id);
        if ($recipe) {
            $newStatus = $recipe->trang_thai === 'hoat_dong' ? 'ngung_su_dung' : 'hoat_dong';
            $recipe->update(['trang_thai' => $newStatus]);

            $msg = $newStatus === 'hoat_dong' ? 'Đã hiện công thức trở lại.' : 'Đã ẩn công thức.';
            session()->flash('message', $msg);
        }
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
