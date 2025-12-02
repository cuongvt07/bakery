<?php

namespace App\Livewire\Admin\Shift;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\CaLamViec;
use App\Models\PhieuChotCa;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
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
            // For testing purposes, if no active shift, try to find the latest one or create a dummy one
            // But since we ran the seeder, there SHOULD be one.
            // If not, we might need to handle it.
            // Let's just return for now, view will handle empty state.
            return;
        }
        
        $this->shiftId = $this->shift->id;
        
        // 2. Load products and opening stock
        $agencyId = $this->shift->diem_ban_id;
        $today = Carbon::today();
        
        $stockData = DB::table('ton_kho_diem_ban')
            ->where('diem_ban_id', $agencyId)
            ->where('ngay', $today)
            ->join('san_pham', 'ton_kho_diem_ban.san_pham_id', '=', 'san_pham.id')
            ->select(
                'san_pham.id', 
                'san_pham.ten_san_pham', 
                'san_pham.ma_san_pham', 
                'san_pham.gia_ban',
                'ton_kho_diem_ban.ton_dau_ca'
            )
            ->get();
            
        $this->products = $stockData;
        
        // 3. Initialize inputs
        foreach ($this->products as $p) {
            $this->closingStock[$p->id] = $p->ton_dau_ca; // Default to opening stock (0 sold)
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
            
            // Validate closing stock not greater than opening (unless we allow returns/imports mid-shift, but let's keep simple)
            // Actually, closing CAN be higher if there was a mid-shift import, but for now assume simple flow.
            
            $sold = $opening - $closing;
            $this->soldQuantities[$p->id] = $sold;
            
            $revenue = $sold * $p->gia_ban;
            $this->totalTheoretical += $revenue;
        }
        
        $this->totalActual = (float)$this->tienMat + (float)$this->tienChuyenKhoan;
        $this->discrepancy = $this->totalActual - $this->totalTheoretical;
    }
    
    public function submit()
    {
        $this->validate([
            'tienMat' => 'required|numeric|min:0',
            'tienChuyenKhoan' => 'required|numeric|min:0',
            'closingStock.*' => 'required|numeric|min:0',
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
            $this->shift->trang_thai = 'da_ket_thuc'; // Or keep it open until approved? Usually close immediately.
            $this->shift->save();
            
            // 3. Update Stock for next day (Optional, or done by a nightly job)
            // For now, we just update the current day's closing stock record
            foreach ($this->products as $p) {
                DB::table('ton_kho_diem_ban')
                    ->where('diem_ban_id', $this->shift->diem_ban_id)
                    ->where('san_pham_id', $p->id)
                    ->where('ngay', Carbon::today())
                    ->update(['ton_cuoi_ca' => $this->closingStock[$p->id]]);
            }
        });
        
        session()->flash('message', 'Chốt ca thành công! Hệ thống đã ghi nhận.');
        return redirect()->route('admin.dashboard'); // Or stay here
    }

    public function render()
    {
        return view('livewire.admin.shift.shift-closing');
    }
}
