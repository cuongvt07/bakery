<?php

namespace App\Livewire\Admin\ProductCategory;

use App\Livewire\Base\BaseListComponent;
use App\Models\ProductCategory;
use App\Services\ExcelExportService;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class CategoryList extends BaseListComponent
{
    public function delete($id)
    {
        try {
            ProductCategory::find($id)?->delete();
            session()->flash('message', 'Danh mục đã được xóa thành công.');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == 23000) {
                session()->flash('error', 'Không thể xóa danh mục này vì còn sản phẩm thuộc danh mục.');
            } else {
                session()->flash('error', 'Có lỗi xảy ra khi xóa danh mục.');
            }
        }
    }
    
    public function exportExcel()
    {
        $categories = $this->getQuery()->get();
        
        $data = $categories->map(function($cat) {
            return [
                'Mã DM' => $cat->ma_danh_muc,
                'Tên danh mục' => $cat->ten_danh_muc,
                'Thứ tự' => $cat->thu_tu,
                'Mô tả' => $cat->mo_ta ?? '-',
            ];
        });
        
        return ExcelExportService::export($data, [
            'title' => 'Danh Sách Danh Mục Sản Phẩm',
            'filename' => 'DanhSach_DanhMuc',
            'columns' => ['Mã DM', 'Tên danh mục', 'Thứ tự', 'Mô tả'],
            'filters' => 'Tất cả',
        ]);
    }
    
    private function getQuery()
    {
        $query = ProductCategory::query();
        $query = $this->applySearch($query, ['ten_danh_muc', 'ma_danh_muc', 'mo_ta']);
        return $this->applySort($query);
    }

    public function render()
    {
        $categories = $this->getQuery()->paginate($this->perPage);
        return view('livewire.admin.product-category.category-list', ['categories' => $categories]);
    }
}
