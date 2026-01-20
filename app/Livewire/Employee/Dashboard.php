<?php

namespace App\Livewire\Employee;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Agency;
use App\Models\ShiftSchedule;
use App\Models\ChamCong;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.mobile')]
class Dashboard extends Component
{
    public $agency;
    public $todayShift;
    public $todayAttendance;
    public $monthlyStats = [];
    public $unclosedShifts = []; // All unclosed shifts (today only)

    public function mount()
    {
        $user = Auth::user();
        
        // Get employee's assigned agency (first agency for now)
        // In future, this should be from employee assignment table
        $this->agency = Agency::first(); // TODO: Get actual assigned agency
        
        // Get all unclosed shifts for TODAY only
        $this->unclosedShifts = \App\Models\CaLamViec::where('nguoi_dung_id', $user->id)
            ->where('trang_thai', 'dang_lam')
            ->where('trang_thai_checkin', true)
            ->whereDate('ngay_lam', Carbon::today())
            ->orderBy('thoi_gian_checkin', 'asc')
            ->get();
        
        // Get all registered shifts for today from shift_schedules
        $registeredShifts = ShiftSchedule::where('nguoi_dung_id', $user->id)
            ->whereDate('ngay_lam', Carbon::today())
            ->whereIn('trang_thai', ['approved', 'pending'])
            ->with('agency')
            ->orderBy('gio_bat_dau')
            ->get();
        
        // Find the active shift (prioritize: not checked in > checked in but not out > next upcoming)
        $this->todayShift = $this->findActiveShift($registeredShifts);
        
        // Get today's attendance (check-in/out) for the active shift
        if ($this->todayShift) {
            $this->todayAttendance = \App\Models\CaLamViec::where('nguoi_dung_id', $user->id)
                ->where('shift_template_id', $this->todayShift->shift_template_id)
                ->where('diem_ban_id', $this->todayShift->diem_ban_id)
                ->whereDate('ngay_lam', Carbon::today())
                ->first();
        }
        
        // Calculate monthly stats
        $this->calculateMonthlyStats();
    }
    
    /**
     * Find the active shift to display
     */
    private function findActiveShift($shifts)
    {
        if ($shifts->isEmpty()) {
            return null;
        }
        
        // Priority 1: Find shift that is checked in but not checked out
        foreach ($shifts as $shift) {
            $caLamViec = \App\Models\CaLamViec::where('nguoi_dung_id', Auth::id())
                ->where('shift_template_id', $shift->shift_template_id)
                ->where('diem_ban_id', $shift->diem_ban_id)
                ->whereDate('ngay_lam', $shift->ngay_lam)
                ->orderBy('thoi_gian_checkin', 'desc') // Get most recent check-in
                ->first();
            
            if ($caLamViec && $caLamViec->trang_thai !== 'da_ket_thuc' && !$caLamViec->phieuChotCa) {
                return $shift; // This shift is in progress
            }
        }
        
        // Priority 2: Find shift that hasn't been checked in yet
        foreach ($shifts as $shift) {
            $caLamViec = \App\Models\CaLamViec::where('nguoi_dung_id', Auth::id())
                ->where('shift_template_id', $shift->shift_template_id)
                ->where('diem_ban_id', $shift->diem_ban_id)
                ->whereDate('ngay_lam', $shift->ngay_lam)
                ->first();
            
            if (!$caLamViec) {
                return $shift; // This shift hasn't started yet
            }
        }
        
        // Priority 3: All shifts are completed, return null
        return null;
    }

    private function calculateMonthlyStats()
    {
        $user = Auth::user();
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        
        $shifts = ShiftSchedule::where('nguoi_dung_id', $user->id)
            ->whereBetween('ngay_lam', [$startOfMonth, $endOfMonth])
            ->get();
        
        $this->monthlyStats = [
            'total_shifts' => $shifts->count(),
            'completed' => $shifts->where('trang_thai', 'da_ket_thuc')->count(),
            'upcoming' => $shifts->where('trang_thai', 'chua_bat_dau')->count(),
            'total_hours' => ChamCong::where('nguoi_dung_id', $user->id)
                ->whereBetween('ngay_cham', [$startOfMonth, $endOfMonth])
                ->sum('tong_gio_lam'),
        ];
    }

    public function checkIn()
    {
        return $this->redirect(route('employee.shifts.check-in'), navigate: true);
    }

    public function checkOut()
    {
        return $this->redirect(route('employee.shifts.check-in'), navigate: true);
    }

    public function render()
    {
        return view('livewire.employee.dashboard');
    }
}
