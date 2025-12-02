<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class CoreFlowSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Tạo Danh mục & Sản phẩm
        $catId = DB::table('danh_muc_san_pham')->insertGetId([
            'ten_danh_muc' => 'Bánh Mì',
            'thu_tu' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $products = [
            ['ma' => 'SP001', 'ten' => 'Bánh Mì Pate', 'gia' => 15000],
            ['ma' => 'SP002', 'ten' => 'Bánh Mì Trứng', 'gia' => 12000],
            ['ma' => 'SP003', 'ten' => 'Bánh Mì Xúc Xích', 'gia' => 18000],
            ['ma' => 'SP004', 'ten' => 'Bánh Mì Thập Cẩm', 'gia' => 25000],
            ['ma' => 'SP005', 'ten' => 'Bánh Bao', 'gia' => 10000],
        ];

        foreach ($products as $p) {
            DB::table('san_pham')->insertOrIgnore([
                'danh_muc_id' => $catId,
                'ma_san_pham' => $p['ma'],
                'ten_san_pham' => $p['ten'],
                'gia_ban' => $p['gia'],
                'trang_thai' => 'con_hang',
                'don_vi_tinh' => 'cái',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 2. Tạo Điểm bán
        $agencyId = DB::table('diem_ban')->insertGetId([
            'ma_diem_ban' => 'DB01',
            'ten_diem_ban' => 'Cửa Hàng Trung Tâm',
            'dia_chi' => '123 Đường Chính, TP.HCM',
            'trang_thai' => 'hoat_dong',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Tạo Nhân viên & Gán vào Điểm bán
        $userId = DB::table('nguoi_dung')->insertGetId([
            'ho_ten' => 'Nhân Viên A',
            'email' => 'nhanvien@demo.com',
            'mat_khau' => Hash::make('password'),
            'vai_tro' => 'nhan_vien',
            'trang_thai' => 'hoat_dong',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('nhan_vien_diem_ban')->insert([
            'nguoi_dung_id' => $userId,
            'diem_ban_id' => $agencyId,
            'ngay_bat_dau' => now(),
        ]);

        // 4. Tạo Tồn kho đầu ca (Giả lập đã phân bổ hàng)
        $productIds = DB::table('san_pham')->pluck('id');
        
        foreach ($productIds as $pid) {
            DB::table('ton_kho_diem_ban')->insert([
                'diem_ban_id' => $agencyId,
                'san_pham_id' => $pid,
                'ngay' => Carbon::today(),
                'ton_dau_ca' => 50, // Mỗi loại có 50 cái đầu ca
                'ton_cuoi_ca' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        // 5. Tạo Ca làm việc hôm nay
        DB::table('ca_lam_viec')->insert([
            'diem_ban_id' => $agencyId,
            'nguoi_dung_id' => $userId,
            'ngay_lam' => Carbon::today(),
            'gio_bat_dau' => '06:00:00',
            'gio_ket_thuc' => '14:00:00',
            'trang_thai' => 'dang_lam',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
