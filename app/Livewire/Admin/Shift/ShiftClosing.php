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
    public $expectedCash = 0; // Opening cash + cash sales
    public $discrepancy = 0;
    public $soldQuantities = []; // [product_id => quantity]

    public function mount()
    {
        // 1. Get active shift for current user
        $this->shift = CaLamViec::where('nguoi_dung_id', Auth::id())
            ->where('trang_thai', 'dang_lam')
            ->first();
            
        if (!$this->shift) {
            session()->flash('error', 'Không có ca làm việc nào đang hoạt động!');
            return $this->redirect(route('admin.shift.check-in'));
        }
        
        // 2. If checked in but not explicitly closing, redirect to POS
        // User should be at POS selling, not at closing page by accident
        if ($this->shift->trang_thai_checkin && !request()->has('confirm_closing')) {
            return $this->redirect('/admin/pos');
        }
        
        // 3. Must be checked in to close shift
        if (!$this->shift->trang_thai_checkin) {
            session()->flash('error', 'Vui lòng check-in trước khi chốt ca!');
            return $this->redirect(route('admin.shift.check-in'));
        }
        
        $this->shiftId = $this->shift->id;
        
        // 4. Load products using Eloquent with accessor
        $details = ChiTietCaLam::where('ca_lam_viec_id', $this->shiftId)
            ->with('sanPham')
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->san_pham_id,
                    'ten_san_pham' => $item->sanPham->ten_san_pham,
                    'ma_san_pham' => $item->sanPham->ma_san_pham,
                    'gia_ban' => $item->sanPham->gia_ban,
                    'ton_dau_ca' => $item->so_luong_nhan_ca,
                    'so_luong_ban' => $item->so_luong_ban,
                    'so_luong_con_lai' => $item->so_luong_con_lai, // Use accessor
                ];
            })
            ->toArray();
            
        $this->products = $details;
        
        // 5. Auto-fill with remaining stock ONLY if not already set
        // This prevents resetting user's manual edits
        if (empty($this->closingStock)) {
            foreach ($this->products as $p) {
                $this->closingStock[$p['id']] = $p['so_luong_con_lai']; // Auto-fill with current remaining
            }
        }
        
        // 6. Load sales summary
        $this->loadSalesSummary();
        
        $this->calculate();
    }
    
    public $cashSalesCount = 0;
    public $transferSalesCount = 0;
    public $cashSalesTotal = 0;
    public $transferSalesTotal = 0;
    
    public function loadSalesSummary()
    {
        // Get all confirmed pending sales for this shift
        $sales = \App\Models\PendingSale::where('ca_lam_viec_id', $this->shiftId)
            ->where('trang_thai', 'confirmed')
            ->get();
        
        $this->cashSalesCount = $sales->where('phuong_thuc_thanh_toan', 'tien_mat')->count();
        $this->transferSalesCount = $sales->where('phuong_thuc_thanh_toan', 'chuyen_khoan')->count();
        $this->cashSalesTotal = $sales->where('phuong_thuc_thanh_toan', 'tien_mat')->sum('tong_tien');
        $this->transferSalesTotal = $sales->where('phuong_thuc_thanh_toan', 'chuyen_khoan')->sum('tong_tien');
    }
    
    public function updated($propertyName)
    {
        // Only recalculate when money-related fields change, NOT when stock changes
        if (str_starts_with($propertyName, 'tienMat') || str_starts_with($propertyName, 'tienChuyenKhoan')) {
            $this->calculate();
        }
    }
    
    public function calculate()
    {
        $this->totalTheoretical = 0;
        $this->soldQuantities = [];
        
        foreach ($this->products as $p) {
            $opening = $p['ton_dau_ca'];
            $closing = (float) ($this->closingStock[$p['id']] ?? 0);
            
            $sold = $opening - $closing;
            $this->soldQuantities[$p['id']] = $sold;
            
            $revenue = $sold * $p['gia_ban'];
            $this->totalTheoretical += $revenue;
        }
        
        // Expected Cash = Opening Cash + Cash Sales Total (from confirmed orders)
        $openingCash = (float)($this->shift->tien_mat_dau_ca ?? 0);
        $cashSalesTotal = (float)$this->cashSalesTotal; // From confirmed pending sales
        $this->expectedCash = $openingCash + $cashSalesTotal;
        
        // Actual Total = Cash Holding + Transfer Total
        $cashHolding = (float)$this->tienMat;
        $transferTotal = (float)$this->transferSalesTotal;
        
        $this->totalActual = $cashHolding + $transferTotal;
        
        $this->discrepancy = $this->totalActual - $this->totalTheoretical;
    }
    
    // Generate text report for easy copy-paste
    public function generateReport()
    {
        $cashHolding = (float)($this->tienMat ?? 0);
        $openingCash = (float)($this->shift->tien_mat_dau_ca ?? 0);
        $cashRevenue = $cashHolding - $openingCash;
        
        $report = "📊 BÁO CÁO CHỐT CA\n\n";
        
        // Cash breakdown
        $report .= "💰 TIỀN MẶT:\n";
        $report .= "- Tiền đầu ca: " . number_format($openingCash/1000, 0) . "k\n";
        $report .= "- Tiền đang giữ: " . number_format($cashHolding/1000, 0) . "k\n";
        $report .= "- Doanh thu TM: " . number_format($cashRevenue/1000, 0) . "k\n";
        
        // Transfer info
        if ($this->transferSalesCount > 0) {
            $report .= "\n💳 CHUYỂN KHOẢN:\n";
            $report .= "- " . $this->transferSalesCount . " đơn - " . number_format($this->transferSalesTotal/1000, 0) . "k\n";
        }
        
        // Stock list
        $report .= "\n📦 TỒN KHO:\n";
        foreach ($this->products as $p) {
            $remaining = intval($this->closingStock[$p['id']] ?? 0);
            if ($remaining > 0) {
                $report .= "Còn {$remaining} " . $p['ten_san_pham'] . "\n";
            }
        }
        
        // Total reconciliation
        $report .= "\n📝 ĐỐI SOÁT:\n";
        $report .= "- Lý thuyết: " . number_format($this->totalTheoretical/1000, 0) . "k\n";
        $report .= "- Thực tế: " . number_format($this->totalActual/1000, 0) . "k\n";
        $report .= "- Chênh lệch: " . number_format($this->discrepancy/1000, 0) . "k" . ($this->discrepancy == 0 ? " ✅" : "") . "\n";
        
        return $report;
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
            'photosCash.*' => 'image|max:10240',
            'photosStock.*' => 'image|max:10240',
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
            
            // Theoretical Total = Expected Cash + Transfer Sales
            // Expected Cash = Opening Cash + Cash Sales
            $theoreticalTotal = $this->expectedCash + $this->transferSalesTotal;
            $phieu->tong_tien_ly_thuyet = $theoreticalTotal;
            
            // Actual Total = Cash Holding + Transfer (should match input)
            $actualTotal = $this->tienMat + $this->tienChuyenKhoan;
            $phieu->tong_tien_thuc_te = $actualTotal;
            
            // Discrepancy = Actual - Theoretical
            $phieu->tien_lech = $actualTotal - $theoreticalTotal;
            
            // Handle Image Uploads with Resizing
            $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());

            $cashPaths = [];
            foreach ($this->photosCash as $photo) {
                $cashPaths[] = $this->resizeAndStore($photo, 'shift-closing/cash', $manager);
            }
            $phieu->anh_tien_mat = json_encode($cashPaths);

            $stockPaths = [];
            foreach ($this->photosStock as $photo) {
                $stockPaths[] = $this->resizeAndStore($photo, 'shift-closing/stock', $manager);
            }
            $phieu->anh_hang_hoa = json_encode($stockPaths);
            
            // Prepare JSON data
            $tonDau = [];
            $tonCuoi = [];
            foreach ($this->products as $p) {
                $tonDau[$p['id']] = $p['ton_dau_ca'];
                $tonCuoi[$p['id']] = $this->closingStock[$p['id']];
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
                $sold = $this->soldQuantities[$p['id']] ?? 0;
                ChiTietCaLam::where('ca_lam_viec_id', $this->shiftId)
                    ->where('san_pham_id', $p['id'])
                    ->update([
                        'so_luong_giao_ca' => $this->closingStock[$p['id']],
                        'so_luong_ban' => $sold
                    ]);
                    
                // Update Daily Stock (TonKhoDiemBan) as well to keep it in sync
                DB::table('ton_kho_diem_ban')
                    ->where('diem_ban_id', $this->shift->diem_ban_id)
                    ->where('san_pham_id', $p['id'])
                    ->where('ngay', Carbon::today())
                    ->update(['ton_cuoi_ca' => $this->closingStock[$p['id']]]);
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
        
        $text = "📊 BÁO CÁO CHỐT CA - $date\n";
        $text .= "━━━━━━━━━━━━━━━━━━━━\n";
        $text .= "👤 Nhân viên: $userName\n";
        $text .= "🕒 $shiftName\n\n";
        
        $text .= "💰 TIỀN MẶT\n";
        $text .= "Hiện tại: " . number_format($this->tienMat) . "đ\n\n";
        
        $text .= "📝 ĐƠN HÀNG\n";
        $text .= "💵 TM: {$this->cashSalesCount} đơn - " . number_format($this->cashSalesTotal) . "đ\n";
        $text .= "💳 CK: {$this->transferSalesCount} đơn - " . number_format($this->transferSalesTotal) . "đ\n\n";
        
        $text .= "📦 TỒN KHO (So với đầu ca)\n";
        foreach ($this->products as $p) {
            $dauCa = $p['ton_dau_ca'];
            $cuoiCa = $this->closingStock[$p['id']] ?? 0;
            $ban = $dauCa - $cuoiCa;
            
            $text .= "• {$p['ten_san_pham']}: {$cuoiCa} (bán {$ban})\n";
        }
        
        if (!empty($this->ghiChu)) {
            $text .= "\n📌 GHI CHÚ\n";
            $text .= $this->ghiChu . "\n";
        }
        
        $this->dispatch('copy-to-clipboard', text: $text);
        $this->dispatch('show-alert', [
            'type' => 'success',
            'message' => 'Đã copy báo cáo!'
        ]);
    }

    public function render()
    {
        return view('livewire.admin.shift.shift-closing');
    }

    private function resizeAndStore($photo, $folder, $manager)
    {
        $filename = md5($photo->getClientOriginalName() . time()) . '.jpg';
        $path = $folder . '/' . $filename;
        $fullPath = storage_path('app/public/' . $path);
        
        // Ensure directory exists
        if (!file_exists(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0755, true);
        }

        // Read image from temporary file
        $image = $manager->read($photo->getRealPath());

        // Resize: Max width 1024, constrain aspect ratio
        $image->scale(width: 1024);

        // Encode to JPG with 75% quality and save
        $image->toJpeg(75)->save($fullPath);

        return $path;
    }
}
