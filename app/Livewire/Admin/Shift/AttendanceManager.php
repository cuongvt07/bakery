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
    public $editingScheduleId;
    public $editingDate;
    public $editingNote;

    // Bulk Sync Progress
    public $syncProgress = 0;
    public $syncTotal = 0;
    public $isSyncing = false;

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
                ->where(function ($q) {
                    $q->whereNotNull('thoi_gian_checkin')
                        ->orWhere('trang_thai', 'da_ket_thuc');
                })
                ->get();

            $totalAttended = $actualShifts->count();

            // 4. Calculate Total Hours - Use stored value from tong_gio_lam_viec
            $totalHours = 0;
            foreach ($actualShifts as $shift) {
                // Use stored total hours from database (calculated during sync)
                // Falls back to calculateWorkHours if not yet synced
                $totalHours += $shift->tong_gio_lam_viec ?? $this->calculateWorkHours($shift);
            }

            // 5. Calculate Salary
            // Hệ số lương = Lương tháng (không chia gì)
            // Lương thực tế = Hệ số × Tổng giờ
            $monthlySalary = $user->getSalaryForCalculation();
            $hourlyRate = $monthlySalary; // Hệ số lương = lương tháng
            $totalSalary = round($monthlySalary * $totalHours, 0); // Total = hệ số × hours

            $summary[] = [
                'user' => $user,
                'registered_count' => $registeredShifts,
                'attended_count' => $totalAttended,
                'total_hours' => round($totalHours, 2),
                'hourly_rate' => $hourlyRate,
                'total_salary' => $totalSalary,
            ];
        }

        return collect($summary);
    }

    public function showDetail($userId)
    {
        $this->selectedUserId = $userId;
        $this->selectedUser = User::find($userId);

        if (!$this->selectedUser)
            return;

        $startOfMonth = Carbon::createFromFormat('Y-m', $this->month)->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();
        $daysInMonth = $endOfMonth->day;

        $this->detailData = [];

        // Pre-fetch data for efficiency
        $schedules = ShiftSchedule::where('nguoi_dung_id', $userId)
            ->whereBetween('ngay_lam', [$startOfMonth, $endOfMonth])
            ->get()
            ->keyBy(function ($item) {
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
                $work = $dayWorks->first(function ($w) use ($sch) {
                    return $w->shift_template_id == $sch->shift_template_id;
                });

                // Remove from dayWorks if found to avoid duplication in "extra" loop
                if ($work) {
                    $dayWorks = $dayWorks->reject(function ($w) use ($work) {
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
                    } elseif ($work->trang_thai == 'da_ket_thuc') {
                        $checkOut = substr($work->gio_ket_thuc, 0, 5) . ' (Est)';
                    }

                    // Use stored total hours from database (calculated during sync)
                    // Falls back to calculateWorkHours if not yet synced
                    $hours = $work->tong_gio_lam_viec ?? $this->calculateWorkHours($work);
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
                    'id' => $work->id ?? null,
                    'schedule_id' => $sch->id,
                    'name' => $shiftName,
                    'is_extra' => false,
                    'schedule_time' => $sch->gio_bat_dau->format('H:i') . ' - ' . $sch->gio_ket_thuc->format('H:i'),
                    'actual_in' => $checkIn,
                    'actual_out' => $checkOut,
                    'hours' => max(0, round($hours, 2)),
                    'status' => $status,
                    'is_ot' => $work->phieuChotCa->ot ?? false,
                    'debug_hours' => $hours
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
                    // Use stored total hours from database (calculated during sync)
                    // Falls back to calculation if not yet synced
                    $hours = $work->tong_gio_lam_viec ?? 0;

                    if (!$hours) {
                        // Fallback: Calculate if not stored yet
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
                }

                $templateName = $work->shiftTemplate->name ?? 'Ca bổ sung';
                $agencyName = $work->diemBan->ten_diem_ban ?? 'Điểm chưa rõ';
                $shiftName = "{$templateName} - {$agencyName}";

                $shifts[] = [
                    'id' => $work->id,
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
                    'debug_end' => isset($end) && $end ? $end->format('H:i') : 'N/A',
                    'debug_hours' => $hours
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

    public function editShift($shiftId = null, $scheduleId = null, $date = null)
    {
        $this->resetValidation();
        $this->editingShiftId = $shiftId;
        $this->editingScheduleId = $scheduleId;
        $this->editingDate = $date;

        if ($shiftId) {
            $work = CaLamViec::with('phieuChotCa')->find($shiftId);
            if (!$work)
                return;

            $this->editingCheckIn = $work->thoi_gian_checkin ? $work->thoi_gian_checkin->format('H:i') : '';

            if ($work->phieuChotCa) {
                $this->editingCheckOut = Carbon::parse($work->phieuChotCa->gio_chot)->format('H:i');
                $this->editingIsOt = (bool) ($work->phieuChotCa->ot ?? false);
            } else {
                $this->editingCheckOut = '';
                $this->editingIsOt = false;
            }
            $this->editingNote = $work->ghi_chu;
        } else {
            // New Shift
            $this->editingCheckIn = '';
            $this->editingCheckOut = '';
            $this->editingIsOt = false;
            $this->editingNote = '';
        }

        $this->showEditModal = true;
    }

    public function saveShift()
    {
        $this->validate([
            'editingCheckIn' => 'required',
            'editingCheckOut' => 'required',
        ]);

        if ($this->editingShiftId) {
            $work = CaLamViec::find($this->editingShiftId);
        } elseif ($this->editingScheduleId) {
            $sch = ShiftSchedule::find($this->editingScheduleId);
            if (!$sch)
                return; // Should not happen

            $work = new CaLamViec();
            $work->nguoi_dung_id = $this->selectedUserId; // from detail view
            $work->ngay_lam = $this->editingDate;
            $work->diem_ban_id = $sch->diem_ban_id;
            $work->shift_template_id = $sch->shift_template_id;
            $work->gio_bat_dau = $sch->gio_bat_dau;
            $work->gio_ket_thuc = $sch->gio_ket_thuc;
            $work->trang_thai = 'da_ket_thuc';
            $work->save();
        } else {
            return;
        }

        // Update Check-in
        if ($this->editingCheckIn) {
            $checkInDateTime = Carbon::parse($work->ngay_lam->format('Y-m-d') . ' ' . $this->editingCheckIn);
            $work->thoi_gian_checkin = $checkInDateTime;
            $work->trang_thai_checkin = 1; // Sync status flag with time
        }
        $work->ghi_chu = $this->editingNote;
        $work->save();

        // Update Check-out (PhieuChotCa)
        if ($this->editingCheckOut) {
            if (!$work->phieuChotCa) {
                // Create new Phieu
                $phieu = new \App\Models\PhieuChotCa(); // Ensure class is imported or fully qualified
                $phieu->ca_lam_viec_id = $work->id;
                $phieu->ngay_chot = $work->ngay_lam; // Default same day? Or logic for overnight?
                // For overnight, if CheckOut < CheckIn (Time), maybe addDay?
                // Simple logic for now: same date unless specified (but UI only asks time).
                // Let's assume same date for simplicity or let Accessor handle? 
                // Actually PhieuChotCa stores DATE and TIME separately.
                // If CheckOut time (e.g. 07:00) < CheckIn time (23:00), usually Next Day.
                // Let's rely on manual correction if needed, or simple check:
                $ci = Carbon::parse($this->editingCheckIn);
                $co = Carbon::parse($this->editingCheckOut);

                // Calculate actual hours worked
                $actualHours = $ci->diffInMinutes($co) / 60;

                // Get max hours from shift template
                $maxHours = 8;
                if ($work->gio_bat_dau && $work->gio_ket_thuc) {
                    $templateStart = Carbon::parse($work->gio_bat_dau);
                    $templateEnd = Carbon::parse($work->gio_ket_thuc);
                    $maxHours = $templateStart->diffInHours($templateEnd);
                    if ($maxHours == 0)
                        $maxHours = 8;
                }

                // CAP hours if NOT OT: Only allow up to max shift hours
                if (!$this->editingIsOt && $actualHours > $maxHours) {
                    $co = $ci->copy()->addHours($maxHours);
                    $this->dispatch('alert', ['type' => 'warning', 'message' => "Giờ làm vượt quá lịch ca ({$maxHours}h). Tự động cap tối đa {$maxHours}h (chưa tick OT)"]);
                }

                if ($co->lt($ci)) {
                    $phieu->ngay_chot = $work->ngay_lam->copy()->addDay();
                } else {
                    $phieu->ngay_chot = $work->ngay_lam;
                }

                $phieu->gio_chot = $co->toTimeString();
                $phieu->diem_ban_id = $work->diem_ban_id;
                $phieu->nguoi_chot_id = $work->nguoi_dung_id;
                $phieu->tien_mat = 0;
                $phieu->tien_chuyen_khoan = 0;
                $phieu->tong_tien_thuc_te = 0;
                $phieu->tong_tien_ly_thuyet = 0;
                $phieu->tien_lech = 0;
                $phieu->hang_lech = [];
                $phieu->ot = $this->editingIsOt;
                $phieu->save();
            } else {
                // Logic for date rollover if user changed time
                $ci = Carbon::parse($this->editingCheckIn);
                $co = Carbon::parse($this->editingCheckOut);

                // Calculate actual hours worked
                // If co < ci, assume it's next day for calculation purposes
                if ($co->lt($ci)) {
                    $calcCo = $co->copy()->addDay();
                    $actualHours = $ci->diffInMinutes($calcCo) / 60;
                } else {
                    $actualHours = $ci->diffInMinutes($co) / 60;
                }

                // Get max hours from shift template
                $maxHours = 8;
                if ($work->gio_bat_dau && $work->gio_ket_thuc) {
                    $templateStart = Carbon::parse($work->gio_bat_dau);
                    $templateEnd = Carbon::parse($work->gio_ket_thuc);
                    $maxHours = $templateStart->diffInHours($templateEnd);
                    if ($maxHours == 0)
                        $maxHours = 8;
                }

                // CAP hours if NOT OT: Only allow up to max shift hours
                if (!$this->editingIsOt && $actualHours > $maxHours) {
                    // Removed auto-cap to respect user input
                }

                // SIMPLIFIED: Always set checkout date as shift date (No overnight logic)
                $work->phieuChotCa->ngay_chot = $work->ngay_lam;

                $work->phieuChotCa->gio_chot = $co->format('H:i:s');
                $work->phieuChotCa->ot = $this->editingIsOt;
                $work->phieuChotCa->save();
            }

            // Recalculate and save total hours immediately
            // Refresh to ensure we have the latest phieuChotCa relations and data
            $work->refresh();
            $work->calculateAndSaveTotalHours()->save();
        }

        $this->showEditModal = false;
        $this->showDetail($this->selectedUserId); // Refresh
    }

    public function syncAttendance()
    {
        $this->isSyncing = true;
        $this->syncProgress = 0;

        // Get unique users with shifts that need syncing
        $users = User::whereIn('vai_tro', ['nhan_vien', 'quan_ly'])->get();
        $this->syncTotal = $users->count();

        $processedCount = 0;

        foreach ($users as $user) {
            // Get all shifts with check-in for this user
            $shifts = CaLamViec::where('nguoi_dung_id', $user->id)
                ->whereNotNull('thoi_gian_checkin')
                ->get();

            foreach ($shifts as $shift) {
                $shift->calculateAndSaveTotalHours()->save();
            }

            $processedCount++;
            $this->syncProgress = $processedCount;

            // Dispatch event to update UI progress
            $this->dispatch('sync-progress', [
                'current' => $processedCount,
                'total' => $this->syncTotal,
                'percent' => round(($processedCount / $this->syncTotal) * 100)
            ]);
        }

        $this->isSyncing = false;

        // Reload detail if user is selected
        if ($this->selectedUserId) {
            $this->showDetail($this->selectedUserId);
        }

        $this->dispatch('alert', ['type' => 'success', 'message' => "Đã đồng bộ công thành công cho {$this->syncTotal} nhân viên!"]);
    }

    private function calculateWorkHours($work)
    {
        // 1. Get Max Hours from Work (snapshot of schedule)
        $maxHours = 8;
        try {
            if ($work->gio_bat_dau && $work->gio_ket_thuc) {
                $s = Carbon::parse($work->gio_bat_dau);
                $e = Carbon::parse($work->gio_ket_thuc);
                $maxHours = $s->diffInHours($e);
                if ($maxHours == 0)
                    $maxHours = 8;
            }
        } catch (\Exception $e) {
        }

        // 2. Strict Check: Must have Check-in
        if (!$work->thoi_gian_checkin)
            return 0;

        $start = $work->thoi_gian_checkin;
        $end = null;
        $isOt = false;

        if ($work->phieuChotCa) {
            // Strict Check: Must have Check-out Time in Phieu
            if (!$work->phieuChotCa->gio_chot)
                return 0;

            $ngayChot = $work->phieuChotCa->ngay_chot ?? $work->ngay_lam;
            $gioChot = $work->phieuChotCa->gio_chot;
            $gioChotStr = $gioChot instanceof \Carbon\Carbon ? $gioChot->format('H:i:s') : $gioChot;

            try {
                $dateStr = \Carbon\Carbon::parse($ngayChot)->format('Y-m-d');
                $end = Carbon::parse($dateStr . ' ' . $gioChotStr);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("CalcError: " . $e->getMessage());
                return 0;
            }

            $isOt = (bool) ($work->phieuChotCa->ot ?? false);

        } elseif ($work->trang_thai == 'da_ket_thuc') {
            // Fallback for auto-completed shifts
            $end = $work->shift_end_date_time;
        } else {
            return 0;
        }

        if (!$end)
            return 0;

        // Overnight adjustment
        if ($end->lt($start)) {
            $end->addDay();
        }

        $diff = abs($end->floatDiffInHours($start));

        $hours = $isOt ? $diff : min($diff, $maxHours);
        return round(max(0, $hours), 2);
    }
}
