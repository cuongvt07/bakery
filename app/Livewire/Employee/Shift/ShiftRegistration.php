<?php

namespace App\Livewire\Employee\Shift;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\ShiftTemplate;
use App\Models\ShiftSchedule;
use App\Models\Agency;
use App\Models\NhatKyHoatDong;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

#[Layout('components.layouts.mobile')]
class ShiftRegistration extends Component
{
    public $currentMonth;
    public $agencies = [];
    public $selectedAgencyId;
    public $selectedAgency;
    public $availableSlots = [];
    public $days = [];
    
    public $registeredShifts = [];

    public function mount()
    {
        $this->currentMonth = Carbon::now()->startOfMonth();
        $this->agencies = Agency::get();
        $this->selectedAgency = Agency::first();
        $this->selectedAgencyId = $this->selectedAgency?->id;
        
        $this->loadData();
    }

    public function updatedSelectedAgencyId($value)
    {
        $this->selectedAgency = Agency::find($value);
        $this->loadData();
    }

    public function loadData()
    {
        $this->days = [];
        $start = $this->currentMonth->copy();
        $end = $this->currentMonth->copy()->endOfMonth();
        $daysInMonth = $end->day;

        for ($i = 1; $i <= $daysInMonth; $i++) {
            $this->days[] = $start->copy()->day($i);
        }
        
        if ($this->selectedAgency) {
            $this->availableSlots = ShiftTemplate::where('diem_ban_id', $this->selectedAgency->id)
                ->where('is_active', true)
                ->orderBy('start_time')
                ->get();
        } else {
            $this->availableSlots = [];
        }

        $schedules = ShiftSchedule::where('nguoi_dung_id', Auth::id())
            ->whereBetween('ngay_lam', [$start->format('Y-m-d'), $end->format('Y-m-d')])
            ->where('diem_ban_id', $this->selectedAgencyId)
            ->get();

        $this->registeredShifts = [];
        foreach ($schedules as $sched) {
            $date = $sched->ngay_lam->format('Y-m-d');
            $start = Carbon::parse($sched->gio_bat_dau)->format('H:i');
            
            $key = $date . '_' . $start;
            $this->registeredShifts[$key] = $sched->id;
        }
    }

    public function toggleShift($date, $templateId)
    {
        $template = ShiftTemplate::find($templateId);
        if (!$template) return;

        $startTime = Carbon::parse($template->start_time)->format('H:i');
        $key = $date . '_' . $startTime;

        if (isset($this->registeredShifts[$key])) {
            $scheduleId = $this->registeredShifts[$key];
            $schedule = ShiftSchedule::find($scheduleId);
            
            if ($schedule) {
                NhatKyHoatDong::logActivity(
                    action: 'huy_lich_lam',
                    oldData: $schedule->toArray(),
                    description: "Hủy lịch làm việc {$date} ({$startTime})"
                );
                
                $schedule->delete();
            }
            
            unset($this->registeredShifts[$key]);
        } else {
            // Using 'approved' as default CONFIRMED status per table definition
            $schedule = ShiftSchedule::create([
                'nguoi_dung_id' => Auth::id(),
                'diem_ban_id' => $this->selectedAgency->id,
                'shift_template_id' => $template->id,
                'ngay_lam' => $date,
                'gio_bat_dau' => $template->start_time,
                'gio_ket_thuc' => $template->end_time,
                'trang_thai' => 'approved', // FIXED: Use valid ENUM value
                'ghi_chu' => 'Đăng ký nhanh',
            ]);

            NhatKyHoatDong::logActivity(
                action: 'dang_ky_lich',
                newData: $schedule->toArray(),
                description: "Đăng ký lịch làm việc {$date} ({$startTime})"
            );

            $this->registeredShifts[$key] = $schedule->id;
        }
    }

    public function previousMonth()
    {
        $this->currentMonth->subMonth();
        $this->loadData();
    }

    public function nextMonth()
    {
        $this->currentMonth->addMonth();
        $this->loadData();
    }

    public function render()
    {
        return view('livewire.employee.shift.shift-registration');
    }
}
