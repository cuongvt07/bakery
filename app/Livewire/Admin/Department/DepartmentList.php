<?php

namespace App\Livewire\Admin\Department;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Department;

class DepartmentList extends Component
{
    use WithPagination;
    
    public $search = '';
    public $statusFilter = '';
    
    protected $queryString = ['search', 'statusFilter'];
    
    public function updatingSearch()
    {
        $this->resetPage();
    }
    
    public function updatingStatusFilter()
    {
        $this->resetPage();
    }
    
    public function delete($id)
    {
        $department = Department::findOrFail($id);
        
        // Check if department has employees
        if ($department->users()->count() > 0) {
            session()->flash('error', 'Không thể xóa phòng ban đang có nhân viên. Vui lòng chuyển nhân viên sang phòng ban khác trước.');
            return;
        }
        
        $department->delete();
        session()->flash('success', 'Xóa phòng ban thành công!');
    }
    
    public function toggleStatus($id)
    {
        $department = Department::findOrFail($id);
        $department->trang_thai = $department->trang_thai === 'hoat_dong' ? 'tam_ngung' : 'hoat_dong';
        $department->save();
        
        session()->flash('success', 'Cập nhật trạng thái thành công!');
    }
    
    public function render()
    {
        $query = Department::query()
            ->withCount(['users as active_employees_count' => function ($query) {
                $query->where('trang_thai', 'hoat_dong');
            }]);
        
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('ma_phong_ban', 'like', '%' . $this->search . '%')
                  ->orWhere('ten_phong_ban', 'like', '%' . $this->search . '%');
            });
        }
        
        if ($this->statusFilter) {
            $query->where('trang_thai', $this->statusFilter);
        }
        
        $departments = $query->orderBy('created_at', 'desc')->paginate(10);
        
        return view('livewire.admin.department.department-list', [
            'departments' => $departments,
        ])->layout('components.layouts.admin', [
            'pageTitle' => 'Quản lý Phòng Ban',
            'breadcrumbs' => [
                ['label' => 'Phòng ban'],
            ]
        ]);
    }
}
