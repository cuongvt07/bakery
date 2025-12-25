<?php

namespace App\Livewire\Admin\User;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;

use App\Models\Agency;

#[Layout('components.layouts.app')]
class UserForm extends Component
{
    use WithFileUploads;
    public ?User $user = null;
    
    // Basic info
    public $ma_nhan_vien = '';
    public $ho_ten = '';
    public $email = '';
    public $password = '';
    public $so_dien_thoai = '';
    public $facebook = '';
    public $dia_chi = '';
    public $so_cmnd = '';
    public $ngay_cap_cmnd = '';
    public $noi_cap_cmnd = '';
    public $nguoi_lien_he_khan_cap = '';
    public $sdt_lien_he_khan_cap = '';
    public $image = null; // New avatar upload
    public $existing_avatar = null;

    // Banking
    public $ngan_hang = '';
    public $so_tai_khoan = '';
    public $chu_tai_khoan = '';
    
    // Contract
    public $loai_hop_dong = 'thu_viec';
    public $ngay_ky_hop_dong = '';
    public $ngay_het_han_hop_dong = '';
    public $ngay_vao_lam = '';
    public $ngay_thu_viec = '';
    public $ngay_chinh_thuc = '';
    public $ghi_chu_hop_dong = '';
    
    // File upload
    public $file_hop_dong = null;
    public $existing_file = null;
    
    // Salary
    public $luong_co_ban = 0;
    public $luong_thu_viec = 0;
    public $luong_chinh_thuc = 0;
    public $loai_luong = 'theo_ngay';
    
    // System
    public $vai_tro = 'nhan_vien';
    public $phong_ban_id = null;
    public $trang_thai = 'hoat_dong';
    
    // Assignment
    public $agencies = [];
    public $selectedAgencies = [];

    public function updated($propertyName)
    {
        // Removed auto-zero logic to allow empty inputs
    }

    public function mount($id = null)
    {
        $this->agencies = Agency::where('trang_thai', 'hoat_dong')->get();

        if ($id) {
            $this->user = User::with('diemBan')->findOrFail($id);
            // ... (previous fields) ...
            $this->ma_nhan_vien = $this->user->ma_nhan_vien;
            $this->ho_ten = $this->user->ho_ten;
            $this->email = $this->user->email;
            $this->so_dien_thoai = $this->user->so_dien_thoai;
            $this->facebook = $this->user->facebook;
            $this->dia_chi = $this->user->dia_chi;
            
            $this->so_cmnd = $this->user->so_cmnd;
            $this->ngay_cap_cmnd = $this->user->ngay_cap_cmnd?->format('Y-m-d');
            $this->noi_cap_cmnd = $this->user->noi_cap_cmnd;
            $this->nguoi_lien_he_khan_cap = $this->user->nguoi_lien_he_khan_cap;
            $this->sdt_lien_he_khan_cap = $this->user->sdt_lien_he_khan_cap;
            $this->existing_avatar = $this->user->anh_dai_dien;
            
            $this->ngan_hang = $this->user->ngan_hang;
            $this->so_tai_khoan = $this->user->so_tai_khoan;
            $this->chu_tai_khoan = $this->user->chu_tai_khoan;

            $this->loai_hop_dong = $this->user->loai_hop_dong ?? 'thu_viec';
            $this->ngay_ky_hop_dong = $this->user->ngay_ky_hop_dong?->format('Y-m-d');
            $this->ngay_het_han_hop_dong = $this->user->ngay_het_han_hop_dong?->format('Y-m-d');
            $this->ngay_vao_lam = $this->user->ngay_vao_lam?->format('Y-m-d');
            $this->ngay_thu_viec = $this->user->ngay_thu_viec?->format('Y-m-d');
            $this->ngay_chinh_thuc = $this->user->ngay_chinh_thuc?->format('Y-m-d');
            $this->ghi_chu_hop_dong = $this->user->ghi_chu_hop_dong;
            $this->existing_file = $this->user->file_hop_dong;
            
            // Set to null if 0 to show placeholder
            $this->luong_co_ban = ($this->user->luong_co_ban && $this->user->luong_co_ban > 0) ? $this->user->luong_co_ban : null;
            $this->luong_thu_viec = ($this->user->luong_thu_viec && $this->user->luong_thu_viec > 0) ? $this->user->luong_thu_viec : null;
            $this->luong_chinh_thuc = ($this->user->luong_chinh_thuc && $this->user->luong_chinh_thuc > 0) ? $this->user->luong_chinh_thuc : null;
            
            $this->loai_luong = $this->user->loai_luong ?? 'theo_ngay';
            $this->loai_luong = $this->user->loai_luong ?? 'theo_ngay';
            
            $this->vai_tro = $this->user->vai_tro;
            $this->phong_ban_id = $this->user->phong_ban_id;
            $this->trang_thai = $this->user->trang_thai;
            
            // Assignment
            $this->selectedAgencies = $this->user->diemBan->pluck('id')->toArray();
        } else {
            // Auto-generate code preview for new user
            $this->ma_nhan_vien = (new User)->generateUniqueCode();
            // Default null to show placeholder
            $this->luong_thu_viec = null;
            $this->luong_chinh_thuc = null;
            $this->luong_co_ban = null;
        }
    }

    public function save()
    {
        $this->validate([
            'ho_ten' => 'required|min:3',
            'email' => 'required|email|unique:nguoi_dung,email,' . ($this->user->id ?? 'NULL'),
            'so_dien_thoai' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
            'password' => $this->user ? 'nullable|min:6' : 'required|min:6',
            'vai_tro' => 'required|in:admin,nhan_vien',
            'phong_ban_id' => 'nullable|exists:phong_ban,id',
            'trang_thai' => 'required|in:hoat_dong,khoa',
            'loai_hop_dong' => 'nullable|in:thu_viec,chinh_thuc,hop_tac',
            'file_hop_dong' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'image' => 'nullable|image|max:2048',
        ]);

        $data = [
            'ho_ten' => $this->ho_ten,
            'email' => $this->email,
            'so_dien_thoai' => $this->so_dien_thoai,
            'facebook' => $this->facebook,
            'dia_chi' => $this->dia_chi,
            'so_cmnd' => $this->so_cmnd,
            'ngay_cap_cmnd' => $this->ngay_cap_cmnd,
            'noi_cap_cmnd' => $this->noi_cap_cmnd,
            'nguoi_lien_he_khan_cap' => $this->nguoi_lien_he_khan_cap,
            'sdt_lien_he_khan_cap' => $this->sdt_lien_he_khan_cap,
            'ngan_hang' => $this->ngan_hang,
            'so_tai_khoan' => $this->so_tai_khoan,
            'chu_tai_khoan' => $this->chu_tai_khoan,
            'loai_hop_dong' => $this->loai_hop_dong,
            'ngay_ky_hop_dong' => $this->ngay_ky_hop_dong,
            'ngay_het_han_hop_dong' => $this->ngay_het_han_hop_dong,
            'ngay_vao_lam' => $this->ngay_vao_lam,
            'ngay_thu_viec' => $this->ngay_thu_viec,
            'ngay_chinh_thuc' => $this->ngay_chinh_thuc,
            'ghi_chu_hop_dong' => $this->ghi_chu_hop_dong,
            'luong_co_ban' => $this->luong_co_ban ?? 0,
            'luong_thu_viec' => $this->luong_thu_viec ?? 0,
            'luong_chinh_thuc' => $this->luong_chinh_thuc ?? 0,
            'loai_luong' => $this->loai_luong,
            'loai_luong' => $this->loai_luong,
            'vai_tro' => $this->vai_tro,
            'phong_ban_id' => $this->phong_ban_id,
            'trang_thai' => $this->trang_thai,
        ];

        if ($this->password) {
            $data['mat_khau'] = Hash::make($this->password);
        }
        
        // Handle avatar upload
        if ($this->image) {
            $maNhanVien = $this->user ? $this->user->ma_nhan_vien : $this->ma_nhan_vien;
            $filename = 'avatar-' . time() . '.' . $this->image->extension();
            $path = $this->image->storeAs(
                'avatars/' . $maNhanVien,
                $filename,
                'public'
            );
            $data['anh_dai_dien'] = $path;
        }

        // Handle contract file upload
        if ($this->file_hop_dong) {
            $maNhanVien = $this->user ? $this->user->ma_nhan_vien : $this->ma_nhan_vien;
            $filename = 'hop-dong-' . time() . '.' . $this->file_hop_dong->extension();
            $path = $this->file_hop_dong->storeAs(
                'contracts/' . $maNhanVien,
                $filename,
                'public'
            );
            $data['file_hop_dong'] = $path;
        }

        if ($this->user) {
            $this->user->update($data);
            
            // Sync agencies with pivot data
            $syncData = [];
            foreach ($this->selectedAgencies as $agencyId) {
                // Keep existing pivot data if possible, or set new
                $syncData[$agencyId] = ['ngay_bat_dau' => now()];
            }
            $this->user->diemBan()->sync($syncData);
            
            session()->flash('message', 'Cập nhật nhân viên thành công.');
        } else {
            $user = User::create($data);
            
            // Sync agencies
            $syncData = [];
            foreach ($this->selectedAgencies as $agencyId) {
                $syncData[$agencyId] = ['ngay_bat_dau' => now()];
            }
            $user->diemBan()->sync($syncData);
            
            session()->flash('message', 'Thêm mới nhân viên thành công.');
        }

        return redirect()->route('admin.users.index');
    }

    public function render()
    {
        $departments = \App\Models\Department::active()->orderBy('ten_phong_ban')->get();
        return view('livewire.admin.user.user-form', [
            'departments' => $departments,
        ]);
    }
}
