<?php

namespace App\Livewire\Admin\Shift;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\CaLamViec;
use App\Models\PhieuChotCa;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\ChiTietCaLam;
use Carbon\Carbon;

#[Layout('components.layouts.app')]
class ShiftClosing extends Component
{
    public $shiftId;
    public $shift;
    public $products = [];
    
    // Inputs
    public $closingStock = []; // [product_id => quantity]
    public $tienMat = 0;
    public $tienChuyenKhoan = 0;
    public $ghiChu = '';
    
    // Calculated
    public $totalTheoretical = 0;
    public $totalActual = 0;
    public $discrepancy = 0;
    public $soldQuantities = []; // [product_id => quantity]

    public function mount()
    {
        // 1. Get active shift for current user
        $this->shift = CaLamViec::where('nguoi_dung_id', Auth::id())
            ->where('trang_thai', 'dang_lam')
            ->first();
            
        if (!$this->shift) {
            return;
        }
        
        $this->shiftId = $this->shift->id;
        
        // 2. Load products and opening stock from ChiTietCaLam (Confirmed at Check-in)
        $details = ChiTietCaLam::where('ca_lam_viec_id', $this->shiftId)
            ->join('san_pham', 'chi_tiet_ca_lam.san_pham_id', '=', 'san_pham.id')
            ->select(
                'san_pham.id', 
                'san_pham.ten_san_pham', 
                'san_pham.ma_san_pham', 
                'san_pham.gia_ban',
                'chi_tiet_ca_lam.so_luong_nhan_ca as ton_dau_ca'
            )
            ->get();
            
        $this->products = $details;
        
        // 3. Initialize inputs
        foreach ($this->products as $p) {
            $this->closingStock[$p->id] = $p->ton_dau_ca; // Default to opening stock
        }
        
        $this->calculate();
    }
    
    public function updated($propertyName)
    {
        $this->calculate();
    }
    
    public function calculate()
    {
        $this->totalTheoretical = 0;
        $this->soldQuantities = [];
        
        foreach ($this->products as $p) {
            $opening = $p->ton_dau_ca;
            $closing = (float) ($this->closingStock[$p->id] ?? 0);
            
            $sold = $opening - $closing;
            $this->soldQuantities[$p->id] = $sold;
            
            $revenue = $sold * $p->gia_ban;
            $this->totalTheoretical += $revenue;
        }
        
        $this->totalActual = (float)$this->tienMat + (float)$this->tienChuyenKhoan;
        $this->discrepancy = $this->totalActual - $this->totalTheoretical;
    }
    
    use \Livewire\WithFileUploads;

    public $photosCash = []; // Array of UploadedFile
    public $photosStock = []; // Array of UploadedFile

    public function submit()
    {
        $this->validate([
            'tienMat' => 'required|numeric|min:0',
            'tienChuyenKhoan' => 'required|numeric|min:0',
            'closingStock.*' => 'required|numeric|min:0',
            'photosCash.*' => 'image|max:2048',
            'photosStock.*' => 'image|max:2048',
        ]);
        
        DB::transaction(function () {
            // 1. Create PhieuChotCa
            $phieu = new PhieuChotCa();
            $phieu->ma_phieu = 'PCC-' . time();
            $phieu->diem_ban_id = $this->shift->diem_ban_id;
            $phieu->nguoi_chot_id = Auth::id();
            $phieu->ca_lam_viec_id = $this->shiftId;
            $phieu->ngay_chot = now();
            $phieu->gio_chot = now();
            
            $phieu->tien_mat = $this->tienMat;
            $phieu->tien_chuyen_khoan = $this->tienChuyenKhoan;
            $phieu->tong_tien_thuc_te = $this->totalActual;
            $phieu->tong_tien_ly_thuyet = $this->totalTheoretical;
            $phieu->tien_lech = $this->discrepancy;
            
            // Handle Image Uploads
            $cashPaths = [];
            foreach ($this->photosCash as $photo) {
                $cashPaths[] = $photo->store('shift-closing/cash', 'public');
            }
            $phieu->anh_tien_mat = json_encode($cashPaths);

            $stockPaths = [];
            foreach ($this->photosStock as $photo) {
                $stockPaths[] = $photo->store('shift-closing/stock', 'public');
            }
            $phieu->anh_hang_hoa = json_encode($stockPaths);
            
            // Prepare JSON data
            $tonDau = [];
            $tonCuoi = [];
            foreach ($this->products as $p) {
                $tonDau[$p->id] = $p->ton_dau_ca;
                $tonCuoi[$p->id] = $this->closingStock[$p->id];
            }
            
            $phieu->ton_dau_ca = json_encode($tonDau);
            $phieu->ton_cuoi_ca = json_encode($tonCuoi);
            $phieu->ghi_chu = $this->ghiChu;
            $phieu->trang_thai = 'cho_duyet';
            
            $phieu->save();
            
            // 2. Update Shift status
            $this->shift->trang_thai = 'da_ket_thuc'; 
            $this->shift->save();
            
            // 3. Update ChiTietCaLam (Closing Stock & Sold)
            foreach ($this->products as $p) {
                $sold = $this->soldQuantities[$p->id] ?? 0;
                ChiTietCaLam::where('ca_lam_viec_id', $this->shiftId)
                    ->where('san_pham_id', $p->id)
                    ->update([
                        'so_luong_giao_ca' => $this->closingStock[$p->id],
                        'so_luong_ban' => $sold
                    ]);
                    
                // Update Daily Stock (TonKhoDiemBan) as well to keep it in sync
                DB::table('ton_kho_diem_ban')
                    ->where('diem_ban_id', $this->shift->diem_ban_id)
                    ->where('san_pham_id', $p->id)
                    ->where('ngay', Carbon::today())
                    ->update(['ton_cuoi_ca' => $this->closingStock[$p->id]]);
            }
        });
        
        session()->flash('message', 'Chốt ca thành công! Hệ thống đã ghi nhận.');
        return redirect()->route('admin.dashboard');
    }

    public function generateZaloText()
    {
        $date = now()->format('d/m/Y');
        $shiftName = "Ca " . ($this->shift->gio_bat_dau < '12:00:00' ? 'Sáng' : 'Chiều');
        $userName = Auth::user()->ho_ten;
        
        $text = "BÁO CÁO CHỐT CA - $date\n";
        $text .= "--------------------------------\n";
        $text .= "👤 Nhân viên: $userName\n";
        $text .= "🕒 $shiftName\n\n";
        
        $text .= "📦 TỒN KHO:\n";
        foreach ($this->products as $p) {
            $sold = $this->soldQuantities[$p->id] ?? 0;
            $text .= "- {$p->ten_san_pham}: Bán $sold (Còn {$this->closingStock[$p->id]})\n";
        }
        
        $text .= "\n💰 DOANH THU:\n";
        $text .= "- Tiền mặt: " . number_format($this->tienMat) . " đ\n";
        $text .= "- Chuyển khoản: " . number_format($this->tienChuyenKhoan) . " đ\n";
        $text .= "- Tổng thực tế: " . number_format($this->totalActual) . " đ\n";
        
        if ($this->discrepancy != 0) {
            $status = $this->discrepancy > 0 ? "DƯ" : "THIẾU";
            $text .= "\n⚠️ LỆCH: " . number_format(abs($this->discrepancy)) . " đ ($status)\n";
        } else {
            $text .= "\n✅ Khớp doanh thu\n";
        }
        
        $this->dispatch('copy-to-clipboard', text: $text);
    }

    public function render()
    {
        return view('livewire.admin.shift.shift-closing');
    }
}
