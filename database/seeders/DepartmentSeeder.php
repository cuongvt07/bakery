<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('phong_ban')->insert([
            [
                'ma_phong_ban' => 'PB0001',
                'ten_phong_ban' => 'Phòng Bán Hàng',
                'ma_mau' => '#10B981', // Green
                'trang_thai' => 'hoat_dong',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma_phong_ban' => 'PB0002',
                'ten_phong_ban' => 'Phòng Sản Xuất',
                'ma_mau' => '#F59E0B', // Orange
                'trang_thai' => 'hoat_dong',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
