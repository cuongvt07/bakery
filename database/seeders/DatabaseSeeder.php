<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Suppliers (Nhà cung cấp)
        $suppliers = [
            ['ma_ncc' => 'NCC-001', 'ten_ncc' => 'Công ty Bột Mì Sài Gòn', 'dia_chi' => 'Q.1, TP.HCM', 'so_dien_thoai' => '0283822333', 'email' => 'info@botmisaigon.vn'],
            ['ma_ncc' => 'NCC-002', 'ten_ncc' => 'Vinamilk - Chi nhánh HCM', 'dia_chi' => 'Q.Bình Thạnh, TP.HCM', 'so_dien_thoai' => '0288888888', 'email' => 'sales@vinamilk.com.vn'],
            ['ma_ncc' => 'NCC-003', 'ten_ncc' => 'Đường Biên Hòa', 'dia_chi' => 'Biên Hòa, Đồng Nai', 'so_dien_thoai' => '0251123456', 'email' => 'contact@duongbienhoa.vn'],
            ['ma_ncc' => 'NCC-004', 'ten_ncc' => 'Trứng Sạch Long Thành', 'dia_chi' => 'Long Thành, Đồng Nai', 'so_dien_thoai' => '0909111222', 'email' => 'info@trungsach.vn'],
        ];
        
        foreach ($suppliers as $s) {
            DB::table('nha_cung_cap')->insert($s + ['created_at' => now(), 'updated_at' => now()]);
        }

        // 2. Ingredients (Nguyên liệu)
        $ingredients = [
            // Bột
            ['ma_nguyen_lieu' => 'NVL-001', 'ten_nguyen_lieu' => 'Bột mì đa dụng', 'don_vi_tinh' => 'kg', 'ton_kho_hien_tai' => 100, 'ton_kho_toi_thieu' => 20],
            ['ma_nguyen_lieu' => 'NVL-002', 'ten_nguyen_lieu' => 'Bột mì số 8', 'don_vi_tinh' => 'kg', 'ton_kho_hien_tai' => 50, 'ton_kho_toi_thieu' => 10],
            ['ma_nguyen_lieu' => 'NVL-003', 'ten_nguyen_lieu' => 'Bột nở', 'don_vi_tinh' => 'gói', 'ton_kho_hien_tai' => 30, 'ton_kho_toi_thieu' => 5],
            
            // Sữa & Bơ
            ['ma_nguyen_lieu' => 'NVL-004', 'ten_nguyen_lieu' => 'Sữa tươi không đường', 'don_vi_tinh' => 'lít', 'ton_kho_hien_tai' => 40, 'ton_kho_toi_thieu' => 10],
            ['ma_nguyen_lieu' => 'NVL-005', 'ten_nguyen_lieu' => 'Sữa đặc có đường', 'don_vi_tinh' => 'hộp', 'ton_kho_hien_tai' => 50, 'ton_kho_toi_thieu' => 10],
            ['ma_nguyen_lieu' => 'NVL-006', 'ten_nguyen_lieu' => 'Bơ lạt', 'don_vi_tinh' => 'kg', 'ton_kho_hien_tai' => 20, 'ton_kho_toi_thieu' => 5],
            ['ma_nguyen_lieu' => 'NVL-007', 'ten_nguyen_lieu' => 'Kem tươi Whipping', 'don_vi_tinh' => 'lít', 'ton_kho_hien_tai' => 15, 'ton_kho_toi_thieu' => 3],
            
            // Đường & Muối
            ['ma_nguyen_lieu' => 'NVL-008', 'ten_nguyen_lieu' => 'Đường cát trắng', 'don_vi_tinh' => 'kg', 'ton_kho_hien_tai' => 80, 'ton_kho_toi_thieu' => 15],
            ['ma_nguyen_lieu' => 'NVL-009', 'ten_nguyen_lieu' => 'Đường bột', 'don_vi_tinh' => 'kg', 'ton_kho_hien_tai' => 30, 'ton_kho_toi_thieu' => 5],
            ['ma_nguyen_lieu' => 'NVL-010', 'ten_nguyen_lieu' => 'Muối', 'don_vi_tinh' => 'kg', 'ton_kho_hien_tai' => 20, 'ton_kho_toi_thieu' => 3],
            
            // Trứng
            ['ma_nguyen_lieu' => 'NVL-011', 'ten_nguyen_lieu' => 'Trứng gà', 'don_vi_tinh' => 'quả', 'ton_kho_hien_tai' => 200, 'ton_kho_toi_thieu' => 50],
            
            // Khác
            ['ma_nguyen_lieu' => 'NVL-012', 'ten_nguyen_lieu' => 'Vani', 'don_vi_tinh' => 'chai', 'ton_kho_hien_tai' => 10, 'ton_kho_toi_thieu' => 2],
            ['ma_nguyen_lieu' => 'NVL-013', 'ten_nguyen_lieu' => 'Bột cacao', 'don_vi_tinh' => 'kg', 'ton_kho_hien_tai' => 10, 'ton_kho_toi_thieu' => 2],
            ['ma_nguyen_lieu' => 'NVL-014', 'ten_nguyen_lieu' => 'Chocolate chips', 'don_vi_tinh' => 'kg', 'ton_kho_hien_tai' => 8, 'ton_kho_toi_thieu' => 2],
            ['ma_nguyen_lieu' => 'NVL-015', 'ten_nguyen_lieu' => 'Dầu ăn', 'don_vi_tinh' => 'lít', 'ton_kho_hien_tai' => 25, 'ton_kho_toi_thieu' => 5],
        ];
        
        foreach ($ingredients as $i) {
            DB::table('nguyen_lieu')->insert($i + ['created_at' => now(), 'updated_at' => now()]);
        }

        // 3. Product Categories
        $categories = [
            ['ten_danh_muc' => 'Bánh Flan', 'mo_ta' => 'Bánh flan các loại'],
            ['ten_danh_muc' => 'Bánh Bông Lan', 'mo_ta' => 'Bánh bông lan, sponge cake'],
            ['ten_danh_muc' => 'Bánh Cookies', 'mo_ta' => 'Bánh quy, cookies'],
            ['ten_danh_muc' => 'Bánh Kem', 'mo_ta' => 'Bánh kem sinh nhật, cupcake'],
            ['ten_danh_muc' => 'Bánh Mì', 'mo_ta' => 'Bánh mì sandwich, baguette'],
        ];
        
        foreach ($categories as $c) {
            DB::table('danh_muc_san_pham')->insert($c + ['created_at' => now(), 'updated_at' => now()]);
        }

        // 4. Products
        $products = [
            ['ma_san_pham' => 'FLAN-01', 'ten_san_pham' => 'Bánh Flan Truyền Thống', 'danh_muc_id' => 1, 'gia_ban' => 5000, 'don_vi_tinh' => 'cái', 'don_vi_phan_phoi' => 'Khay', 'so_luong_quy_doi' => 10, 'trang_thai' => 'con_hang'],
            ['ma_san_pham' => 'FLAN-02', 'ten_san_pham' => 'Bánh Flan Cafe', 'danh_muc_id' => 1, 'gia_ban' => 6000, 'don_vi_tinh' => 'cái', 'don_vi_phan_phoi' => 'Khay', 'so_luong_quy_doi' => 10, 'trang_thai' => 'con_hang'],
            
            ['ma_san_pham' => 'BL-01', 'ten_san_pham' => 'Bánh Bông Lan Trứng Muối', 'danh_muc_id' => 2, 'gia_ban' => 8000, 'don_vi_tinh' => 'cái', 'don_vi_phan_phoi' => 'Hộp', 'so_luong_quy_doi' => 6, 'trang_thai' => 'con_hang'],
            ['ma_san_pham' => 'BL-02', 'ten_san_pham' => 'Bánh Bông Lan Cuộn', 'danh_muc_id' => 2, 'gia_ban' => 45000, 'don_vi_tinh' => 'cái', 'don_vi_phan_phoi' => null, 'so_luong_quy_doi' => 1, 'trang_thai' => 'con_hang'],
            
            ['ma_san_pham' => 'CK-01', 'ten_san_pham' => 'Cookies Chocolate Chip', 'danh_muc_id' => 3, 'gia_ban' => 3000, 'don_vi_tinh' => 'cái', 'don_vi_phan_phoi' => 'túi', 'so_luong_quy_doi' => 5, 'trang_thai' => 'con_hang'],
            ['ma_san_pham' => 'CK-02', 'ten_san_pham' => 'Cookies Bơ', 'danh_muc_id' => 3, 'gia_ban' => 2500, 'don_vi_tinh' => 'cái', 'don_vi_phan_phoi' => 'túi', 'so_luong_quy_doi' => 5, 'trang_thai' => 'con_hang'],
            
            ['ma_san_pham' => 'CAKE-01', 'ten_san_pham' => 'Cupcake Vani', 'danh_muc_id' => 4, 'gia_ban' => 12000, 'don_vi_tinh' => 'cái', 'don_vi_phan_phoi' => 'Hộp', 'so_luong_quy_doi' => 4, 'trang_thai' => 'con_hang'],
            
            ['ma_san_pham' => 'BM-01', 'ten_san_pham' => 'Bánh Mì Sandwich', 'danh_muc_id' => 5, 'gia_ban' => 15000, 'don_vi_tinh' => 'cái', 'don_vi_phan_phoi' => null, 'so_luong_quy_doi' => 1, 'trang_thai' => 'con_hang'],
        ];
        
        foreach ($products as $p) {
            DB::table('san_pham')->insert($p + ['created_at' => now(), 'updated_at' => now()]);
        }

        // 5. Recipes (Công thức)
        $recipes = [
            // Flan Truyền Thống (Product ID: 1)
            [
                'ma_cong_thuc' => 'CT-FLAN-01',
                'ten_cong_thuc' => 'Công thức Flan Truyền Thống',
                'san_pham_id' => 1,
                'so_luong_san_xuat' => 100,
                'don_vi_san_xuat' => 'cái',
                'mo_ta' => 'Flan truyền thống với caramel',
                'trang_thai' => 'hoat_dong',
            ],
            // Bánh Bông Lan Trứng Muối (Product ID: 3)
            [
                'ma_cong_thuc' => 'CT-BL-01',
                'ten_cong_thuc' => 'Công thức Bông Lan Trứng Muối',
                'san_pham_id' => 3,
                'so_luong_san_xuat' => 50,
                'don_vi_san_xuat' => 'cái',
                'mo_ta' => 'Bánh bông lan trứng muối thơm ngon',
                'trang_thai' => 'hoat_dong',
            ],
            // Cookies Chocolate (Product ID: 5)
            [
                'ma_cong_thuc' => 'CT-CK-01',
                'ten_cong_thuc' => 'Công thức Cookies Chocolate Chip',
                'san_pham_id' => 5,
                'so_luong_san_xuat' => 80,
                'don_vi_san_xuat' => 'cái',
                'mo_ta' => 'Cookies giòn rụm với chocolate chips',
                'trang_thai' => 'hoat_dong',
            ],
        ];
        
        foreach ($recipes as $r) {
            DB::table('cong_thuc_san_xuat')->insert($r + ['created_at' => now(), 'updated_at' => now()]);
        }

        // 6. Recipe Details (Chi tiết công thức)
        $recipeDetails = [
            // CT-FLAN-01 (Recipe ID: 1) - 100 cái
            ['cong_thuc_id' => 1, 'nguyen_lieu_id' => 4, 'so_luong' => 5, 'don_vi' => 'lít', 'don_gia' => 28000], // Sữa tươi
            ['cong_thuc_id' => 1, 'nguyen_lieu_id' => 5, 'so_luong' => 4, 'don_vi' => 'hộp', 'don_gia' => 12000], // Sữa đặc
            ['cong_thuc_id' => 1, 'nguyen_lieu_id' => 11, 'so_luong' => 30, 'don_vi' => 'quả', 'don_gia' => 3000], // Trứng
            ['cong_thuc_id' => 1, 'nguyen_lieu_id' => 8, 'so_luong' => 2, 'don_vi' => 'kg', 'don_gia' => 18000], // Đường
            ['cong_thuc_id' => 1, 'nguyen_lieu_id' => 12, 'so_luong' => 1, 'don_vi' => 'chai', 'don_gia' => 15000], // Vani
            
            // CT-BL-01 (Recipe ID: 2) - 50 cái
            ['cong_thuc_id' => 2, 'nguyen_lieu_id' => 1, 'so_luong' => 3, 'don_vi' => 'kg', 'don_gia' => 22000], // Bột mì
            ['cong_thuc_id' => 2, 'nguyen_lieu_id' => 11, 'so_luong' => 40, 'don_vi' => 'quả', 'don_gia' => 3000], // Trứng
            ['cong_thuc_id' => 2, 'nguyen_lieu_id' => 8, 'so_luong' => 1.5, 'don_vi' => 'kg', 'don_gia' => 18000], // Đường
            ['cong_thuc_id' => 2, 'nguyen_lieu_id' => 6, 'so_luong' => 0.5, 'don_vi' => 'kg', 'don_gia' => 120000], // Bơ
            ['cong_thuc_id' => 2, 'nguyen_lieu_id' => 3, 'so_luong' => 3, 'don_vi' => 'gói', 'don_gia' => 5000], // Bột nở
            ['cong_thuc_id' => 2, 'nguyen_lieu_id' => 4, 'so_luong' => 1, 'don_vi' => 'lít', 'don_gia' => 28000], // Sữa
            
            // CT-CK-01 (Recipe ID: 3) - 80 cái
            ['cong_thuc_id' => 3, 'nguyen_lieu_id' => 1, 'so_luong' => 2, 'don_vi' => 'kg', 'don_gia' => 22000], // Bột mì
            ['cong_thuc_id' => 3, 'nguyen_lieu_id' => 6, 'so_luong' => 1, 'don_vi' => 'kg', 'don_gia' => 120000], // Bơ
            ['cong_thuc_id' => 3, 'nguyen_lieu_id' => 8, 'so_luong' => 1, 'don_vi' => 'kg', 'don_gia' => 18000], // Đường
            ['cong_thuc_id' => 3, 'nguyen_lieu_id' => 11, 'so_luong' => 10, 'don_vi' => 'quả', 'don_gia' => 3000], // Trứng
            ['cong_thuc_id' => 3, 'nguyen_lieu_id' => 14, 'so_luong' => 0.5, 'don_vi' => 'kg', 'don_gia' => 150000], // Chocolate chips
            ['cong_thuc_id' => 3, 'nguyen_lieu_id' => 12, 'so_luong' => 1, 'don_vi' => 'chai', 'don_gia' => 15000], // Vani
        ];
        
        foreach ($recipeDetails as $rd) {
            DB::table('chi_tiet_cong_thuc')->insert($rd + ['created_at' => now(), 'updated_at' => now()]);
        }

        // Calculate recipe costs
        DB::statement('
            UPDATE cong_thuc_san_xuat r
            SET chi_phi_uoc_tinh = (
                SELECT SUM(so_luong * don_gia)
                FROM chi_tiet_cong_thuc
                WHERE cong_thuc_id = r.id
            )
        ');

        // 7. Agencies (Điểm bán)
        $agencies = [
            ['ma_diem_ban' => 'DB-01', 'ten_diem_ban' => 'Điểm Quận 1', 'dia_chi' => '123 Nguyễn Huệ, Q.1, TP.HCM', 'so_dien_thoai' => '0283123456', 'trang_thai' => 'hoat_dong'],
            ['ma_diem_ban' => 'DB-02', 'ten_diem_ban' => 'Điểm Quận 3', 'dia_chi' => '456 Võ Văn Tần, Q.3, TP.HCM', 'so_dien_thoai' => '0283789012', 'trang_thai' => 'hoat_dong'],
            ['ma_diem_ban' => 'DB-03', 'ten_diem_ban' => 'Điểm Bình Thạnh', 'dia_chi' => '789 Xô Viết Nghệ Tĩnh, Q.Bình Thạnh, TP.HCM', 'so_dien_thoai' => '0283345678', 'trang_thai' => 'hoat_dong'],
        ];
        
        foreach ($agencies as $a) {
            DB::table('diem_ban')->insert($a + ['created_at' => now(), 'updated_at' => now()]);
        }

        // 8. Users
        $users = [
            [
                'ho_ten' => 'Admin',
                'email' => 'admin@bakery.vn',
                'mat_khau' => Hash::make('admin123'),
                'vai_tro' => 'admin',
                'so_dien_thoai' => '0901234567',
                'trang_thai' => 'hoat_dong',
            ],
            [
                'ho_ten' => 'Nguyễn Văn A',
                'email' => 'nva@bakery.vn',
                'mat_khau' => Hash::make('nhanvien123'),
                'vai_tro' => 'nhan_vien',
                'so_dien_thoai' => '0902345678',
                'trang_thai' => 'hoat_dong',
            ],
            [
                'ho_ten' => 'Trần Thị B',
                'email' => 'ttb@bakery.vn',
                'mat_khau' => Hash::make('nhanvien123'),
                'vai_tro' => 'nhan_vien',
                'so_dien_thoai' => '0903456789',
                'trang_thai' => 'hoat_dong',
            ],
        ];
        
        foreach ($users as $u) {
            DB::table('nguoi_dung')->insert($u + ['created_at' => now(), 'updated_at' => now()]);
        }

        // 9. Assign employees to agencies
        DB::table('nhan_vien_diem_ban')->insert([
            ['diem_ban_id' => 1, 'nguoi_dung_id' => 2, 'ngay_bat_dau' => Carbon::now()->subDays(30), 'created_at' => now(), 'updated_at' => now()],
            ['diem_ban_id' => 2, 'nguoi_dung_id' => 3, 'ngay_bat_dau' => Carbon::now()->subDays(20), 'created_at' => now(), 'updated_at' => now()],
        ]);

        echo "\n✅ Seeder completed successfully!\n";
        echo "📊 Summary:\n";
        echo "   - Suppliers: " . count($suppliers) . "\n";
        echo "   - Ingredients: " . count($ingredients) . "\n";
        echo "   - Product Categories: " . count($categories) . "\n";
        echo "   - Products: " . count($products) . "\n";
        echo "   - Recipes: " . count($recipes) . "\n";
        echo "   - Recipe Details: " . count($recipeDetails) . "\n";
        echo "   - Agencies: " . count($agencies) . "\n";
        echo "   - Users: " . count($users) . "\n";
        echo "\n🔐 Login credentials:\n";
        echo "   Admin: admin@bakery.vn / admin123\n";
        echo "   Employee: nva@bakery.vn / nhanvien123\n\n";
    }
}
