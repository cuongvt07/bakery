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
    
    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 15],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'vaiTro' => ['except' => ''],
        'trangThai' => ['except' => ''],
    ];
    
    public function updatingVaiTro() { $this->resetPage(); }
    public function updatingTrangThai() { $this->resetPage(); }
    
    public function resetFilters()
    {
        parent::resetFilters();
        $this->reset(['vaiTro', 'trangThai']);
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
                'Họ tên' => $user->ho_ten,
                'Email' => $user->email,
                'SĐT' => $user->so_dien_thoai ?? '-',
                'Vai trò' => $user->vai_tro === 'admin' ? 'Admin' : 'Nhân viên',
                'Trạng thái' => $user->trang_thai === 'hoat_dong' ? 'Hoạt động' : 'Khóa',
                'Lương CB' => number_format($user->luong_co_ban, 0, ',', '.') . ' đ',
            ];
        });
        
        return ExcelExportService::export($data, [
            'title' => 'Danh Sách Người Dùng',
            'filename' => 'DanhSach_NguoiDung',
            'columns' => ['Họ tên', 'Email', 'SĐT', 'Vai trò', 'Trạng thái', 'Lương CB'],
            'filters' => 'Tất cả',
        ]);
    }
    
    private function getQuery()
    {
        $query = User::query();
        $query = $this->applySearch($query, ['ho_ten', 'email', 'so_dien_thoai']);
        
        if ($this->vaiTro) $query->where('vai_tro', $this->vaiTro);
        if ($this->trangThai) $query->where('trang_thai', $this->trangThai);
        
        return $this->applySort($query);
    }

    public function render()
    {
        $users = $this->getQuery()->paginate($this->perPage);
        return view('livewire.admin.user.user-list', ['users' => $users]);
    }
}
