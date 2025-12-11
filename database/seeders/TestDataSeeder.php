<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Agency;
use App\Models\ShiftTemplate;
use App\Models\ShiftSchedule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        try {
            DB::transaction(function () {
                // 1. NGƯỜI DÙNG
                $this->command->info('Creating Users...');
                
                // Admin
                $adminExists = DB::table('nguoi_dung')->where('email', 'admin@bakery.com')->exists();
                if (!$adminExists) {
                    DB::table('nguoi_dung')->insert([
                        'ho_ten' => 'Admin System',
                        'email' => 'admin@bakery.com',
                        'mat_khau' => Hash::make('password'),
                        'vai_tro' => 'admin',
                        'so_dien_thoai' => '0900000001',
                        'trang_thai' => 'hoat_dong',
                        'created_at' => now(), 'updated_at' => now(),
                    ]);
                }

                // Employee 1
                $emp1Exists = DB::table('nguoi_dung')->where('email', 'nva@bakery.com')->exists();
                if (!$emp1Exists) {
                    DB::table('nguoi_dung')->insert([
                        'ho_ten' => 'Nguyễn Văn A',
                        'email' => 'nva@bakery.com',
                        'mat_khau' => Hash::make('password'),
                        'vai_tro' => 'nhan_vien',
                        'so_dien_thoai' => '0900000002',
                        'trang_thai' => 'hoat_dong',
                        'created_at' => now(), 'updated_at' => now(),
                    ]);
                }

                // Employee 2
                $emp2Exists = DB::table('nguoi_dung')->where('email', 'ttb@bakery.com')->exists();
                if (!$emp2Exists) {
                    DB::table('nguoi_dung')->insert([
                        'ho_ten' => 'Trần Thị B',
                        'email' => 'ttb@bakery.com',
                        'mat_khau' => Hash::make('password'),
                        'vai_tro' => 'nhan_vien',
                        'so_dien_thoai' => '0900000003',
                        'trang_thai' => 'hoat_dong',
                        'created_at' => now(), 'updated_at' => now(),
                    ]);
                }

                // 2. ĐIỂM BÁN
                $this->command->info('Creating Agencies...');
                
                // Xưởng
                if (!DB::table('diem_ban')->where('ma_diem_ban', 'XUONG01')->exists()) {
                    DB::table('diem_ban')->insert([
                        'ma_diem_ban' => 'XUONG01',
                        'ten_diem_ban' => 'Xưởng Sản Xuất',
                        'dia_chi' => '123 Đường ABC',
                        'so_dien_thoai' => '0281234567',
                        'loai_dai_ly' => 'rieng_tu',
                        'created_at' => now(), 'updated_at' => now(),
                    ]);
                }

                // Điểm bán 1
                if (!DB::table('diem_ban')->where('ma_diem_ban', 'DB001')->exists()) {
                    DB::table('diem_ban')->insert([
                        'ma_diem_ban' => 'DB001',
                        'ten_diem_ban' => '336 Nguyễn Trãi',
                        'dia_chi' => '336 Nguyễn Trãi',
                        'so_dien_thoai' => '0282345678',
                        'loai_dai_ly' => 'via_he',
                        'created_at' => now(), 'updated_at' => now(),
                    ]);
                }

                $this->command->info('✅ Setup Test Data Complete!');
                $this->command->info('');
                $this->command->info('📧 Admin: admin@bakery.com / password');
                $this->command->info('📧 Nhân viên 1: nva@bakery.com / password');
                $this->command->info('📧 Nhân viên 2: ttb@bakery.com / password');
                $this->command->info('');
                $this->command->info('💡 Bạn có thể tự thêm Shift Templates và Ca Làm Việc qua giao diện web');
            });
        } catch (\Exception $e) {
            $this->command->error("Error seeding data: " . $e->getMessage());
            throw $e;
        }
    }
}
