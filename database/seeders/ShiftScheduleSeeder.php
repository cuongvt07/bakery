<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShiftScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating Shift Schedules...');
        
        // Lấy các IDs cần thiết
        $xuongId = DB::table('diem_ban')->where('ma_diem_ban', 'XUONG01')->value('id');
        $diem1Id = DB::table('diem_ban')->where('ma_diem_ban', 'DB001')->value('id');
        $emp1Id = DB::table('nguoi_dung')->where('email', 'nva@bakery.com')->value('id');
        $emp2Id = DB::table('nguoi_dung')->where('email', 'ttb@bakery.com')->value('id');
        
        // Lấy IDs của shift templates
        $xuongCa1Id = DB::table('shift_templates')->where('diem_ban_id', $xuongId)->where('name', 'Ca 1')->value('id');
        $xuongCa2Id = DB::table('shift_templates')->where('diem_ban_id', $xuongId)->where('name', 'Ca 2')->value('id');
        $diemCaSangId = DB::table('shift_templates')->where('diem_ban_id', $diem1Id)->where('name', 'Ca sáng')->value('id');
        $diemCaChieuId = DB::table('shift_templates')->where('diem_ban_id', $diem1Id)->where('name', 'Ca chiều')->value('id');
        
        // Validate IDs
        if (!$xuongId || !$diem1Id || !$emp1Id || !$emp2Id) {
            $this->command->error('❌ Required IDs not found! Please run TestDataSeeder first.');
            return;
        }
        
        if (!$xuongCa1Id || !$xuongCa2Id || !$diemCaSangId || !$diemCaChieuId) {
            $this->command->error('❌ Shift template IDs not found!');
            return;
        }
        
        $this->command->info("✓ Agency IDs - Xưởng: $xuongId, Điểm: $diem1Id");
        $this->command->info("✓ Employee IDs - Emp1: $emp1Id, Emp2: $emp2Id");
        $this->command->info("✓ Template IDs - Xưởng: $xuongCa1Id, $xuongCa2Id | Điểm: $diemCaSangId, $diemCaChieuId");
        
        // Tạo lịch ca cho tuần này và tuần tới
        $shifts = [];
        $today = now()->startOfDay();
        
        // Tuần này (7 ngày từ hôm nay)
        for ($i = 0; $i < 7; $i++) {
            $date = $today->copy()->addDays($i);
            
            // Ca làm việc ở Xưởng - Nhân viên A làm Ca 1
            $shifts[] = [
                'diem_ban_id' => $xuongId,
                'nguoi_dung_id' => $emp1Id,
                'shift_template_id' => $xuongCa1Id,
                'ngay_lam' => $date->format('Y-m-d'),
                'gio_bat_dau' => '08:00:00',
                'gio_ket_thuc' => '12:00:00',
                'trang_thai' => $i < 1 ? 'da_ket_thuc' : ($i == 1 ? 'dang_lam' : 'chua_bat_dau'),
                'ghi_chu' => $i == 0 ? 'Hoàn thành tốt' : null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            
            // Ca làm việc ở Xưởng - Nhân viên B làm Ca 2
            $shifts[] = [
                'diem_ban_id' => $xuongId,
                'nguoi_dung_id' => $emp2Id,
                'shift_template_id' => $xuongCa2Id,
                'ngay_lam' => $date->format('Y-m-d'),
                'gio_bat_dau' => '14:00:00',
                'gio_ket_thuc' => '18:00:00',
                'trang_thai' => $i < 1 ? 'da_ket_thuc' : 'chua_bat_dau',
                'created_at' => now(),
                'updated_at' => now(),
            ];
            
            // Ca làm việc ở Điểm bán - Nhân viên A làm Ca sáng (ngày chẵn)
            if ($i % 2 == 0) {
                $shifts[] = [
                    'diem_ban_id' => $diem1Id,
                    'nguoi_dung_id' => $emp1Id,
                    'shift_template_id' => $diemCaSangId,
                    'ngay_lam' => $date->format('Y-m-d'),
                    'gio_bat_dau' => '10:00:00',
                    'gio_ket_thuc' => '14:00:00',
                    'trang_thai' => $i < 1 ? 'da_ket_thuc' : 'chua_bat_dau',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            
            // Ca làm việc ở Điểm bán - Nhân viên B làm Ca chiều (ngày lẻ)
            if ($i % 2 == 1) {
                $shifts[] = [
                    'diem_ban_id' => $diem1Id,
                    'nguoi_dung_id' => $emp2Id,
                    'shift_template_id' => $diemCaChieuId,
                    'ngay_lam' => $date->format('Y-m-d'),
                    'gio_bat_dau' => '14:00:00',
                    'gio_ket_thuc' => '18:00:00',
                    'trang_thai' => $i < 1 ? 'da_ket_thuc' : 'chua_bat_dau',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        
        // Tuần tới (7 ngày tiếp theo) - Luân phiên nhân viên
        for ($i = 7; $i < 14; $i++) {
            $date = $today->copy()->addDays($i);
            
            $shifts[] = [
                'diem_ban_id' => $xuongId,
                'nguoi_dung_id' => $emp2Id,
                'shift_template_id' => $xuongCa1Id,
                'ngay_lam' => $date->format('Y-m-d'),
                'gio_bat_dau' => '08:00:00',
                'gio_ket_thuc' => '12:00:00',
                'trang_thai' => 'chua_bat_dau',
                'created_at' => now(),
                'updated_at' => now(),
            ];
            
            $shifts[] = [
                'diem_ban_id' => $xuongId,
                'nguoi_dung_id' => $emp1Id,
                'shift_template_id' => $xuongCa2Id,
                'ngay_lam' => $date->format('Y-m-d'),
                'gio_bat_dau' => '14:00:00',
                'gio_ket_thuc' => '18:00:00',
                'trang_thai' => 'chua_bat_dau',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        
        // Insert từng ca một để dễ debug
        foreach ($shifts as $shift) {
            DB::table('ca_lam_viec')->insert($shift);
        }
        
        $this->command->info('✅ Created ' . count($shifts) . ' shift schedules');
    }
}
