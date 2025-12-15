<?php

namespace App\Livewire\Employee\Shift;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\ShiftSchedule as ShiftScheduleModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.mobile')]
class ShiftSchedule extends Component
{
    public $currentMonth;
    public $viewMode = 'calendar'; // calendar | list
    public $filterStatus = '';
    public $shifts = [];
    public $monthlyStats = [];
    public $calendarDays = [];

    public function mount()
    {
        $this->currentMonth = Carbon::now();
        $this->loadShifts();
    }

    public function loadShifts()
    {
        $startOfMonth = $this->currentMonth->copy()->startOfMonth();
        $endOfMonth = $this->currentMonth->copy()->endOfMonth();
        
        // 1. Fetch Schedules (No more Pending Requests needed since we auto-approve)
        $query = ShiftScheduleModel::where('nguoi_dung_id', Auth::id())
            ->whereBetween('ngay_lam', [$startOfMonth, $endOfMonth])
            ->with('agency');
        
        if ($this->filterStatus) {
            $query->where('trang_thai', $this->filterStatus);
        }
        
        // Sort by Date + Time
        $this->shifts = $query->orderBy('ngay_lam')->orderBy('gio_bat_dau')->get();
        
        // Calculate stats using DB ENUMs
        $this->monthlyStats = [
            'total' => $this->shifts->count(),
            'completed' => $this->shifts->where('trang_thai', 'completed')->count(), // was da_ket_thuc
            'upcoming' => $this->shifts->where('trang_thai', 'approved')->count(),   // was chua_bat_dau, now approved
            // 'pending' => 0, // No more pending requests in schedule
        ];
        
        // Build calendar grid
        $this->buildCalendar();
    }

    private function buildCalendar()
    {
        $startOfMonth = $this->currentMonth->copy()->startOfMonth();
        $endOfMonth = $this->currentMonth->copy()->endOfMonth();
        
        $startDate = $startOfMonth->copy()->startOfWeek(Carbon::SUNDAY);
        $endDate = $endOfMonth->copy()->endOfWeek(Carbon::SATURDAY);
        
        $this->calendarDays = [];
        $current = $startDate->copy();
        
        while ($current <= $endDate) {
            $dayShifts = $this->shifts->filter(function($shift) use ($current) {
                return Carbon::parse($shift->ngay_lam)->isSameDay($current);
            });
            
            $this->calendarDays[] = [
                'date' => $current->copy(),
                'isCurrentMonth' => $current->month === $this->currentMonth->month,
                'isToday' => $current->isToday(),
                'shifts' => $dayShifts,
            ];
            
            $current->addDay();
        }
    }

    public function previousMonth()
    {
        $this->currentMonth->subMonth();
        $this->loadShifts();
    }

    public function nextMonth()
    {
        $this->currentMonth->addMonth();
        $this->loadShifts();
    }

    public function toggleViewMode()
    {
        $this->viewMode = $this->viewMode === 'calendar' ? 'list' : 'calendar';
    }

    public function setFilter($status)
    {
        $this->filterStatus = $status;
        $this->loadShifts();
    }

    // Keep these for requesting changes to approved shifts
    public function requestChange($shiftId)
    {
        return redirect()->route('employee.shifts.requests', ['action' => 'change', 'shiftId' => $shiftId]);
    }

    public function requestOff($shiftId)
    {
        return redirect()->route('employee.shifts.requests', ['action' => 'off', 'shiftId' => $shiftId]);
    }

    public function render()
    {
        return view('livewire.employee.shift.shift-schedule');
    }
}
