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
                'Người đại diện' => $supplier->nguoi_dai_dien ?? '-',
                'Số điện thoại' => $supplier->so_dien_thoai ?? '-',
                'SĐT Zalo' => $supplier->so_dien_thoai_zalo ?? '-',
                'Email' => $supplier->email ?? '-',
                'Địa chỉ' => $supplier->dia_chi ?? '-',
                'Sản phẩm cung cấp' => $supplier->san_pham_cung_cap ?? '-',
                'Nội dung thỏa thuận' => $supplier->noi_dung_thoa_thuan ?? '-',
                'Ghi chú' => $supplier->ghi_chu ?? '-',
            ];
        });
        
        return ExcelExportService::export($data, [
            'title' => 'Danh Sách Nhà Cung Cấp',
            'filename' => 'DanhSach_NhaCungCap',
            'columns' => ['Mã NCC', 'Tên nhà cung cấp', 'Người đại diện', 'Số điện thoại', 'SĐT Zalo', 'Email', 'Địa chỉ', 'Sản phẩm cung cấp', 'Nội dung thỏa thuận', 'Ghi chú'],
            'filters' => 'Tất cả',
        ]);
    }
    
    private function getQuery()
    {
        $query = Supplier::query();
        $query = $this->applySearch($query, ['ten_ncc', 'ma_ncc', 'nguoi_dai_dien', 'so_dien_thoai', 'so_dien_thoai_zalo', 'email']);
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
