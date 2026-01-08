<?php

namespace App\Livewire\Admin\NguoiDung;

use App\Livewire\Base\BaseListComponent;
use App\Models\User;
use App\Services\ExcelExportService;

class DanhSach extends BaseListComponent
{
    // Thêm filter đặc thù cho module Người dùng
    public $vaiTro = '';
    public $trangThai = '';
    public $diemBanId = '';
    
    // Thêm vào queryString
    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 15],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'dateFrom' => ['except' => ''],
        'dateTo' => ['except' => ''],
        'vaiTro' => ['except' => ''],
        'trangThai' => ['except' => ''],
        'diemBanId' => ['except' => ''],
    ];
    
    /**
     * Reset filter khi thay đổi
     */
    public function updatingVaiTro()
    {
        $this->resetPage();
    }
    
    public function updatingTrangThai()
    {
        $this->resetPage();
    }
    
    public function updatingDiemBanId()
    {
        $this->resetPage();
    }
    
    /**
     * Override resetFilters để reset cả filter đặc thù
     */
    public function resetFilters()
    {
        parent::resetFilters();
        $this->reset(['vaiTro', 'trangThai', 'diemBanId']);
    }
    
    /**
     * Xóa người dùng
     */
    public function delete($id)
    {
        try {
            $user = User::find($id);
            if ($user) {
                $user->delete();
                session()->flash('success', 'Xóa người dùng thành công!');
            }
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == 23000) {
                session()->flash('error', 'Không thể xóa người dùng này vì có lịch làm việc, mẻ sản xuất hoặc nhật ký hoạt động liên quan.');
            } else {
                session()->flash('error', 'Có lỗi xảy ra khi xóa người dùng.');
            }
        }
    }
    
    /**
     * Export Excel
     */
    public function exportExcel()
    {
        $users = $this->getQuery()->get();
        
        // Transform data để export
        $data = $users->map(function($user) {
            return [
                'Mã NV' => $user->id,
                'Họ tên' => $user->ho_ten,
                'Email' => $user->email,
                'Số điện thoại' => $user->so_dien_thoai ?? '-',
                'Vai trò' => $user->vai_tro === 'admin' ? 'Quản trị viên' : 'Nhân viên',
                'Trạng thái' => $user->trang_thai === 'hoat_dong' ? 'Hoạt động' : 'Khóa',
                'Lương cơ bản' => number_format($user->luong_co_ban, 0, ',', '.') . ' đ',
                'Loại lương' => $user->loai_luong === 'theo_ngay' ? 'Theo ngày' : 'Theo giờ',
                'Ngày vào làm' => $user->ngay_vao_lam ? date('d/m/Y', strtotime($user->ngay_vao_lam)) : '-',
            ];
        });
        
        // Build filter string
        $filters = [];
        if ($this->dateFrom) $filters[] = "Từ {$this->dateFrom}";
        if ($this->dateTo) $filters[] = "Đến {$this->dateTo}";
        if ($this->vaiTro) $filters[] = "Vai trò: " . ($this->vaiTro === 'admin' ? 'Quản trị viên' : 'Nhân viên');
        if ($this->trangThai) $filters[] = "Trạng thái: " . ($this->trangThai === 'hoat_dong' ? 'Hoạt động' : 'Khóa');
        
        return ExcelExportService::export($data, [
            'title' => 'Danh Sách Người Dùng',
            'filename' => 'DanhSach_NguoiDung',
            'columns' => ['Mã NV', 'Họ tên', 'Email', 'Số điện thoại', 'Vai trò', 'Trạng thái', 'Lương cơ bản', 'Loại lương', 'Ngày vào làm'],
            'filters' => !empty($filters) ? implode(', ', $filters) : 'Tất cả',
        ]);
    }
    
    /**
     * Query builder
     */
    private function getQuery()
    {
        $query = User::query()->with('diemBans');
        
        // Apply search (sử dụng method từ BaseListComponent)
        $query = $this->applySearch($query, ['ho_ten', 'email', 'so_dien_thoai']);
        
        // Apply date filter
        $query = $this->applyDateFilter($query, 'created_at');
        
        // Filter by role
        if ($this->vaiTro) {
            $query->where('vai_tro', $this->vaiTro);
        }
        
        // Filter by status
        if ($this->trangThai) {
            $query->where('trang_thai', $this->trangThai);
        }
        
        // Filter by điểm bán
        if ($this->diemBanId) {
            $query->whereHas('diemBans', function($q) {
                $q->where('diem_ban_id', $this->diemBanId);
            });
        }
        
        // Apply sort
        $query = $this->applySort($query);
        
        return $query;
    }
    
    /**
     * Render component
     */
    public function render()
    {
        $users = $this->getQuery()->paginate($this->perPage);
        
        // Load danh sách điểm bán cho filter
        $diemBans = \App\Models\Agency::orderBy('ten_diem_ban')->get();
        
        return view('livewire.admin.nguoi-dung.danh-sach', [
            'users' => $users,
            'diemBans' => $diemBans,
        ])->layout('components.layouts.admin', [
            'pageTitle' => 'Quản lý Người dùng',
            'breadcrumbs' => [
                ['label' => 'Người dùng', 'url' => route('admin.nguoidung.index')],
            ]
        ]);
    }
}
