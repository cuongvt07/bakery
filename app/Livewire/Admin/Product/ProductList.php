<?php

namespace App\Livewire\Admin\Product;

use App\Livewire\Base\BaseListComponent;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Services\ExcelExportService;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class ProductList extends BaseListComponent
{
    public $categoryId = '';
    public $trangThai = '';
    
    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 15],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'categoryId' => ['except' => ''],
        'trangThai' => ['except' => ''],
    ];
    
    public function updatingCategoryId()
    {
        $this->resetPage();
    }
    
    public function updatingTrangThai()
    {
        $this->resetPage();
    }
    
    public function resetFilters()
    {
        parent::resetFilters();
        $this->reset(['categoryId', 'trangThai']);
    }

    public function delete($id)
    {
        Product::find($id)?->delete();
        session()->flash('message', 'Sản phẩm đã được xóa thành công.');
    }
    
    public function exportExcel()
    {
        $products = $this->getQuery()->get();
        
        $data = $products->map(function($product) {
            return [
                'Mã SP' => $product->ma_san_pham,
                'Tên sản phẩm' => $product->ten_san_pham,
                'Danh mục' => $product->category?->ten_danh_muc ?? '-',
                'Giá bán' => number_format($product->gia_ban, 0, ',', '.') . ' đ',
                'Đơn vị tính' => $product->don_vi_tinh,
                'Trạng thái' => match($product->trang_thai) {
                    'con_hang' => 'Còn hàng',
                    'het_hang' => 'Hết hàng',
                    'ngung_ban' => 'Ngừng bán',
                    default => '-'
                },
            ];
        });
        
        $filters = [];
        if ($this->categoryId) {
            $cat = ProductCategory::find($this->categoryId);
            $filters[] = "Danh mục: " . ($cat?->ten_danh_muc ?? '');
        }
        if ($this->trangThai) $filters[] = "Trạng thái: " . match($this->trangThai) {
            'con_hang' => 'Còn hàng',
            'het_hang' => 'Hết hàng',
            'ngung_ban' => 'Ngừng bán',
            default => ''
        };
        
        return ExcelExportService::export($data, [
            'title' => 'Danh Sách Sản Phẩm',
            'filename' => 'DanhSach_SanPham',
            'columns' => ['Mã SP', 'Tên sản phẩm', 'Danh mục', 'Giá bán', 'Đơn vị tính', 'Trạng thái'],
            'filters' => !empty($filters) ? implode(', ', $filters) : 'Tất cả',
        ]);
    }
    
    private function getQuery()
    {
        $query = Product::query()->with('category');
        
        $query = $this->applySearch($query, ['ten_san_pham', 'ma_san_pham']);
        
        if ($this->categoryId) {
            $query->where('danh_muc_id', $this->categoryId);
        }
        
        if ($this->trangThai) {
            $query->where('trang_thai', $this->trangThai);
        }
        
        $query = $this->applySort($query);
        
        return $query;
    }

    public function render()
    {
        $products = $this->getQuery()->paginate($this->perPage);
        $categories = ProductCategory::orderBy('ten_danh_muc')->get();

        return view('livewire.admin.product.product-list', [
            'products' => $products,
            'categories' => $categories,
        ]);
    }
}
