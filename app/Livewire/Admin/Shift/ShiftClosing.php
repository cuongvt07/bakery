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
    #[\Livewire\Attributes\Url]
    public $confirm_closing = null;

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

    /**
     * Auto-close all unclosed shifts before today
     * Sets checkout time = checkin time so total working hours = 0
     */
    private function autoCloseOldShifts()
    {
        $oldShifts = CaLamViec::where('nguoi_dung_id', Auth::id())
            ->where('trang_thai', 'dang_lam')
            ->where('trang_thai_checkin', true)
            ->whereDate('ngay_lam', '<', Carbon::today())
            ->orderBy('ngay_lam', 'asc')
            ->get();

        if ($oldShifts->isEmpty()) {
            \Log::info('ShiftClosing: No old unclosed shifts found');
            return;
        }

        \Log::info('ShiftClosing: Auto-closing old shifts', [
            'count' => $oldShifts->count(),
            'shift_ids' => $oldShifts->pluck('id')->toArray()
        ]);

        DB::transaction(function () use ($oldShifts) {
            foreach ($oldShifts as $shift) {
                // Create PhieuChotCa with checkout time = checkin time (total hours = 0)
                $phieu = new PhieuChotCa();
                $phieu->ma_phieu = 'PCC-AUTO-' . $shift->id . '-' . time();
                $phieu->diem_ban_id = $shift->diem_ban_id;
                $phieu->nguoi_chot_id = Auth::id();
                $phieu->ca_lam_viec_id = $shift->id;

                // Use checkin time as checkout time (total hours = 0)
                $checkoutTime = $shift->thoi_gian_checkin ?? now();
                $phieu->ngay_chot = $checkoutTime->toDateString();
                $phieu->gio_chot = $checkoutTime->toTimeString();

                $phieu->tien_mat = $shift->tien_mat_dau_ca ?? 0;
                $phieu->tien_chuyen_khoan = 0;
                $phieu->tong_tien_thuc_te = $shift->tien_mat_dau_ca ?? 0;
                $phieu->tong_tien_ly_thuyet = 0;
                $phieu->tien_lech = 0;
                $phieu->ton_dau_ca = json_encode([]);
                $phieu->ton_cuoi_ca = json_encode([]);
                $phieu->ghi_chu = 'Tự động chốt - Quên chốt ca (Giờ checkout = Giờ checkin, Tổng công = 0)';
                $phieu->trang_thai = 'cho_duyet';
                $phieu->ot = false;

                $phieu->save();

                // Update shift status AND set total hours to 0
                $shift->trang_thai = 'da_ket_thuc';
                $shift->tong_gio_lam_viec = 0; // Checkout = Checkin → Total hours = 0
                $shift->save();

                \Log::info('ShiftClosing: Auto-closed shift', [
                    'shift_id' => $shift->id,
                    'ngay_lam' => $shift->ngay_lam->format('Y-m-d'),
                    'phieu_id' => $phieu->id,
                    'tong_gio_lam_viec' => 0
                ]);
            }
        });
    }

    /**
     * Create check-in for current shift if employee forgot to check in
     */
    private function createCheckInForCurrentShift()
    {
        // Strategy: Look for a schedule that SHOULD have been checked in but wasn't.
        // 1. Look for today's schedule
        $query = \App\Models\ShiftSchedule::with(['agency', 'shiftTemplate'])
            ->where('nguoi_dung_id', Auth::id())
            ->whereIn('trang_thai', ['approved', 'pending']);

        // Handle overnight/late shifts: If check-in is early morning (00:00 - 06:00), 
        // also check yesterday's schedules that might have ended late.
        if (now()->hour < 6) {
            $schedule = (clone $query)->whereDate('ngay_lam', Carbon::today()->subDay())
                ->orderBy('gio_ket_thuc', 'desc')
                ->first();

            if (!$schedule) {
                // If no yesterday schedule, check today
                $schedule = (clone $query)->whereDate('ngay_lam', Carbon::today())
                    ->orderBy('gio_bat_dau', 'asc')
                    ->first();
            }
        } else {
            // Normal case: Check today
            $schedule = $query->whereDate('ngay_lam', Carbon::today())
                ->orderBy('gio_bat_dau', 'asc')
                ->first();
        }

        if (!$schedule) {
            \Log::info('ShiftClosing: No registered shift found for auto-checkin');
            return null;
        }

        // Check if ca_lam_viec already exists for this schedule
        $existingShift = CaLamViec::where('shift_template_id', $schedule->shift_template_id)
            ->where('nguoi_dung_id', $schedule->nguoi_dung_id)
            ->where('diem_ban_id', $schedule->diem_ban_id)
            ->whereDate('ngay_lam', $schedule->ngay_lam)
            ->first();

        if ($existingShift) {
            return $existingShift;
        }

        \Log::info('ShiftClosing: Creating auto check-in', [
            'schedule_id' => $schedule->id,
            'date' => $schedule->ngay_lam
        ]);

        // Create new shift with auto check-in "Late"
        // Set check-in time = now (Closing time) so duration is effectively 0
        $shift = CaLamViec::create([
            'diem_ban_id' => $schedule->diem_ban_id,
            'nguoi_dung_id' => Auth::id(),
            'ngay_lam' => $schedule->ngay_lam, // Use schedule date (could be yesterday)
            'gio_bat_dau' => $schedule->gio_bat_dau,
            'gio_ket_thuc' => $schedule->gio_ket_thuc,
            'trang_thai' => 'dang_lam',
            'trang_thai_checkin' => true,
            'thoi_gian_checkin' => now(),
            'shift_template_id' => $schedule->shift_template_id,
            'ghi_chu' => 'Quên check-in - Tự động tạo khi chốt ca',
        ]);

        \Log::info('ShiftClosing: Created shift', ['shift_id' => $shift->id]);

        return $shift;
    }

    public function mount()
    {
        // DEBUG: Log entry point
        \Log::info('ShiftClosing mount() called', [
            'user_id' => Auth::id(),
            'user_role' => Auth::user()->vai_tro,
            'confirm_closing_property' => $this->confirm_closing,
            'has_confirm_closing_request' => request()->has('confirm_closing'),
            'confirm_closing_value' => request()->get('confirm_closing'),
            'all_params' => request()->all()
        ]);

        // STEP 0: Auto-close old unclosed shifts and create check-in for current shift if needed
        $this->autoCloseOldShifts();
        $autoCreatedShift = $this->createCheckInForCurrentShift();

        // 1. Get all unclosed shifts (Today and Yesterday)
        $unclosedShifts = CaLamViec::where('nguoi_dung_id', Auth::id())
            ->where('trang_thai', 'dang_lam')
            ->where('trang_thai_checkin', true)
            ->whereDate('ngay_lam', '>=', Carbon::today()->subDay())
            ->orderBy('thoi_gian_checkin', 'asc') // Oldest first
            ->get();

        // If we just auto-created a shift and it's not in the list (rare but possible due to timing/cache), use it directly
        if ($unclosedShifts->isEmpty() && $autoCreatedShift) {
            $this->shift = $autoCreatedShift;
            \Log::info('ShiftClosing: Using auto-created shift directly', ['shift_id' => $this->shift->id]);
        } elseif ($unclosedShifts->isEmpty()) {
            \Log::info('ShiftClosing: No unclosed shifts found');
            session()->flash('error', 'Không có ca làm việc nào đang hoạt động!');
            return $this->redirect(route('admin.shift.check-in'));
        } else {
            // 2. If multiple shifts today → Show selector
            if ($unclosedShifts->count() > 1) {
                \Log::info('ShiftClosing: Multiple shifts found, showing selector');
                $this->unclosedShifts = $unclosedShifts;
                $this->showShiftSelector = true;
                return;
            }
            // 3. Only one shift → Load it directly
            $this->shift = $unclosedShifts->first();
        }

        \Log::info('ShiftClosing: Shift loaded', [
            'shift_id' => $this->shift->id,
            'trang_thai_checkin' => $this->shift->trang_thai_checkin,
            'confirm_closing_property' => $this->confirm_closing
        ]);

        // 2. If checked in but not explicitly closing, redirect to POS
        // User should be at POS selling, not at closing page by accident
        if ($this->shift->trang_thai_checkin && !$this->confirm_closing) {
            \Log::info('ShiftClosing: Missing confirm_closing parameter, redirecting to POS');
            if (Auth::user()->vai_tro === 'nhan_vien') {
                return $this->redirect(route('employee.pos'));
            }
            return $this->redirect(route('admin.pos.quick-sale'));
        }

        // 3. Must be checked in to close shift
        if (!$this->shift->trang_thai_checkin) {
            \Log::info('ShiftClosing: Not checked in, redirecting to check-in');
            session()->flash('error', 'Vui lòng check-in trước khi chốt ca!');
            return $this->redirect(route('admin.shift.check-in'));
        }

        \Log::info('ShiftClosing: All checks passed, loading shift closing form');

        $this->shiftId = $this->shift->id;

        // 4. Load products using Eloquent with accessor
        $details = ChiTietCaLam::where('ca_lam_viec_id', $this->shiftId)
            ->with('sanPham')
            ->get()
            ->map(function ($item) {
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

    // Checkout warning
    public $showCheckoutWarning = false;
    public $checkoutWarningType = null; // 'early', 'late', 'normal'
    public $checkoutWarningMessage = '';
    public $isOvertime = false;

    // Multi-shift handling (same day only)
    public $unclosedShifts = [];
    public $showShiftSelector = false;
    public $hasOlderUnclosedShift = false;
    public $olderShiftWarning = '';

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

    /**
     * Select which shift to close (when multiple shifts exist)
     */
    public function selectShiftToClose($shiftId)
    {
        $this->shift = CaLamViec::find($shiftId);

        if (!$this->shift) {
            session()->flash('error', 'Không tìm thấy ca làm việc!');
            return;
        }

        // Check if there's an older unclosed shift (warning only, not blocking)
        $olderShift = CaLamViec::where('nguoi_dung_id', Auth::id())
            ->where('trang_thai', 'dang_lam')
            ->where('trang_thai_checkin', true)
            ->whereDate('ngay_lam', Carbon::today())
            ->where('thoi_gian_checkin', '<', $this->shift->thoi_gian_checkin)
            ->orderBy('thoi_gian_checkin', 'asc')
            ->first();

        if ($olderShift) {
            $this->hasOlderUnclosedShift = true;
            $this->olderShiftWarning = "Bạn có ca " .
                Carbon::parse($olderShift->gio_bat_dau)->format('H:i') .
                " (check-in lúc " . $olderShift->thoi_gian_checkin->format('H:i') .
                ") chưa chốt. Bạn có chắc muốn chốt ca này trước?";
        }

        $this->showShiftSelector = false;
        $this->shiftId = $this->shift->id;

        // Continue with normal mount flow
        if ($this->shift->trang_thai_checkin && !$this->confirm_closing) {
            if (Auth::user()->vai_tro === 'nhan_vien') {
                return $this->redirect(route('employee.pos'));
            }
            return $this->redirect(route('admin.pos.quick-sale'));
        }

        if (!$this->shift->trang_thai_checkin) {
            session()->flash('error', 'Vui lòng check-in trước khi chốt ca!');
            return $this->redirect(route('admin.shift.check-in'));
        }

        // Load products and sales summary
        $details = ChiTietCaLam::where('ca_lam_viec_id', $this->shiftId)
            ->with('sanPham')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->san_pham_id,
                    'ten_san_pham' => $item->sanPham->ten_san_pham,
                    'ma_san_pham' => $item->sanPham->ma_san_pham,
                    'gia_ban' => $item->sanPham->gia_ban,
                    'ton_dau_ca' => $item->so_luong_nhan_ca,
                    'so_luong_ban' => $item->so_luong_ban,
                    'so_luong_con_lai' => $item->so_luong_con_lai,
                ];
            })
            ->toArray();

        $this->products = $details;

        if (empty($this->closingStock)) {
            foreach ($this->products as $p) {
                $this->closingStock[$p['id']] = $p['so_luong_con_lai'];
            }
        }

        $this->loadSalesSummary();
        $this->calculate();
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
        $openingCash = (float) ($this->shift->tien_mat_dau_ca ?? 0);
        $cashSalesTotal = (float) $this->cashSalesTotal; // From confirmed pending sales
        $this->expectedCash = $openingCash + $cashSalesTotal;

        // Actual Total = Cash Holding + Transfer Total
        $cashHolding = (float) $this->tienMat;
        $transferTotal = (float) $this->transferSalesTotal;

        $this->totalActual = $cashHolding + $transferTotal;

        $this->discrepancy = $this->totalActual - $this->totalTheoretical;
    }

    /**
     * Initiate checkout with warning check
     */
    public function initiateSubmit()
    {
        // Validate first
        $this->validate([
            'tienMat' => 'required|numeric|min:0',
            'tienChuyenKhoan' => 'required|numeric|min:0',
            'closingStock.*' => 'required|numeric|min:0',
            'photosCash.*' => 'image|max:10240',
            'photosStock.*' => 'image|max:10240',
        ]);

        // Check expected checkout time
        $expectedCheckoutTime = $this->shift->expected_checkout_time;
        $now = now();
        $gracePeriodEnd = $expectedCheckoutTime->copy()->addMinutes(15);

        if ($now->lt($expectedCheckoutTime)) {
            // Early checkout
            $totalMinutes = (int) $now->diffInMinutes($expectedCheckoutTime);
            $timeDisplay = $this->formatMinutes($totalMinutes);
            $this->checkoutWarningType = 'early';
            $this->checkoutWarningMessage = "Bạn đang chốt ca sớm {$timeDisplay}. Bạn có chắc muốn chốt ca không?";
        } elseif ($now->gt($gracePeriodEnd)) {
            // Late checkout
            $totalMinutes = (int) $expectedCheckoutTime->diffInMinutes($now);
            $timeDisplay = $this->formatMinutes($totalMinutes);
            $this->checkoutWarningType = 'late';
            $this->checkoutWarningMessage = "Bạn đang chốt ca muộn {$timeDisplay}. Phiếu chốt ca sẽ được ghi chú: 'Chốt ca muộn - Quên chốt ca'.";
        } else {
            // Normal checkout
            $this->checkoutWarningType = 'normal';
            $this->checkoutWarningMessage = 'Xác nhận chốt ca?';
        }

        $this->showCheckoutWarning = true;
    }

    /**
     * Format minutes to readable time
     */
    private function formatMinutes(int $minutes): string
    {
        if ($minutes < 60) {
            return "{$minutes} phút";
        }

        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;

        if ($remainingMinutes === 0) {
            return "{$hours} giờ";
        }

        return "{$hours} giờ {$remainingMinutes} phút";
    }

    // Generate text report for easy copy-paste
    public function generateReport()
    {
        $cashHolding = (float) ($this->tienMat ?? 0);
        $openingCash = (float) ($this->shift->tien_mat_dau_ca ?? 0);
        $cashRevenue = $cashHolding - $openingCash;

        $report = "📊 BÁO CÁO CHỐT CA\n\n";

        // Cash breakdown
        $report .= "💰 TIỀN MẶT:\n";
        $report .= "- Tiền đầu ca: " . number_format($openingCash / 1000, 0) . "k\n";
        $report .= "- Tiền đang giữ: " . number_format($cashHolding / 1000, 0) . "k\n";
        $report .= "- Doanh thu TM: " . number_format($cashRevenue / 1000, 0) . "k\n";

        // Transfer info
        if ($this->transferSalesCount > 0) {
            $report .= "\n💳 CHUYỂN KHOẢN:\n";
            $report .= "- " . $this->transferSalesCount . " đơn - " . number_format($this->transferSalesTotal / 1000, 0) . "k\n";
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
        $report .= "- Lý thuyết: " . number_format($this->totalTheoretical / 1000, 0) . "k\n";
        $report .= "- Thực tế: " . number_format($this->totalActual / 1000, 0) . "k\n";
        $report .= "- Chênh lệch: " . number_format($this->discrepancy / 1000, 0) . "k" . ($this->discrepancy == 0 ? " ✅" : "") . "\n";

        return $report;
    }

    use \Livewire\WithFileUploads;

    public $photosCash = []; // Array of UploadedFile
    public $photosStock = []; // Array of UploadedFile

    public function submit()
    {
        // Skip validation if already validated in initiateSubmit
        if (!$this->showCheckoutWarning) {
            $this->validate([
                'tienMat' => 'required|numeric|min:0',
                'tienChuyenKhoan' => 'required|numeric|min:0',
                'closingStock.*' => 'required|numeric|min:0',
                'photosCash.*' => 'image|max:10240',
                'photosStock.*' => 'image|max:10240',
            ]);
        }

        DB::transaction(function () {
            // Determine note based on checkout type
            $autoNote = match ($this->checkoutWarningType) {
                'early' => 'Chốt ca sớm',
                'late' => 'Chốt ca muộn - Quên chốt ca',
                default => '',
            };

            // Combine auto note with user note
            $finalNote = $autoNote;
            if (!empty($this->ghiChu)) {
                $finalNote .= ($finalNote ? ' - ' : '') . $this->ghiChu;
            }
            // 1. Create PhieuChotCa
            $phieu = new PhieuChotCa();
            $phieu->ma_phieu = 'PCC-' . time();
            $phieu->diem_ban_id = $this->shift->diem_ban_id;
            $phieu->nguoi_chot_id = Auth::id();
            $phieu->ca_lam_viec_id = $this->shiftId;
            $phieu->ngay_chot = now()->toDateString(); // Use string format
            $phieu->gio_chot = now()->toTimeString(); // Use string format

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

            // Save OT flag
            $phieu->ot = $this->isOvertime;

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
            $phieu->ghi_chu = $finalNote;
            $phieu->trang_thai = 'cho_duyet';
            $phieu->ot = $this->isOvertime;

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

                // Log sales to batch history (FIFO - deduct from oldest batches first)
                if ($sold > 0) {
                    // Get distributions for this product at this shop, ordered by batch production date (oldest first)
                    $distributions = \App\Models\PhanBoHangDiemBan::where('diem_ban_id', $this->shift->diem_ban_id)
                        ->where('san_pham_id', $p['id'])
                        ->whereNotNull('me_san_xuat_id')
                        ->orderBy('created_at', 'asc')
                        ->get();

                    $remainingSold = $sold;
                    foreach ($distributions as $dist) {
                        if ($remainingSold <= 0)
                            break;

                        // Check how much was already sold from this distribution
                        $alreadySold = \App\Models\LichSuCapNhatMe::where('me_san_xuat_id', $dist->me_san_xuat_id)
                            ->where('san_pham_id', $p['id'])
                            ->where('diem_ban_id', $this->shift->diem_ban_id)
                            ->where('loai', \App\Models\LichSuCapNhatMe::LOAI_BAN)
                            ->sum('so_luong_doi'); // Will be negative

                        $availableFromDist = $dist->so_luong + $alreadySold; // so_luong is positive, alreadySold is negative

                        if ($availableFromDist > 0) {
                            $deduct = min($remainingSold, $availableFromDist);

                            \App\Models\LichSuCapNhatMe::create([
                                'me_san_xuat_id' => $dist->me_san_xuat_id,
                                'san_pham_id' => $p['id'],
                                'diem_ban_id' => $this->shift->diem_ban_id,
                                'loai' => \App\Models\LichSuCapNhatMe::LOAI_BAN,
                                'ca_lam_viec_id' => $this->shiftId,
                                'nguoi_cap_nhat_id' => Auth::id(),
                                'so_luong_doi' => -$deduct, // Negative = sold
                                'du_lieu_cu' => $availableFromDist,
                                'du_lieu_moi' => $availableFromDist - $deduct,
                                'ghi_chu' => 'Bán ca ' . ($this->shift->gio_bat_dau < '12:00:00' ? 'sáng' : 'chiều'),
                            ]);

                            $remainingSold -= $deduct;
                        }
                    }
                }
            }
        });

        session()->flash('message', 'Chốt ca thành công! Hệ thống đã ghi nhận.');

        // Redirect based on user role
        if (Auth::user()->vai_tro === 'nhan_vien') {
            return redirect()->route('employee.dashboard');
        }

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
