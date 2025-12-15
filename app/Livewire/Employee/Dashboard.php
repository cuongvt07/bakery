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

    public function mount()
    {
        $user = Auth::user();
        
        // Get employee's assigned agency (first agency for now)
        // In future, this should be from employee assignment table
        $this->agency = Agency::first(); // TODO: Get actual assigned agency
        
        // Get today's shift
        $this->todayShift = ShiftSchedule::where('nguoi_dung_id', $user->id)
            ->whereDate('ngay_lam', Carbon::today())
            ->with('agency')
            ->first();
        
        // Get today's attendance (check-in/out)
        $this->todayAttendance = ChamCong::where('nguoi_dung_id', $user->id)
            ->whereDate('ngay_cham', Carbon::today())
            ->first();
        
        // Calculate monthly stats
        $this->calculateMonthlyStats();
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
        return redirect()->route('admin.shift.check-in');
    }

    public function checkOut()
    {
        return redirect()->route('admin.shift.check-in');
    }

    public function render()
    {
        return view('livewire.employee.dashboard');
    }
}
