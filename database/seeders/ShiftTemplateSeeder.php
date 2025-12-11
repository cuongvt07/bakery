<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Agency;
use App\Models\ShiftTemplate;
use Illuminate\Support\Facades\DB;

class ShiftTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            // Get all agencies
            $agencies = Agency::all();
            
            if ($agencies->isEmpty()) {
                $this->command->warn('No agencies found! Please seed agencies first.');
                return;
            }
            
            foreach ($agencies as $agency) {
                // Determine shift type based on name
                $isXuong = stripos($agency->ten_diem_ban, 'xưởng') !== false 
                        || stripos($agency->ten_diem_ban, 'xuong') !== false;
                
                if ($isXuong) {
                    // Xưởng: 3 shifts (8-12, 14-18, 18-22)
                    ShiftTemplate::create([
                        'diem_ban_id' => $agency->id,
                        'name' => 'Ca 1',
                        'start_time' => '08:00:00',
                        'end_time' => '12:00:00',
                        'is_default' => true,
                        'is_active' => true,
                    ]);
                    
                    ShiftTemplate::create([
                        'diem_ban_id' => $agency->id,
                        'name' => 'Ca 2',
                        'start_time' => '14:00:00',
                        'end_time' => '18:00:00',
                        'is_default' => true,
                        'is_active' => true,
                    ]);
                    
                    ShiftTemplate::create([
                        'diem_ban_id' => $agency->id,
                        'name' => 'Ca 3',
                        'start_time' => '18:00:00',
                        'end_time' => '22:00:00',
                        'is_default' => true,
                        'is_active' => true,
                    ]);
                } else {
                    // Điểm bán: 2 shifts (10-14, 14-18)
                    ShiftTemplate::create([
                        'diem_ban_id' => $agency->id,
                        'name' => 'Ca sáng',
                        'start_time' => '10:00:00',
                        'end_time' => '14:00:00',
                        'is_default' => true,
                        'is_active' => true,
                    ]);
                    
                    ShiftTemplate::create([
                        'diem_ban_id' => $agency->id,
                        'name' => 'Ca chiều',
                        'start_time' => '14:00:00',
                        'end_time' => '18:00:00',
                        'is_default' => true,
                        'is_active' => true,
                    ]);
                }
                
                $this->command->info("Created templates for: {$agency->ten_diem_ban}");
            }
        });
    }
}
