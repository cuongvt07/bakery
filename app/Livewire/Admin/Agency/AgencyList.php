<?php

namespace App\Livewire\Admin\Agency;

use App\Livewire\Base\BaseListComponent;
use App\Models\Agency;
use App\Services\ExcelExportService;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class AgencyList extends BaseListComponent
{
    public $trangThai = '';
    
    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 15],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'trangThai' => ['except' => ''],
    ];
    
    public function updatingTrangThai() { $this->resetPage(); }
    
    public function resetFilters()
    {
        parent::resetFilters();
        $this->reset(['trangThai']);
    }

    public function delete($id)
    {
        Agency::find($id)?->delete();
        session()->flash('message', 'Điểm bán đã được xóa thành công.');
    }
    
    public function exportExcel()
    {
        $agencies = $this->getQuery()->get();
        
        $data = $agencies->map(function($agency) {
            return [
                'Mã ĐB' => $agency->ma_diem_ban,
                'Tên điểm bán' => $agency->ten_diem_ban,
                'Địa chỉ' => $agency->dia_chi,
                'SĐT' => $agency->so_dien_thoai ?? '-',
                'Trạng thái' => $agency->trang_thai === 'hoat_dong' ? 'Hoạt động' : 'Đóng cửa',
            ];
        });
        
        return ExcelExportService::export($data, [
            'title' => 'Danh Sách Điểm Bán',
            'filename' => 'DanhSach_DiemBan',
            'columns' => ['Mã ĐB', 'Tên điểm bán', 'Địa chỉ', 'SĐT', 'Trạng thái'],
            'filters' => 'Tất cả',
        ]);
    }
    
    private function getQuery()
    {
        $query = Agency::query();
        $query = $this->applySearch($query, ['ten_diem_ban', 'ma_diem_ban', 'dia_chi']);
        
        if ($this->trangThai) $query->where('trang_thai', $this->trangThai);
        
        return $this->applySort($query);
    }

    public function render()
    {
        $agencies = $this->getQuery()->paginate($this->perPage);
        return view('livewire.admin.agency.agency-list', ['agencies' => $agencies]);
    }
}
