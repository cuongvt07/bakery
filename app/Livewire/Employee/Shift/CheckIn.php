<?php

namespace App\Livewire\Employee\Shift;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\CaLamViec;
use App\Models\NhanVienDiemBan;
use App\Models\PhanBoHangDiemBan;
use App\Models\ChiTietCaLam;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use Livewire\WithFileUploads;

#[Layout('components.layouts.mobile')]
class CheckIn extends Component
{
    use WithFileUploads;

    public $hasActiveShift = false;
    public $isCheckedIn = false;
    public $shift;
    
    // Inputs
    public $openingCash = 0;
    public $receivedStock = []; // [product_id => quantity]
    public $maxStock = []; // [product_id => max_quantity]
    public $products = [];
    public $checkinImages = []; // Array of images
    public $ghi_chu = ''; // Check-in notes
    
    // Multi-shift selection
    public $todayShifts = [];
    public $showShiftSelection = false;
    
    // Unclosed shift handling
    public $hasUnclosedShift = false;
    public $unclosedShift = null;
    public $isLateCheckout = false;
    public $showCheckoutPrompt = false;
    
    // Checkout handling for non-sales
    public $showCheckoutConfirm = false;
    public $checkoutWarningType = null; // 'early', 'late', 'normal'
    public $checkoutWarningMessage = '';
    public $isOvertime = false; // OT checkbox
    
    // Shift availability
    public $hasRegisteredShifts = false;
    
    // Check-in type (sales, production, office)
    public $checkinType = 'office';
    
    public function mount()
    {
        $this->checkinType = Auth::user()->getCheckinType();
        $this->checkShiftStatus();
    }
    
    public function checkShiftStatus()
    {
        $user = Auth::user();
        
        // 1. Check for active shift
        $this->shift = CaLamViec::with('shiftTemplate')
            ->where('nguoi_dung_id', $user->id)
            ->where('trang_thai', 'dang_lam')
            ->first();
            
        if ($this->shift) {
            $this->hasActiveShift = true;
            $this->isCheckedIn = $this->shift->trang_thai_checkin;
            
            // Check user's check-in type
            $checkinType = $user->getCheckinType();
            
            // Load stock only for sales staff
            if (!$this->isCheckedIn && $checkinType === 'sales') {
                $this->loadDistributedStock();
            }
        } else {
            $this->checkTodayShifts();
        }
    }

    public function checkTodayShifts() 
    {
        // First check for unclosed shifts from previous days or earlier today
        $unclosed = CaLamViec::where('nguoi_dung_id', Auth::id())
            ->where('trang_thai_checkin', true)
            ->where('trang_thai', 'dang_lam')
            ->whereDate('ngay_lam', '<=', Carbon::today())
            ->orderBy('ngay_lam', 'desc')
            ->orderBy('gio_bat_dau', 'desc')
            ->first();
        
        if ($unclosed) {
            $this->unclosedShift = $unclosed;
            $this->hasUnclosedShift = true;
            
            // Check if checkout is late (>15 minutes after expected checkout)
            $expectedCheckoutTime = $unclosed->expected_checkout_time;
            $gracePeriodEnd = $expectedCheckoutTime->copy()->addMinutes(15);
            $this->isLateCheckout = now()->gt($gracePeriodEnd);
            
            $this->showCheckoutPrompt = true;
            return;
        }
        
        // Get registered shifts from shift_schedules table
        $registeredShifts = \App\Models\ShiftSchedule::with(['agency', 'shiftTemplate'])
            ->where('nguoi_dung_id', Auth::id())
            ->whereDate('ngay_lam', Carbon::today())
            ->whereIn('trang_thai', ['approved', 'pending'])
            ->orderBy('gio_bat_dau')
            ->get();
        
        // Filter out shifts that have been checked in and completed
        $shifts = $registeredShifts->filter(function($shift) {
            // Check if this shift has a corresponding ca_lam_viec record
            $caLamViec = \App\Models\CaLamViec::where('shift_template_id', $shift->shift_template_id)
                ->where('nguoi_dung_id', $shift->nguoi_dung_id)
                ->where('diem_ban_id', $shift->diem_ban_id)
                ->whereDate('ngay_lam', $shift->ngay_lam)
                ->first();
            
            // If no ca_lam_viec exists, shift is available for check-in
            if (!$caLamViec) {
                return true;
            }
            
            // If ca_lam_viec exists, only show if not completed
            return $caLamViec->trang_thai !== 'da_ket_thuc' && !$caLamViec->phieuChotCa;
        });

        if ($shifts->count() >= 1) {
            $this->todayShifts = $shifts;
            $this->showShiftSelection = true;
        }
        
        // Set hasRegisteredShifts based on filtered shifts (shifts not yet completed)
        $this->hasRegisteredShifts = $shifts->count() > 0;
    }
    
    public function startShift()
    {
        // Check for unclosed shifts first
        $unclosed = CaLamViec::where('nguoi_dung_id', Auth::id())
            ->where('trang_thai_checkin', true)
            ->where('trang_thai', 'dang_lam')
            ->whereDate('ngay_lam', '<=', Carbon::today())
            ->first();
        
        if ($unclosed) {
            $this->unclosedShift = $unclosed;
            $this->hasUnclosedShift = true;
            
            // Check if checkout is late
            $expectedCheckoutTime = $unclosed->expected_checkout_time;
            $gracePeriodEnd = $expectedCheckoutTime->copy()->addMinutes(15);
            $this->isLateCheckout = now()->gt($gracePeriodEnd);
            
            $this->showCheckoutPrompt = true;
            session()->flash('warning', 'Bạn cần chốt ca trước đó trước khi bắt đầu ca mới!');
            return;
        }
        
        // 1. Get registered shifts from shift_schedules table
        $registeredShifts = \App\Models\ShiftSchedule::with(['agency', 'shiftTemplate'])
            ->where('nguoi_dung_id', Auth::id())
            ->whereDate('ngay_lam', Carbon::today())
            ->whereIn('trang_thai', ['approved', 'pending'])
            ->orderBy('gio_bat_dau')
            ->get();
        
        // Filter out shifts that have been checked in and completed
        $shifts = $registeredShifts->filter(function($shift) {
            $caLamViec = \App\Models\CaLamViec::where('shift_template_id', $shift->shift_template_id)
                ->where('nguoi_dung_id', $shift->nguoi_dung_id)
                ->where('diem_ban_id', $shift->diem_ban_id)
                ->whereDate('ngay_lam', $shift->ngay_lam)
                ->first();
            
            if (!$caLamViec) {
                return true;
            }
            
            return $caLamViec->trang_thai !== 'da_ket_thuc' && !$caLamViec->phieuChotCa;
        });
            
        if ($shifts->isEmpty()) {
            $this->hasRegisteredShifts = false;
            session()->flash('error', 'Bạn chưa đăng ký ca làm việc cho ngày hôm nay!');
            return;
        }
        
        // 2. Always show shift selection modal
        $this->hasRegisteredShifts = true;
        $this->todayShifts = $shifts;
        $this->showShiftSelection = true;
    }

    public function selectShift($shiftId)
    {
        $selectedShift = \App\Models\ShiftSchedule::find($shiftId);
        if ($selectedShift) {
            $this->createSession($selectedShift);
        }
    }
    
    
    private function createSession($schedule)
    {
        // Get checkinType directly from user to ensure it's always correct
        $checkinType = Auth::user()->getCheckinType();
        
        // For sales staff: show full check-in form (cash, images, stock)
        if ($checkinType === 'sales') {
            $this->shift = CaLamViec::create([
                'diem_ban_id' => $schedule->diem_ban_id,
                'nguoi_dung_id' => Auth::id(),
                'ngay_lam' => now(),
                'gio_bat_dau' => $schedule->gio_bat_dau, // Use schedule time
                'gio_ket_thuc' => $schedule->gio_ket_thuc, // Use schedule time
                'trang_thai' => 'dang_lam',
                'trang_thai_checkin' => false,
                'shift_template_id' => $schedule->shift_template_id,
                'ghi_chu' => $this->ghi_chu,
            ]);
            
            // Load distributed stock for confirmation
            $this->loadDistributedStock();
            
            // Hide modal and show check-in form
            $this->showShiftSelection = false;
            $this->todayShifts = [];
            $this->hasActiveShift = true;
            $this->isCheckedIn = false;
            return;
        }
        
        // For production staff: show simple check-in form (images + notes only)
        if ($checkinType === 'production') {
            $this->shift = CaLamViec::create([
                'diem_ban_id' => $schedule->diem_ban_id,
                'nguoi_dung_id' => Auth::id(),
                'ngay_lam' => now(),
                'gio_bat_dau' => $schedule->gio_bat_dau, // Use schedule time
                'gio_ket_thuc' => $schedule->gio_ket_thuc, // Use schedule time
                'trang_thai' => 'dang_lam',
                'trang_thai_checkin' => false,
                'shift_template_id' => $schedule->shift_template_id,
                'ghi_chu' => $this->ghi_chu,
            ]);
            
            // Hide modal and show check-in form
            $this->showShiftSelection = false;
            $this->todayShifts = [];
            $this->hasActiveShift = true;
            $this->isCheckedIn = false;
            return;
        }
        
        // For office staff: auto check-in and redirect to dashboard
        $shift = CaLamViec::create([
            'diem_ban_id' => $schedule->diem_ban_id,
            'nguoi_dung_id' => Auth::id(),
            'ngay_lam' => now(),
            'gio_bat_dau' => $schedule->gio_bat_dau, // Use schedule time
            'gio_ket_thuc' => $schedule->gio_ket_thuc, // Use schedule time
            'trang_thai' => 'dang_lam',
            'trang_thai_checkin' => true, // Auto check-in for office
            'thoi_gian_checkin' => now(),
            'shift_template_id' => $schedule->shift_template_id,
            'ghi_chu' => $this->ghi_chu,
        ]);
        
        $this->showShiftSelection = false;
        $this->todayShifts = [];
        $this->checkShiftStatus();
    }
    
    public function loadDistributedStock()
    {
        $agencyId = $this->shift->diem_ban_id;
        
        // Find all distributions for this agency, today only, with status 'chua_nhan'
        $distributions = PhanBoHangDiemBan::with(['product'])
            ->where('diem_ban_id', $agencyId)
            ->whereDate('created_at', Carbon::today())
            ->where('trang_thai', 'chua_nhan')
            ->get();
            
        $this->products = [];
        $this->receivedStock = [];
        $this->maxStock = [];
        
        foreach ($distributions as $dist) {
            if ($dist->product) {
                $product = $dist->product;
                
                // Add to products list if not already there
                if (!isset($this->receivedStock[$product->id])) {
                    $this->products[] = $product;
                    $this->receivedStock[$product->id] = null; // Default to null for placeholder behavior
                    $this->maxStock[$product->id] = 0;
                }
                
                // Add quantity from this distribution
                $this->maxStock[$product->id] += $dist->so_luong;
            }
        }
    }
    
    public function fillMaxStock($productId)
    {
        if (isset($this->maxStock[$productId])) {
            $this->receivedStock[$productId] = $this->maxStock[$productId];
        }
    }

    public function deleteImage($index)
    {
        if (isset($this->checkinImages[$index])) {
            array_splice($this->checkinImages, $index, 1);
        }
    }
    
    public function confirmCheckIn()
    {
        $user = Auth::user();
        $checkinType = $user->getCheckinType();

        $rules = [
            'checkinImages.*' => 'image|max:10240',
            'checkinImages' => 'nullable|array',
        ];

        // Sales: require money and stock
        if ($checkinType === 'sales') {
            $rules['openingCash'] = 'required|numeric|min:0';
            $rules['receivedStock.*'] = 'nullable|numeric|min:0';
        }
        
        // Production: no additional requirements (just photos)
        // Office: no additional requirements

        $this->validate($rules, [
            'openingCash.required' => 'Vui lòng nhập tiền mặt đầu ca.',
        ]);
        
        DB::transaction(function () use ($checkinType) {
            // Handle Image Uploads with Resizing (reduces from ~5MB to ~200KB)
            $imagePaths = [];
            if ($this->checkinImages) {
                $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
                
                foreach ($this->checkinImages as $photo) {
                    $imagePaths[] = $this->resizeAndStoreCheckInImage($photo, $manager);
                }
            }

            // 1. Update Shift
            $updateData = [
                'trang_thai_checkin' => true,
                'thoi_gian_checkin' => now(),
                'hinh_anh_checkin' => !empty($imagePaths) ? json_encode($imagePaths) : null,
                'ghi_chu' => $this->ghi_chu,
            ];

            if ($checkinType === 'sales') {
                $updateData['tien_mat_dau_ca'] = $this->openingCash;
            }

            $this->shift->update($updateData);
            
            if ($checkinType === 'sales') {
                // 2. Create Shift Details (ChiTietCaLam)
                foreach ($this->products as $p) {
                    // Treat null as 0
                    $qty = $this->receivedStock[$p->id] ? $this->receivedStock[$p->id] : 0;
                    
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
            }
        });
        
        session()->flash('success', 'Check-in thành công!');
        
        // Redirect logic based on check-in type
        if ($checkinType === 'sales') {
            return $this->redirect(route('employee.pos'), navigate: true);
        } else {
            return $this->redirect(route('employee.dashboard'), navigate: true);
        }
    }
    
    /**
     * Resize and compress check-in images to reduce storage
     * Target: < 300KB per image (from ~3-8MB on mobile)
     */
    private function resizeAndStoreCheckInImage($photo, $manager)
    {
        $filename = md5($photo->getClientOriginalName() . time() . rand()) . '.jpg';
        $path = 'checkin-photos/' . $filename;
        $fullPath = storage_path('app/public/' . $path);
        
        // Ensure directory exists
        if (!file_exists(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0755, true);
        }

        try {
            // Read image from temporary file
            $image = $manager->read($photo->getRealPath());

            // Resize: Max width 1024px (good balance between quality and size)
            // Images from phones are usually 3000x4000px, so this is a big reduction
            $image->scale(width: 1024);

            // Encode to JPG with 75% quality and save
            // 75% is sweet spot: good quality, small file size
            $image->toJpeg(75)->save($fullPath);

            return $path;
        } catch (\Exception $e) {
            // Fallback to original if resize fails
            \Log::error('Check-in image resize failed: ' . $e->getMessage());
            return $photo->store('checkin-photos', 'public');
        }
    }
    
    /**
     * Force checkout the unclosed shift
     */
    public function forceCheckout()
    {
        if (!$this->unclosedShift) {
            return;
        }
        
        DB::transaction(function () {
            $ghi_chu = $this->isLateCheckout 
                ? 'Chốt ca muộn - Quên chốt ca'
                : 'Chốt ca trước khi bắt đầu ca mới';
            
            // Create phieu chot ca
            $maPhieu = 'PC-' . $this->unclosedShift->ngay_lam->format('Ymd') . '-' . str_pad($this->unclosedShift->id, 4, '0', STR_PAD_LEFT);
            
            \App\Models\PhieuChotCa::create([
                'ma_phieu' => $maPhieu,
                'diem_ban_id' => $this->unclosedShift->diem_ban_id,
                'nguoi_chot_id' => Auth::id(),
                'ca_lam_viec_id' => $this->unclosedShift->id,
                'ngay_chot' => now()->toDateString(),
                'gio_chot' => now()->toTimeString(),
                'tien_mat' => $this->unclosedShift->tien_mat_dau_ca ?? 0,
                'tien_chuyen_khoan' => 0,
                'tong_tien_thuc_te' => $this->unclosedShift->tien_mat_dau_ca ?? 0,
                'tong_tien_ly_thuyet' => 0,
                'tien_lech' => 0,
                'ghi_chu' => $ghi_chu,
                'trang_thai' => 'cho_duyet',
                'ot' => $this->isOvertime,
            ]);
            
            // Update shift status
            $this->unclosedShift->update([
                'trang_thai' => 'da_ket_thuc',
            ]);
        });
        
        session()->flash('success', 'Đã chốt ca thành công!');
        $this->reset(['unclosedShift', 'hasUnclosedShift', 'isLateCheckout', 'showCheckoutPrompt']);
        $this->checkShiftStatus();
    }
    
    /**
     * Initiate checkout for non-sales staff
     */
    public function initiateCheckout()
    {
        if (!$this->shift) {
            return;
        }
        
        // Calculate expected checkout time based on actual check-in
        $expectedCheckoutTime = $this->shift->expected_checkout_time;
        $now = now();
        $gracePeriodEnd = $expectedCheckoutTime->copy()->addMinutes(15); // 15 minutes grace period
        
        // Determine checkout type
        if ($now->lt($expectedCheckoutTime)) {
            // Too early (before expected checkout)
            $totalMinutes = (int) $now->diffInMinutes($expectedCheckoutTime);
            $timeDisplay = $this->formatMinutes($totalMinutes);
            $this->checkoutWarningType = 'early';
            $this->checkoutWarningMessage = "Bạn đang chốt ca sớm {$timeDisplay}. Bạn có chắc muốn chốt ca không?";
        } elseif ($now->gt($gracePeriodEnd)) {
            // Too late (more than 15 min after expected checkout)
            $totalMinutes = (int) $expectedCheckoutTime->diffInMinutes($now);
            $timeDisplay = $this->formatMinutes($totalMinutes);
            $this->checkoutWarningType = 'late';
            $this->checkoutWarningMessage = "Bạn đang chốt ca muộn {$timeDisplay}. Phiếu chốt ca sẽ được ghi chú: 'Chốt ca muộn - Quên chốt ca'.";
        } else {
            // Normal checkout (within grace period)
            $this->checkoutWarningType = 'normal';
            $this->checkoutWarningMessage = 'Xác nhận chốt ca?';
        }
        
        $this->showCheckoutConfirm = true;
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
    
    /**
     * Confirm checkout
     */
    public function confirmCheckout()
    {
        if (!$this->shift) {
            return;
        }
        
        DB::transaction(function () {
            $ghi_chu = match($this->checkoutWarningType) {
                'early' => 'Chốt ca sớm',
                'late' => 'Chốt ca muộn - Quên chốt ca',
                default => 'Chốt ca bình thường',
            };
            
            // Create phieu chot ca
            $maPhieu = 'PC-' . $this->shift->ngay_lam->format('Ymd') . '-' . str_pad($this->shift->id, 4, '0', STR_PAD_LEFT);
            
            \App\Models\PhieuChotCa::create([
                'ma_phieu' => $maPhieu,
                'diem_ban_id' => $this->shift->diem_ban_id,
                'nguoi_chot_id' => Auth::id(),
                'ca_lam_viec_id' => $this->shift->id,
                'ngay_chot' => now()->toDateString(),
                'gio_chot' => now()->toTimeString(),
                'tien_mat' => $this->shift->tien_mat_dau_ca ?? 0,
                'tien_chuyen_khoan' => 0,
                'tong_tien_thuc_te' => $this->shift->tien_mat_dau_ca ?? 0,
                'tong_tien_ly_thuyet' => 0,
                'tien_lech' => 0,
                'ghi_chu' => $ghi_chu,
                'trang_thai' => 'cho_duyet',
                'ot' => $this->isOvertime,
            ]);
            
            // Update ca_lam_viec status only (not shift_schedules)
            $this->shift->update([
                'trang_thai' => 'da_ket_thuc',
            ]);
        });
        
        // Reset state
        $this->reset(['shift', 'hasActiveShift', 'isCheckedIn', 'showCheckoutConfirm', 'checkoutWarningType', 'checkoutWarningMessage']);
        
        session()->flash('success', 'Đã chốt ca thành công!');
        return $this->redirect(route('employee.dashboard'), navigate: true);
    }
    
    public function render()
    {
        return view('livewire.employee.shift.check-in');
    }
}
