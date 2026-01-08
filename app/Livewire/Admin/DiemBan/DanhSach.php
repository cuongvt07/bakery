<?php

namespace App\Livewire\Admin\DiemBan;

use App\Livewire\Base\BaseListComponent;
use App\Models\Agency;
use App\Services\ExcelExportService;

class DanhSach extends BaseListComponent
{
    // Filter đặc thù
    public $trangThai = '';
    
    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 15],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'trangThai' => ['except' => ''],
    ];
    
    public function updatingTrangThai()
    {
        $this->resetPage();
    }
    
    public function resetFilters()
    {
        parent::resetFilters();
        $this->reset(['trangThai']);
    }
    
    public function delete($id)
    {
        try {
            $agency = Agency::find($id);
            if ($agency) {
                $agency->delete();
                session()->flash('success', 'Xóa điểm bán thành công!');
            }
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == 23000) {
                session()->flash('error', 'Không thể xóa điểm bán này vì có phân bổ, lịch làm việc hoặc ghi chú liên quan.');
            } else {
                session()->flash('error', 'Có lỗi xảy ra khi xóa điểm bán.');
            }
        }
    }
    
    public function exportExcel()
    {
        $agencies = $this->getQuery()->get();
        
        $data = $agencies->map(function($agency) {
            return [
                'Mã điểm bán' => $agency->ma_diem_ban,
                'Tên điểm bán' => $agency->ten_diem_ban,
                'Địa chỉ' => $agency->dia_chi,
                'Số điện thoại' => $agency->so_dien_thoai ?? '-',
                'Trạng thái' => $agency->trang_thai === 'hoat_dong' ? 'Hoạt động' : 'Đóng cửa',
                'Ghi chú' => $agency->ghi_chu ?? '-',
            ];
        });
        
        $filters = [];
        if ($this->trangThai) $filters[] = "Trạng thái: " . ($this->trangThai === 'hoat_dong' ? 'Hoạt động' : 'Đóng cửa');
        
        return ExcelExportService::export($data, [
            'title' => 'Danh Sách Điểm Bán',
            'filename' => 'DanhSach_DiemBan',
            'columns' => ['Mã điểm bán', 'Tên điểm bán', 'Địa chỉ', 'Số điện thoại', 'Trạng thái', 'Ghi chú'],
            'filters' => !empty($filters) ? implode(', ', $filters) : 'Tất cả',
        ]);
    }
    
    private function getQuery()
    {
        $query = Agency::query();
        
        // Apply search
        $query = $this->applySearch($query, ['ten_diem_ban', 'ma_diem_ban', 'dia_chi', 'so_dien_thoai']);
        
        // Filter by status
        if ($this->trangThai) {
            $query->where('trang_thai', $this->trangThai);
        }
        
        // Apply sort
        $query = $this->applySort($query);
        
        return $query;
    }
    
    public function render()
    {
        $agencies = $this->getQuery()->paginate($this->perPage);
        
        return view('livewire.admin.diem-ban.danh-sach', [
            'agencies' => $agencies,
        ])->layout('components.layouts.admin', [
            'pageTitle' => 'Quản lý Điểm bán',
            'breadcrumbs' => [
                ['label' => 'Điểm bán', 'url' => route('admin.diemban.index')],
            ]
        ]);
    }
}
