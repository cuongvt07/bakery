<?php

namespace App\Livewire\Admin\User;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class UserForm extends Component
{
    public ?User $user = null;
    public $ho_ten = '';
    public $email = '';
    public $password = '';
    public $so_dien_thoai = '';
    public $vai_tro = 'nhan_vien';
    public $trang_thai = 'hoat_dong';
    public $luong_co_ban = 0;
    public $loai_luong = 'theo_ngay';

    public function mount($id = null)
    {
        if ($id) {
            $this->user = User::findOrFail($id);
            $this->ho_ten = $this->user->ho_ten;
            $this->email = $this->user->email;
            $this->so_dien_thoai = $this->user->so_dien_thoai;
            $this->vai_tro = $this->user->vai_tro;
            $this->trang_thai = $this->user->trang_thai;
            $this->luong_co_ban = $this->user->luong_co_ban;
            $this->loai_luong = $this->user->loai_luong;
        }
    }

    public function save()
    {
        $this->validate([
            'ho_ten' => 'required|min:3',
            'email' => 'required|email|unique:nguoi_dung,email,' . ($this->user->id ?? 'NULL'),
            'password' => $this->user ? 'nullable|min:6' : 'required|min:6',
            'vai_tro' => 'required|in:admin,nhan_vien',
            'trang_thai' => 'required|in:hoat_dong,khoa',
        ]);

        $data = [
            'ho_ten' => $this->ho_ten,
            'email' => $this->email,
            'so_dien_thoai' => $this->so_dien_thoai,
            'vai_tro' => $this->vai_tro,
            'trang_thai' => $this->trang_thai,
            'luong_co_ban' => $this->luong_co_ban,
            'loai_luong' => $this->loai_luong,
        ];

        if ($this->password) {
            $data['mat_khau'] = Hash::make($this->password);
        }

        if ($this->user) {
            $this->user->update($data);
            session()->flash('message', 'Cập nhật người dùng thành công.');
        } else {
            User::create($data);
            session()->flash('message', 'Thêm mới người dùng thành công.');
        }

        return redirect()->route('admin.users.index');
    }

    public function render()
    {
        return view('livewire.admin.user.user-form');
    }
}
