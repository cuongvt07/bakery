<?php

namespace App\Livewire\Admin\Shift;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\User;
use App\Models\ShiftSchedule;
use App\Models\CaLamViec;
use Carbon\Carbon;
use Illuminate\Support\Collection;

#[Layout('components.layouts.admin')]
class AttendanceManager extends Component
{
    public $month; // Format: Y-m
    public $year;
    
    // Detailed view
    public $selectedUserId = null;
    public $showDetailModal = false;
    public $detailData = [];
    public $selectedUser = null;

    // Edit Modal
    public $showEditModal = false;
    public $editingShiftId = null;
    public $editingCheckIn;
    public $editingCheckOut;
    public $editingIsOt = false;

    public function mount()
    {
        $this->month = now()->format('Y-m');
    }

    public function getAttendanceSummaryProperty()
    {
        $startOfMonth = Carbon::createFromFormat('Y-m', $this->month)->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        // 1. Get all employees (or active ones)
        // User indicates 'vai_tro' is the column to use.
        // Assuming we want 'nhan_vien' and 'quan_ly' (manager might also have shifts?).
        // Let's broaden to include both or just list all as per previous logic attempt but correct column.
        $users = User::whereIn('vai_tro', ['nhan_vien', 'quan_ly'])->get();

        $summary = [];

        foreach ($users as $user) {
            // 2. Count Registered Shifts (ShiftSchedule)
            $registeredShifts = ShiftSchedule::where('nguoi_dung_id', $user->id)
                ->whereBetween('ngay_lam', [$startOfMonth, $endOfMonth])
                ->whereIn('trang_thai', ['approved', 'completed']) // Assuming these are valid statuses
                ->count();

            // 3. Get Actual Shifts (CaLamViec)
            $actualShifts = CaLamViec::where('nguoi_dung_id', $user->id)
                ->whereBetween('ngay_lam', [$startOfMonth, $endOfMonth])
                ->where(function($q) {
                    $q->whereNotNull('thoi_gian_checkin')
                      ->orWhere('trang_thai', 'da_ket_thuc');
                })
                ->get();

            $totalAttended = $actualShifts->count();
            
            // 4. Calculate Total Hours
            $totalHours = 0;
            foreach ($actualShifts as $shift) {
                if ($shift->gio_bat_dau && $shift->gio_ket_thuc) {
                    $start = Carbon::parse($shift->ngay_lam->format('Y-m-d') . ' ' . $shift->gio_bat_dau);
                    
                    // If checkin/checkout exists and is valid, user might want to use that.
                    // But usually for payroll, we compare scheduled vs actual.
                    // The requirement says: "chỗ này sẽ đếm các ca đã checkin - chốt ca thành công"
                    // "tổng thời gian làm việc (chốt ca - check in max chỉ được 8h / 1 ca)"
                    
                    // Let's look for Checkin/Checkout times. 
                    // CaLamViec has 'thoi_gian_checkin'. Checkout is in 'phieuChotCa' or implied by status.
                    // However, `CaLamViec` also has `gio_bat_dau` and `gio_ket_thuc` which might be the *assigned* time?
                    // Let's check if there is real checkout time.
                    // The `CheckIn` component creates `PhieuChotCa` with `gio_chot`. 
                    
                    // Let's prioritize actual PhieuChotCa times if available, otherwise fallback or 0.
                    
                    $checkInTime = $shift->thoi_gian_checkin;
                    $checkOutTime = null;
                    
                    if ($shift->phieuChotCa) {
                        // Combine date and time
                        $checkOutDate = $shift->phieuChotCa->ngay_chot;
                        $checkOutTimeStr = $shift->phieuChotCa->gio_chot;
                        if ($checkOutDate && $checkOutTimeStr) {
                            $checkOutTime = Carbon::parse($checkOutDate->format('Y-m-d') . ' ' . Carbon::parse($checkOutTimeStr)->format('H:i:s'));
                        }
                    } elseif ($shift->trang_thai == 'da_ket_thuc') {
                        // Fallback to shift end time if completed but no phieu (rare?)
                         $checkOutTime = Carbon::make($shift->ngay_lam->format('Y-m-d').' '.$shift->gio_ket_thuc);
                    }

                    if ($checkInTime && $checkOutTime) {
                        $diffInHours = $checkOutTime->diffInMinutes($checkInTime) / 60;
                        // Max 8 hours
                        $hours = min($diffInHours, 8);
                        $totalHours += max(0, $hours); // Ensure no negative
                    }
                }
            }

            $summary[] = [
                'user' => $user,
                'registered_count' => $registeredShifts,
                'attended_count' => $totalAttended,
                'total_hours' => round($totalHours, 2),
            ];
        }

        return collect($summary);
    }

    public function showDetail($userId)
    {
        $this->selectedUserId = $userId;
        $this->selectedUser = User::find($userId);
        
        if (!$this->selectedUser) return;

        $startOfMonth = Carbon::createFromFormat('Y-m', $this->month)->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();
        $daysInMonth = $endOfMonth->day;

        $this->detailData = [];

        // Pre-fetch data for efficiency
        $schedules = ShiftSchedule::where('nguoi_dung_id', $userId)
            ->whereBetween('ngay_lam', [$startOfMonth, $endOfMonth])
            ->get()
            ->keyBy(function($item) {
                return $item->ngay_lam->format('Y-m-d') . '-' . $item->id; // Composite key in case multiple shifts/day
            });
            
        // Because a user can have multiple shifts per day, simply iterating days 1..31 isn't enough if we want to show all shifts.
        // However, the requirement says "bấm chi tiết thì sẽ hiển thị popup chi tiết dạng table list thời gian từ đầu tháng tới cuối tháng"
        // and "ngày nào có đăng ký ca thì note lại".
        
        // Let's iterate all days, and for each day check if there are shifts.
        // If multiple shifts satisfy the day, we might need multiple rows or combined info.
        // Let's create a row for every Shift Schedule, AND every 'CaLamViec' that might not have a schedule (ad-hoc?).
        // Actually, usually shifts are based on schedules.
        
        // Let's iterate through each day of the month.
        
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $currentDate = $startOfMonth->copy()->addDays($day - 1);
            $dateString = $currentDate->format('Y-m-d');
            
            // Find all schedules for this day
            $daySchedules = ShiftSchedule::with(['shiftTemplate', 'agency'])
                ->where('nguoi_dung_id', $userId)
                ->whereDate('ngay_lam', $currentDate)
                ->get();
                
            // Find all actual work sessions (CaLamViec) for this day
            $dayWorks = CaLamViec::with(['phieuChotCa', 'shiftTemplate', 'diemBan'])
                ->where('nguoi_dung_id', $userId)
                ->whereDate('ngay_lam', $currentDate)
                ->get();
            
            $shifts = [];
            $dailyTotalHours = 0;
            $hasActivity = false;

            // Process Schedules
            foreach ($daySchedules as $sch) {
                // Find matching work
                $work = $dayWorks->first(function($w) use ($sch) {
                    return $w->shift_template_id == $sch->shift_template_id;
                });
                
                // Remove from dayWorks if found to avoid duplication in "extra" loop
                if ($work) {
                    $dayWorks = $dayWorks->reject(function($w) use ($work) {
                        return $w->id == $work->id;
                    });
                }
                
                $checkIn = '-';
                $checkOut = '-';
                $hours = 0;
                $status = 'absent'; // Default if scheduled but no work
                
                if ($work) {
                    $status = 'working';
                    $checkIn = $work->thoi_gian_checkin ? $work->thoi_gian_checkin->format('H:i') : '-';
                    
                    if ($work->phieuChotCa) {
                          $checkOut = Carbon::parse($work->phieuChotCa->gio_chot)->format('H:i');
                          $end = Carbon::parse($work->phieuChotCa->ngay_chot->format('Y-m-d') . ' ' . Carbon::parse($work->phieuChotCa->gio_chot)->format('H:i:s'));
                         $start = $work->thoi_gian_checkin;
                         if ($start) {
                              $diff = $end->floatDiffInHours($start); 
                              
                              // Calculate max hours from schedule
                              $schStart = Carbon::parse($sch->gio_bat_dau);
                              $schEnd = Carbon::parse($sch->gio_ket_thuc);
                              $maxHours = $schStart->diffInHours($schEnd); // Usually 4 or 8
                              if ($maxHours == 0) $maxHours = 8; // Fallback
                              
                              $isOt = $work->phieuChotCa->ot ?? false;
                              $hours = $isOt ? $diff : min($diff, $maxHours);
                          }
                    } elseif ($work->trang_thai == 'da_ket_thuc') {
                        $checkOut = substr($work->gio_ket_thuc, 0, 5) . ' (Est)';
                        // Fallback calculation for completed shifts without Phieu
                         $end = $work->shift_end_date_time; // Using accessor
                         $start = $work->thoi_gian_checkin;
                         
                         $schStart = Carbon::parse($sch->gio_bat_dau);
                         $schEnd = Carbon::parse($sch->gio_ket_thuc);
                         $maxHours = $schStart->diffInHours($schEnd);
                         if ($maxHours == 0) $maxHours = 8;
                         
                         if ($start) {
                             $diff = $end->floatDiffInHours($start);
                             $hours = min($diff, $maxHours); // No OT allowed for fallback
                         } else {
                             // No checkin but completed? Assume full shift
                             $hours = $maxHours;
                         }
                    }
                } else {
                    // Check if date is in past
                    if ($currentDate->lt(now())) {
                        $status = 'absent';
                    } else {
                        $status = 'future';
                    }
                }

                // Format Name: Template - Agency
                $templateName = $sch->shiftTemplate->name ?? 'Ca không tên';
                $agencyName = $sch->agency->ten_diem_ban ?? 'Điểm chưa rõ';
                $shiftName = "{$templateName} - {$agencyName}";

                $shifts[] = [
                    'name' => $shiftName,
                    'is_extra' => false,
                    'schedule_time' => $sch->gio_bat_dau->format('H:i') . ' - ' . $sch->gio_ket_thuc->format('H:i'),
                    'actual_in' => $checkIn,
                    'actual_out' => $checkOut,
                    'hours' => max(0, round($hours, 2)),
                    'status' => $status,
                    'is_ot' => $work->phieuChotCa->ot ?? false,
                    'debug_diff' => $diff ?? 'N/A',
                    'debug_max' => $maxHours ?? 'N/A',
                    'debug_start' => isset($start) && $start ? $start->format('H:i') : 'N/A',
                    'debug_end' => isset($end) && $end ? $end->format('H:i') : 'N/A'
                ];
                
                $dailyTotalHours += max(0, $hours);
                $hasActivity = true;
            }
            
            // Process Extra Works (Ad-hoc)
            foreach ($dayWorks as $work) {
                $checkIn = $work->thoi_gian_checkin ? $work->thoi_gian_checkin->format('H:i') : '-';
                $checkOut = '-';
                $hours = 0;
                
                if ($work->phieuChotCa) {
                     $checkOut = Carbon::parse($work->phieuChotCa->gio_chot)->format('H:i');
                     $end = Carbon::parse($work->phieuChotCa->ngay_chot->format('Y-m-d') . ' ' . Carbon::parse($work->phieuChotCa->gio_chot)->format('H:i:s'));
                     $start = $work->thoi_gian_checkin;
                     if ($start) {
                          $diff = $end->floatDiffInHours($start);
                          
                           // Calculate max hours from schedule
                           $templateStart = $work->shiftTemplate->start_time ?? '00:00';
                           $templateEnd = $work->shiftTemplate->end_time ?? '00:00';
                           $maxHours = 8; // Default for extra
                           if ($templateStart && $templateEnd) {
                               $s = Carbon::parse($templateStart);
                               $e = Carbon::parse($templateEnd);
                               $maxHours = $s->diffInHours($e);
                           }

                          $isOt = $work->phieuChotCa->ot ?? false;
                          $hours = $isOt ? $diff : min($diff, $maxHours);
                      }
                }

                $templateName = $work->shiftTemplate->name ?? 'Ca bổ sung';
                $agencyName = $work->diemBan->ten_diem_ban ?? 'Điểm chưa rõ';
                $shiftName = "{$templateName} - {$agencyName}";

                $shifts[] = [
                    'name' => $shiftName,
                    'is_extra' => true,
                    'schedule_time' => 'Bổ sung',
                    'actual_in' => $checkIn,
                    'actual_out' => $checkOut,
                    'hours' => max(0, round($hours, 2)),
                    'status' => 'extra',
                    'is_ot' => $work->phieuChotCa->ot ?? false,
                    'debug_diff' => $diff ?? 'N/A',
                    'debug_max' => $maxHours ?? 'N/A',
                    'debug_start' => isset($start) && $start ? $start->format('H:i') : 'N/A',
                    'debug_end' => isset($end) && $end ? $end->format('H:i') : 'N/A'
                ];
                
                $dailyTotalHours += max(0, $hours);
                $hasActivity = true;
            }

            // Only add to list if there is activity (or maybe we want to show all days? User said "từ đầu tháng xoa cuối tháng" -> show all days)
            // User said: "ngày nào có đăng ký ca thì note lại". This implies showing days with shifts.
            // But usually attendance report shows full calendar. Let's keep all days but mark empty ones clearly or just minimal info.
            
            $this->detailData[] = [
                'date' => $currentDate,
                'day_of_week' => $currentDate->dayOfWeek,
                'total_shifts' => count($shifts),
                'total_hours' => round($dailyTotalHours, 2),
                'shifts' => $shifts,
                'has_activity' => $hasActivity
            ];
        }

        $this->showDetailModal = true;
    }

    public function closeModal()
    {
        $this->showDetailModal = false;
        $this->selectedUser = null;
    }

    public function render()
    {
        return view('livewire.admin.shift.attendance-manager', [
            'summary' => $this->attendanceSummary
        ])->layoutData(['hideBreadcrumbs' => true]);
    }
    
    public function editShift($shiftId)
    {
        $work = CaLamViec::with('phieuChotCa')->find($shiftId);
        if (!$work) return;
        
        $this->editingShiftId = $work->id;
        $this->editingCheckIn = $work->thoi_gian_checkin ? $work->thoi_gian_checkin->format('H:i') : '';
        
        if ($work->phieuChotCa) {
             $this->editingCheckOut = Carbon::parse($work->phieuChotCa->gio_chot)->format('H:i');
             $this->editingIsOt = (bool)($work->phieuChotCa->ot ?? false);
        } else {
             $this->editingCheckOut = '';
             $this->editingIsOt = false;
        }
        
        $this->showEditModal = true;
    }
    
    public function saveShift()
    {
        $this->validate([
            'editingCheckIn' => 'required',
            'editingCheckOut' => 'required',
        ]);
        
        $work = CaLamViec::find($this->editingShiftId);
        if (!$work) return;
        
        // Update Check-in
        $checkInDateTime = Carbon::parse($work->ngay_lam->format('Y-m-d') . ' ' . $this->editingCheckIn);
        $work->thoi_gian_checkin = $checkInDateTime;
        $work->save();
        
        // Update Check-out (PhieuChotCa)
        if ($work->phieuChotCa) {
            $work->phieuChotCa->gio_chot = Carbon::parse($this->editingCheckOut)->toTimeString(); // Save as H:i:s string
            $work->phieuChotCa->ot = $this->editingIsOt;
            $work->phieuChotCa->save();
        } else {
            // Create Phieu if not exists (Admin force closing?)
            // For now assume only editing existing closing
        }
        
        $this->showEditModal = false;
        $this->showDetail($this->selectedUserId); // Refresh
    }
}
