<?php

namespace App\Livewire\Admin\User;

use App\Livewire\Base\BaseListComponent;
use App\Models\User;
use App\Services\ExcelExportService;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class UserList extends BaseListComponent
{
    public $vaiTro = '';
    public $trangThai = '';
    public $loaiHopDong = '';
    
    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 15],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'vaiTro' => ['except' => ''],
        'trangThai' => ['except' => ''],
        'loaiHopDong' => ['except' => ''],
    ];
    
    public function updatingVaiTro() { $this->resetPage(); }
    public function updatingTrangThai() { $this->resetPage(); }
    public function updatingLoaiHopDong() { $this->resetPage(); }
    
    public function resetFilters()
    {
        parent::resetFilters();
        $this->reset(['vaiTro', 'trangThai', 'loaiHopDong']);
    }

    public function delete($id)
    {
        User::find($id)?->delete();
        session()->flash('message', 'Người dùng đã được xóa thành công.');
    }
    
    public function exportExcel()
    {
        $users = $this->getQuery()->get();
        
        $data = $users->map(function($user) {
            return [
                'Mã NV' => $user->ma_nhan_vien ?? '-',
                'Họ tên' => $user->ho_ten,
                'Email' => $user->email,
                'SĐT' => $user->so_dien_thoai ?? '-',
                'Địa chỉ' => $user->dia_chi ?? '-',
                'Facebook' => $user->facebook ?? '-',
                'Loại HĐ' => match($user->loai_hop_dong) {
                    'chinh_thuc' => 'Chính thức',
                    'thu_viec' => 'Thử việc',
                    'hop_tac' => 'Hợp tác',
                    default => '-',
                },
                'Ngày ký HĐ' => $user->ngay_ky_hop_dong?->format('d/m/Y') ?? '-',
                'Lương TV' => $user->luong_thu_viec ? number_format($user->luong_thu_viec, 0, ',', '.') . ' đ' : '-',
                'Lương CT' => $user->luong_chinh_thuc ? number_format($user->luong_chinh_thuc, 0, ',', '.') . ' đ' : '-',
                'Loại lương' => $user->loai_luong ?? '-',
                'Vai trò' => $user->vai_tro === 'admin' ? 'Admin' : 'Nhân viên',
                'Trạng thái' => $user->trang_thai === 'hoat_dong' ? 'Hoạt động' : 'Khóa',
            ];
        });
        
        return ExcelExportService::export($data, [
            'title' => 'Danh Sách Nhân Viên',
            'filename' => 'DanhSach_NhanVien',
            'columns' => ['Mã NV', 'Họ tên', 'Email', 'SĐT', 'Địa chỉ', 'Facebook', 'Loại HĐ', 'Ngày ký HĐ', 'Lương TV', 'Lương CT', 'Loại lương', 'Vai trò', 'Trạng thái'],
            'filters' => 'Tất cả',
        ]);
    }
    
    private function getQuery()
    {
        $query = User::query();
        $query = $this->applySearch($query, ['ma_nhan_vien', 'ho_ten', 'email', 'so_dien_thoai']);
        
        if ($this->vaiTro) $query->where('vai_tro', $this->vaiTro);
        if ($this->trangThai) $query->where('trang_thai', $this->trangThai);
        if ($this->loaiHopDong) $query->where('loai_hop_dong', $this->loaiHopDong);
        
        return $this->applySort($query);
    }

    public function render()
    {
        $users = $this->getQuery()->paginate($this->perPage);
        return view('livewire.admin.user.user-list', ['users' => $users]);
    }
}
