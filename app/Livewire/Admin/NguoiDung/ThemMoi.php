<?php

namespace App\Livewire\Admin\NguoiDung;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ThemMoi extends Component
{
    use WithFileUploads;
    
    public $ho_ten = '';
    public $email = '';
    public $so_dien_thoai = '';
    public $mat_khau = '';
    public $mat_khau_confirmation = '';
    public $vai_tro = 'nhan_vien';
    public $phong_ban_id = null;
    public $trang_thai = 'hoat_dong';
    public $dia_chi = '';
    public $ngay_vao_lam = '';
    public $luong_co_ban = 0;
    public $loai_luong = 'theo_ngay';
    public $anh_dai_dien;
    
    protected $rules = [
        'ho_ten' => 'required|string|max:100',
        'email' => 'required|email|unique:nguoi_dung,email',
        'so_dien_thoai' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
        'mat_khau' => 'required|min:6|confirmed',
        'vai_tro' => 'required|in:admin,nhan_vien',
        'phong_ban_id' => 'nullable|exists:phong_ban,id',
        'trang_thai' => 'required|in:hoat_dong,khoa',
        'dia_chi' => 'nullable|string',
        'ngay_vao_lam' => 'nullable|date',
        'luong_co_ban' => 'required|numeric|min:0',
        'loai_luong' => 'required|in:theo_ngay,theo_gio',
        'anh_dai_dien' => 'nullable|image|max:2048', // 2MB max
    ];
    
    protected $messages = [
        'ho_ten.required' => 'Họ tên không được để trống.',
        'email.required' => 'Email không được để trống.',
        'email.email' => 'Email không đúng định dạng.',
        'email.unique' => 'Email đã được sử dụng.',
        'so_dien_thoai.required' => 'Số điện thoại không được để trống.',
        'so_dien_thoai.min' => 'Số điện thoại phải có ít nhất 10 số.',
        'mat_khau.required' => 'Mật khẩu không được để trống.',
        'mat_khau.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
        'mat_khau.confirmed' => 'Xác nhận mật khẩu không khớp.',
        'luong_co_ban.required' => 'Lương cơ bản không được để trống.',
        'luong_co_ban.min' => 'Lương cơ bản phải lớn hơn 0.',
    ];
    
    public function submit()
    {
        $this->validate();
        
        $data = [
            'ho_ten' => $this->ho_ten,
            'email' => $this->email,
            'so_dien_thoai' => $this->so_dien_thoai,
            'mat_khau' => Hash::make($this->mat_khau),
            'vai_tro' => $this->vai_tro,
            'phong_ban_id' => $this->phong_ban_id,
            'trang_thai' => $this->trang_thai,
            'dia_chi' => $this->dia_chi,
            'ngay_vao_lam' => $this->ngay_vao_lam,
            'luong_co_ban' => $this->luong_co_ban,
            'loai_luong' => $this->loai_luong,
        ];
        
        // Handle avatar upload
        if ($this->anh_dai_dien) {
            $filename = time() . '_' . $this->anh_dai_dien->getClientOriginalName();
            $path = $this->anh_dai_dien->storeAs('avatars', $filename, 'public');
            $data['anh_dai_dien'] = $path;
        }
        
        User::create($data);
        
        session()->flash('success', 'Thêm người dùng thành công!');
        return redirect()->route('admin.nguoidung.index');
    }
    
    public function render()
    {
        $departments = \App\Models\Department::active()->orderBy('ten_phong_ban')->get();
        
        return view('livewire.admin.nguoi-dung.them-moi', [
            'departments' => $departments,
        ])
            ->layout('components.layouts.admin', [
                'pageTitle' => 'Thêm người dùng',
                'breadcrumbs' => [
                    ['label' => 'Người dùng', 'url' => route('admin.nguoidung.index')],
                    ['label' => 'Thêm mới'],
                ]
            ]);
    }
}
