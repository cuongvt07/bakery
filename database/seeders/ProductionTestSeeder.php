<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ingredient;
use App\Models\ProductCategory;
use App\Models\Product;
use App\Models\Recipe;
use App\Models\RecipeDetail;

class ProductionTestSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Tạo nguyên liệu với giá nhập
        $ingredients = [
            ['ten' => 'Bột mì', 'don_vi' => 'kg', 'ton_kho' => 100, 'gia' => 15000],
            ['ten' => 'Đường', 'don_vi' => 'kg', 'ton_kho' => 50, 'gia' => 20000],
            ['ten' => 'Trứng gà', 'don_vi' => 'quả', 'ton_kho' => 200, 'gia' => 3000],
            ['ten' => 'Bơ', 'don_vi' => 'kg', 'ton_kho' => 30, 'gia' => 120000],
            ['ten' => 'Sữa tươi', 'don_vi' => 'lít', 'ton_kho' => 40, 'gia' => 25000],
            ['ten' => 'Vani', 'don_vi' => 'lọ', 'ton_kho' => 10, 'gia' => 30000],
            ['ten' => 'Muối', 'don_vi' => 'kg', 'ton_kho' => 20, 'gia' => 8000],
            ['ten' => 'Bột nở', 'don_vi' => 'gói', 'ton_kho' => 50, 'gia' => 5000],
        ];

        $ingredientModels = [];
        foreach ($ingredients as $ing) {
            $ingredientModels[$ing['ten']] = Ingredient::create([
                'ten_nguyen_lieu' => $ing['ten'],
                'don_vi_tinh' => $ing['don_vi'],
                'ton_kho_hien_tai' => $ing['ton_kho'],
                'ton_kho_toi_thieu' => 10,
                'gia_nhap' => $ing['gia'],
            ]);
        }

        // 2. Tạo danh mục (size bánh)
        $categories = [
            ['ten' => 'Nhỏ (15cm)', 'thu_tu' => 1],
            ['ten' => 'Vừa (20cm)', 'thu_tu' => 2],
            ['ten' => 'Lớn (25cm)', 'thu_tu' => 3],
        ];

        $categoryModels = [];
        foreach ($categories as $cat) {
            $categoryModels[] = ProductCategory::create([
                'ten_danh_muc' => $cat['ten'],
                'thu_tu' => $cat['thu_tu'],
            ]);
        }

        // 3. Tạo sản phẩm
        $products = [
            ['ma' => 'SP-001', 'ten' => 'Bánh Bông Lan Trứng Muối', 'gia' => 150000, 'danh_muc_id' => $categoryModels[1]->id],
            ['ma' => 'SP-002', 'ten' => 'Bánh Gato Trái Cây', 'gia' => 200000, 'danh_muc_id' => $categoryModels[1]->id],
            ['ma' => 'SP-003', 'ten' => 'Bánh Kem Dâu', 'gia' => 180000, 'danh_muc_id' => $categoryModels[1]->id],
        ];

        $productModels = [];
        foreach ($products as $prod) {
            $productModels[$prod['ma']] = Product::create([
                'ma_san_pham' => $prod['ma'],
                'ten_san_pham' => $prod['ten'],
                'gia_ban' => $prod['gia'],
                'danh_muc_id' => $prod['danh_muc_id'],
                'trang_thai' => 'con_hang',
            ]);
        }

        // 4. Tạo công thức
        $recipes = [
            [
                'ma' => 'CT-001',
                'ten' => 'Công thức Bánh Bông Lan',
                'san_pham_id' => $productModels['SP-001']->id,
                'so_luong' => 10,
                'don_vi' => 'cái',
                'ingredients' => [
                    ['ten' => 'Bột mì', 'so_luong' => 2],
                    ['ten' => 'Trứng gà', 'so_luong' => 10],
                    ['ten' => 'Đường', 'so_luong' => 0.5],
                    ['ten' => 'Sữa tươi', 'so_luong' => 0.3],
                    ['ten' => 'Bột nở', 'so_luong' => 2],
                ],
            ],
            [
                'ma' => 'CT-002',
                'ten' => 'Công thức Bánh Gato',
                'san_pham_id' => $productModels['SP-002']->id,
                'so_luong' => 8,
                'don_vi' => 'cái',
                'ingredients' => [
                    ['ten' => 'Bột mì', 'so_luong' => 1.5],
                    ['ten' => 'Trứng gà', 'so_luong' => 8],
                    ['ten' => 'Đường', 'so_luong' => 0.6],
                    ['ten' => 'Bơ', 'so_luong' => 0.3],
                    ['ten' => 'Sữa tươi', 'so_luong' => 0.5],
                    ['ten' => 'Vani', 'so_luong' => 1],
                ],
            ],
            [
                'ma' => 'CT-003',
                'ten' => 'Công thức Bánh Kem Dâu',
                'san_pham_id' => $productModels['SP-003']->id,
                'so_luong' => 12,
                'don_vi' => 'cái',
                'ingredients' => [
                    ['ten' => 'Bột mì', 'so_luong' => 1.8],
                    ['ten' => 'Trứng gà', 'so_luong' => 12],
                    ['ten' => 'Đường', 'so_luong' => 0.7],
                    ['ten' => 'Bơ', 'so_luong' => 0.4],
                    ['ten' => 'Sữa tươi', 'so_luong' => 0.6],
                    ['ten' => 'Muối', 'so_luong' => 0.01],
                ],
            ],
        ];

        foreach ($recipes as $rec) {
            $recipe = Recipe::create([
                'ma_cong_thuc' => $rec['ma'],
                'ten_cong_thuc' => $rec['ten'],
                'san_pham_id' => $rec['san_pham_id'],
                'so_luong_san_xuat' => $rec['so_luong'],
                'don_vi_san_xuat' => $rec['don_vi'],
                'trang_thai' => 'hoat_dong',
            ]);

            // Tạo chi tiết công thức
            foreach ($rec['ingredients'] as $ing) {
                $ingredient = $ingredientModels[$ing['ten']];
                RecipeDetail::create([
                    'cong_thuc_id' => $recipe->id,
                    'nguyen_lieu_id' => $ingredient->id,
                    'so_luong' => $ing['so_luong'],
                    'don_vi' => $ingredient->don_vi_tinh,
                    'don_gia' => $ingredient->gia_nhap,
                ]);
            }

            // Tính chi phí cho công thức
            $recipe->calculateCost();
        }

        $this->command->info('✅ Đã tạo xong dữ liệu test cho sản xuất:');
        $this->command->info('   - ' . count($ingredients) . ' nguyên liệu');
        $this->command->info('   - ' . count($categories) . ' size bánh');
        $this->command->info('   - ' . count($products) . ' sản phẩm');
        $this->command->info('   - ' . count($recipes) . ' công thức');
    }
}
