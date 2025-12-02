<?php

namespace App\Livewire\Admin\Distribution;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Product;
use App\Models\Agency;
use App\Models\PhieuXuatHangTong;
use App\Models\ChiTietPhieuXuatTong;
use App\Models\PhanBoHangDiemBan;
use App\Models\ChiTietPhanBo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

#[Layout('components.layouts.app')]
class DailyDistribution extends Component
{
    public $date;
    public $step = 1; // 1: Nhập tổng, 2: Phân bổ
    
    public $products = [];
    public $agencies = [];
    
    // Step 1 Data
    public $totalQuantities = []; // [product_id => quantity]
    public $phieuTong = null;
    public $ghiChuTong = '';
    
    // Step 2 Data
    public $selectedAgencyId = null;
    public $agencyQuantities = []; // [product_id => quantity]
    public $distributedState = []; // [product_id => total_distributed]
    
    public function mount()
    {
        $this->date = Carbon::today()->format('Y-m-d');
        $this->products = Product::where('trang_thai', 'con_hang')->orderBy('ten_san_pham', 'asc')->get();
        $this->agencies = Agency::where('trang_thai', 'hoat_dong')->get();
        
        $this->loadDailyData();
    }
    
    public function updatedDate()
    {
        $this->loadDailyData();
    }
    
    public function loadDailyData()
    {
        // Reset states
        $this->step = 1;
        $this->phieuTong = null;
        $this->totalQuantities = [];
        $this->agencyQuantities = [];
        $this->selectedAgencyId = null;
        
        // Check if Total Ticket exists
        $this->phieuTong = PhieuXuatHangTong::where('ngay_xuat', $this->date)->first();
        
        if ($this->phieuTong) {
            $this->step = 2;
            $this->ghiChuTong = $this->phieuTong->ghi_chu;
            
            // Load details
            foreach ($this->phieuTong->chiTiet as $detail) {
                $this->totalQuantities[$detail->san_pham_id] = $detail->so_luong;
            }
            
            $this->calculateDistributedState();
        } else {
            // Init empty for Step 1
            foreach ($this->products as $p) {
                $this->totalQuantities[$p->id] = 0;
            }
        }
    }
    
    public function calculateDistributedState()
    {
        // Calculate how much has been distributed so far
        $this->distributedState = [];
        foreach ($this->products as $p) {
            $this->distributedState[$p->id] = 0;
        }
        
        if ($this->phieuTong) {
            $distributions = PhanBoHangDiemBan::where('phieu_xuat_hang_tong_id', $this->phieuTong->id)->with('chiTiet')->get();
            foreach ($distributions as $dist) {
                foreach ($dist->chiTiet as $detail) {
                    if (isset($this->distributedState[$detail->san_pham_id])) {
                        $this->distributedState[$detail->san_pham_id] += $detail->so_luong;
                    }
                }
            }
        }
    }
    
    public function createTotalTicket()
    {
        $this->validate([
            'totalQuantities.*' => 'required|numeric|min:0',
        ]);
        
        // Calculate total items
        $grandTotal = array_sum($this->totalQuantities);
        
        if ($grandTotal == 0) {
            session()->flash('error', 'Vui lòng nhập số lượng ít nhất 1 sản phẩm.');
            return;
        }
        
        DB::transaction(function () use ($grandTotal) {
            $phieu = PhieuXuatHangTong::create([
                'ma_phieu' => 'PXT-' . Carbon::parse($this->date)->format('Ymd'),
                'nguoi_xuat_id' => Auth::id(),
                'ngay_xuat' => $this->date,
                'gio_xuat' => now(),
                'tong_so_luong' => $grandTotal,
                'ghi_chu' => $this->ghiChuTong,
                'trang_thai' => 'da_xuat'
            ]);
            
            foreach ($this->totalQuantities as $productId => $qty) {
                if ($qty > 0) {
                    ChiTietPhieuXuatTong::create([
                        'phieu_xuat_hang_tong_id' => $phieu->id,
                        'san_pham_id' => $productId,
                        'so_luong' => $qty
                    ]);
                }
            }
            
            $this->phieuTong = $phieu;
        });
        
        $this->step = 2;
        $this->calculateDistributedState();
        session()->flash('success', 'Đã tạo phiếu xuất tổng thành công! Chuyển sang phân bổ.');
    }
    
    public function selectAgency($agencyId)
    {
        $this->selectedAgencyId = $agencyId;
        $this->agencyQuantities = [];
        
        // Check if already distributed
        $existingDist = PhanBoHangDiemBan::where('phieu_xuat_hang_tong_id', $this->phieuTong->id)
            ->where('diem_ban_id', $agencyId)
            ->first();
            
        if ($existingDist) {
            foreach ($existingDist->chiTiet as $detail) {
                $this->agencyQuantities[$detail->san_pham_id] = $detail->so_luong;
            }
        } else {
            // Init empty
            foreach ($this->products as $p) {
                $this->agencyQuantities[$p->id] = 0;
            }
        }
    }
    
    public function saveAgencyDistribution()
    {
        if (!$this->selectedAgencyId) return;
        
        $this->validate([
            'agencyQuantities.*' => 'required|numeric|min:0',
        ]);
        
        // Validate against remaining stock
        foreach ($this->agencyQuantities as $pid => $qty) {
            $total = $this->totalQuantities[$pid] ?? 0;
            $distributed = $this->distributedState[$pid] ?? 0;
            
            // If editing existing, subtract current value from distributed to get "other distributed"
            // But for simplicity, let's just re-calculate distributed excluding current agency
            // TODO: Strict validation
        }
        
        DB::transaction(function () {
            // 1. Create/Update PhanBoHangDiemBan
            $dist = PhanBoHangDiemBan::updateOrCreate(
                [
                    'phieu_xuat_hang_tong_id' => $this->phieuTong->id,
                    'diem_ban_id' => $this->selectedAgencyId
                ],
                [
                    'nguoi_nhan_id' => null, // Chưa nhận
                    'trang_thai' => 'chua_nhan',
                    'updated_at' => now()
                ]
            );
            
            // 2. Sync details
            // Delete old details
            ChiTietPhanBo::where('phan_bo_hang_diem_ban_id', $dist->id)->delete();
            
            // Insert new
            foreach ($this->agencyQuantities as $pid => $qty) {
                if ($qty > 0) {
                    ChiTietPhanBo::create([
                        'phan_bo_hang_diem_ban_id' => $dist->id,
                        'san_pham_id' => $pid,
                        'so_luong' => $qty
                    ]);
                }
            }
            
            // 3. CRITICAL: Update Stock (Ton Kho Diem Ban)
            // This sets the "Opening Stock" for the shift
            foreach ($this->agencyQuantities as $pid => $qty) {
                DB::table('ton_kho_diem_ban')->updateOrInsert(
                    [
                        'diem_ban_id' => $this->selectedAgencyId,
                        'san_pham_id' => $pid,
                        'ngay' => $this->date
                    ],
                    [
                        'ton_dau_ca' => $qty,
                        // 'ton_cuoi_ca' => 0, // Don't reset if already has value (e.g. mid-day update)
                        'updated_at' => now()
                    ]
                );
            }
        });
        
        $this->calculateDistributedState();
        session()->flash('success', 'Đã lưu phân bổ cho điểm bán!');
    }

    public function render()
    {
        return view('livewire.admin.distribution.daily-distribution');
    }
}
