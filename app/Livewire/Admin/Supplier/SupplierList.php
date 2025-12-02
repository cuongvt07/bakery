<?php

namespace App\Livewire\Admin\Supplier;

use App\Livewire\Base\BaseListComponent;
use App\Models\Supplier;
use App\Services\ExcelExportService;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class SupplierList extends BaseListComponent
{
    public function delete($id)
    {
        Supplier::find($id)?->delete();
        session()->flash('message', 'Nhà cung cấp đã được xóa thành công.');
    }
    
    public function exportExcel()
    {
        $suppliers = $this->getQuery()->get();
        
        $data = $suppliers->map(function($supplier) {
            return [
                'Mã NCC' => $supplier->ma_ncc,
                'Tên nhà cung cấp' => $supplier->ten_ncc,
                'Số điện thoại' => $supplier->so_dien_thoai ?? '-',
                'Email' => $supplier->email ?? '-',
                'Địa chỉ' => $supplier->dia_chi ?? '-',
                'Ghi chú' => $supplier->ghi_chu ?? '-',
            ];
        });
        
        return ExcelExportService::export($data, [
            'title' => 'Danh Sách Nhà Cung Cấp',
            'filename' => 'DanhSach_NhaCungCap',
            'columns' => ['Mã NCC', 'Tên nhà cung cấp', 'Số điện thoại', 'Email', 'Địa chỉ', 'Ghi chú'],
            'filters' => 'Tất cả',
        ]);
    }
    
    private function getQuery()
    {
        $query = Supplier::query();
        $query = $this->applySearch($query, ['ten_ncc', 'ma_ncc', 'so_dien_thoai', 'email']);
        $query = $this->applySort($query);
        return $query;
    }

    public function render()
    {
        $suppliers = $this->getQuery()->paginate($this->perPage);

        return view('livewire.admin.supplier.supplier-list', [
            'suppliers' => $suppliers,
        ]);
    }
}
