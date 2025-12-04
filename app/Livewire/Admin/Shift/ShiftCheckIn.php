<?php

namespace App\Livewire\Admin\Shift;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\CaLamViec;
use App\Models\NhanVienDiemBan;
use App\Models\PhanBoHangDiemBan;
use App\Models\ChiTietCaLam;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

#[Layout('components.layouts.app')]
class ShiftCheckIn extends Component
{
    public $hasActiveShift = false;
    public $isCheckedIn = false;
    public $shift;
    
    // Inputs
    public $openingCash = 0;
    public $receivedStock = []; // [product_id => quantity]
    public $products = [];
    
    public function mount()
    {
        $this->checkShiftStatus();
    }
    
    public function checkShiftStatus()
    {
        $user = Auth::user();
        
        // 1. Check for active shift
        $this->shift = CaLamViec::where('nguoi_dung_id', $user->id)
            ->where('trang_thai', 'dang_lam')
            ->first();
            
        if ($this->shift) {
            $this->hasActiveShift = true;
            $this->isCheckedIn = $this->shift->trang_thai_checkin;
            
            if (!$this->isCheckedIn) {
                $this->loadDistributedStock();
            }
        }
    }
    
    public function startShift()
    {
        // Find assigned agency
        $assignment = NhanVienDiemBan::where('nguoi_dung_id', Auth::id())
            ->where('ngay_bat_dau', '<=', now())
            ->where(function($q) {
                $q->whereNull('ngay_ket_thuc')
                  ->orWhere('ngay_ket_thuc', '>=', now());
            })
            ->first();
            
        if (!$assignment) {
            session()->flash('error', 'Bạn chưa được phân công vào điểm bán nào!');
            return;
        }
        
        $shift = CaLamViec::create([
            'diem_ban_id' => $assignment->diem_ban_id,
            'nguoi_dung_id' => Auth::id(),
            'ngay_lam' => now(),
            'gio_bat_dau' => now(),
            'gio_ket_thuc' => now()->addHours(8), // Estimated
            'trang_thai' => 'dang_lam',
            'trang_thai_checkin' => false,
        ]);
        
        $this->checkShiftStatus();
    }
    
    public function loadDistributedStock()
    {
        $agencyId = $this->shift->diem_ban_id;
        
        // Determine Session based on Shift Start Time
        $startTime = Carbon::parse($this->shift->gio_bat_dau);
        $session = $startTime->lt(Carbon::parse('12:00:00')) ? 'sang' : 'chieu';
        
        // Find all distributions for this agency, today, and session
        $distributions = PhanBoHangDiemBan::with(['productionBatch.recipe.product'])
            ->where('diem_ban_id', $agencyId)
            ->whereDate('created_at', Carbon::today())
            ->where('buoi', $session)
            ->where('trang_thai', 'chua_nhan')
            ->get();
            
        $this->products = [];
        $this->receivedStock = [];
        
        foreach ($distributions as $dist) {
            if ($dist->productionBatch && $dist->productionBatch->recipe) {
                $product = $dist->productionBatch->recipe->product;
                
                // Add to products list if not already there
                if (!isset($this->receivedStock[$product->id])) {
                    $this->products[] = $product;
                    $this->receivedStock[$product->id] = 0;
                }
                
                // Add quantity from this distribution
                $this->receivedStock[$product->id] += $dist->so_luong;
            }
        }
    }
    
    public function confirmCheckIn()
    {
        $this->validate([
            'openingCash' => 'required|numeric|min:0',
            'receivedStock.*' => 'required|numeric|min:0',
        ]);
        
        DB::transaction(function () {
            // 1. Update Shift
            $this->shift->update([
                'tien_mat_dau_ca' => $this->openingCash,
                'trang_thai_checkin' => true,
                'thoi_gian_checkin' => now(),
            ]);
            
            // 2. Create Shift Details (ChiTietCaLam)
            foreach ($this->products as $p) {
                $qty = $this->receivedStock[$p->id] ?? 0;
                if ($qty > 0) {
                    ChiTietCaLam::create([
                        'ca_lam_viec_id' => $this->shift->id,
                        'san_pham_id' => $p->id,
                        'so_luong_nhan_ca' => $qty,
                        'so_luong_giao_ca' => 0,
                        'so_luong_ban' => 0,
                    ]);
                }
            }
            
            // 3. Mark distributions as received
            $agencyId = $this->shift->diem_ban_id;
            $startTime = Carbon::parse($this->shift->gio_bat_dau);
            $session = $startTime->lt(Carbon::parse('12:00:00')) ? 'sang' : 'chieu';
            
            PhanBoHangDiemBan::where('diem_ban_id', $agencyId)
                ->whereDate('created_at', Carbon::today())
                ->where('buoi', $session)
                ->where('trang_thai', 'chua_nhan')
                ->update([
                    'trang_thai' => 'da_nhan',
                    'nguoi_nhan_id' => Auth::id(),
                ]);
        });
        
        $this->checkShiftStatus();
        session()->flash('success', 'Check-in thành công!');
    }
    
    public function render()
    {
        return view('livewire.admin.shift.shift-check-in');
    }
}
