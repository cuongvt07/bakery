<?php

namespace App\Livewire\Admin\Ingredient;

use App\Livewire\Base\BaseListComponent;
use App\Models\Ingredient;
use App\Services\ExcelExportService;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class IngredientList extends BaseListComponent
{
    public $lowStock = false;  // Filter tồn kho thấp
    
    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 15],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'lowStock' => ['except' => false],
    ];
    
    public function updatingLowStock()
    {
        $this->resetPage();
    }
    
    public function resetFilters()
    {
        parent::resetFilters();
        $this->reset(['lowStock']);
    }

    public function delete($id)
    {
        try {
            Ingredient::find($id)?->delete();
            session()->flash('message', 'Nguyên liệu đã được xóa thành công.');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == 23000) {
                session()->flash('error', 'Không thể xóa nguyên liệu này vì nó đang được sử dụng trong các công thức sản xuất.');
            } else {
                session()->flash('error', 'Có lỗi xảy ra khi xóa nguyên liệu.');
            }
        }
    }
    
    public function exportExcel()
    {
        $ingredients = $this->getQuery()->get();
        
        $data = $ingredients->map(function($ing) {
            $lowStock = $ing->ton_kho_hien_tai < $ing->ton_kho_toi_thieu;
            return [
                'Mã NL' => $ing->ma_nguyen_lieu,
                'Tên nguyên liệu' => $ing->ten_nguyen_lieu,
                'Đơn vị tính' => $ing->don_vi_tinh,
                'Tồn kho hiện tại' => number_format($ing->ton_kho_hien_tai, 2, ',', '.'),
                'Tồn kho tối thiểu' => number_format($ing->ton_kho_toi_thieu, 2, ',', '.'),
                'Cảnh báo' => $lowStock ? 'Tồn kho thấp' : 'Bình thường',
            ];
        });
        
        $filters = [];
        if ($this->lowStock) $filters[] = "Chỉ hiển thị: Tồn kho thấp";
        
        return ExcelExportService::export($data, [
            'title' => 'Danh Sách Nguyên Liệu',
            'filename' => 'DanhSach_NguyenLieu',
            'columns' => ['Mã NL', 'Tên nguyên liệu', 'Đơn vị tính', 'Tồn kho hiện tại', 'Tồn kho tối thiểu', 'Cảnh báo'],
            'filters' => !empty($filters) ? implode(', ', $filters) : 'Tất cả',
        ]);
    }
    
    private function getQuery()
    {
        $query = Ingredient::query();
        $query = $this->applySearch($query, ['ten_nguyen_lieu', 'ma_nguyen_lieu']);
        
        // Filter low stock
        if ($this->lowStock) {
            $query->whereRaw('ton_kho_hien_tai < ton_kho_toi_thieu');
        }
        
        $query = $this->applySort($query);
        return $query;
    }

    public function render()
    {
        $ingredients = $this->getQuery()->paginate($this->perPage);

        return view('livewire.admin.ingredient.ingredient-list', [
            'ingredients' => $ingredients,
        ]);
    }
}
