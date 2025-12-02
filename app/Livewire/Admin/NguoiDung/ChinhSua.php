<?php

namespace App\Livewire\Admin\NguoiDung;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ChinhSua extends Component
{
    use WithFileUploads;
    
    public $user;
    public $ho_ten = '';
    public $email = '';
    public $so_dien_thoai = '';
    public $mat_khau = '';
    public $mat_khau_confirmation = '';
    public $vai_tro = 'nhan_vien';
    public $trang_thai = 'hoat_dong';
    public $dia_chi = '';
    public $ngay_vao_lam = '';
    public $luong_co_ban = 0;
    public $loai_luong = 'theo_ngay';
    public $anh_dai_dien;
    public $anh_dai_dien_cu;
    
    public function mount($id)
    {
        $this->user = User::findOrFail($id);
        
        $this->ho_ten = $this->user->ho_ten;
        $this->email = $this->user->email;
        $this->so_dien_thoai = $this->user->so_dien_thoai;
        $this->vai_tro = $this->user->vai_tro;
        $this->trang_thai = $this->user->trang_thai;
        $this->dia_chi = $this->user->dia_chi;
        $this->ngay_vao_lam = $this->user->ngay_vao_lam ? $this->user->ngay_vao_lam->format('Y-m-d') : '';
        $this->luong_co_ban = $this->user->luong_co_ban;
        $this->loai_luong = $this->user->loai_luong;
        $this->anh_dai_dien_cu = $this->user->anh_dai_dien;
    }
    
    protected function rules()
    {
        return [
            'ho_ten' => 'required|string|max:100',
            'email' => ['required', 'email', Rule::unique('nguoi_dung', 'email')->ignore($this->user->id)],
            'so_dien_thoai' => 'nullable|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
            'mat_khau' => 'nullable|min:8|confirmed',
            'vai_tro' => 'required|in:admin,nhan_vien',
            'trang_thai' => 'required|in:hoat_dong,khoa',
            'dia_chi' => 'nullable|string',
            'ngay_vao_lam' => 'nullable|date',
            'luong_co_ban' => 'required|numeric|min:0',
            'loai_luong' => 'required|in:theo_ngay,theo_gio',
            'anh_dai_dien' => 'nullable|image|max:2048',
        ];
    }
    
    protected $messages = [
        'ho_ten.required' => 'Họ tên không được để trống.',
        'email.required' => 'Email không được để trống.',
        'email.email' => 'Email không đúng định dạng.',
        'email.unique' => 'Email đã được sử dụng.',
        'mat_khau.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
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
            'vai_tro' => $this->vai_tro,
            'trang_thai' => $this->trang_thai,
            'dia_chi' => $this->dia_chi,
            'ngay_vao_lam' => $this->ngay_vao_lam ?: null,
            'luong_co_ban' => $this->luong_co_ban,
            'loai_luong' => $this->loai_luong,
        ];
        
        if ($this->mat_khau) {
            $data['mat_khau'] = Hash::make($this->mat_khau);
        }
        
        // Handle avatar upload
        if ($this->anh_dai_dien) {
            $filename = time() . '_' . $this->anh_dai_dien->getClientOriginalName();
            $path = $this->anh_dai_dien->storeAs('avatars', $filename, 'public');
            $data['anh_dai_dien'] = $path;
        }
        
        $this->user->update($data);
        
        session()->flash('success', 'Cập nhật người dùng thành công!');
        return redirect()->route('admin.nguoidung.index');
    }
    
    public function render()
    {
        return view('livewire.admin.nguoi-dung.chinh-sua')
            ->layout('components.layouts.admin', [
                'pageTitle' => 'Chỉnh sửa người dùng',
                'breadcrumbs' => [
                    ['label' => 'Người dùng', 'url' => route('admin.nguoidung.index')],
                    ['label' => 'Chỉnh sửa', 'url' => '#'],
                    ['label' => $this->ho_ten],
                ]
            ]);
    }
}
