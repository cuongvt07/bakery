<?php

use App\Livewire\Admin\Ingredient\IngredientForm;
use App\Models\Ingredient;
use Illuminate\Support\Facades\Validator;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing IngredientForm Save Logic...\n";

// Mock data
$data = [
    'ten_nguyen_lieu' => 'Test Ingredient ' . time(),
    'don_vi_tinh' => 'kg',
    'ton_kho_hien_tai' => 10,
    'ton_kho_toi_thieu' => 5,
    'tong_tien_nhap' => 100000,
];

// Simulate Component Logic
$component = new IngredientForm();
$component->ten_nguyen_lieu = $data['ten_nguyen_lieu'];
$component->don_vi_tinh = $data['don_vi_tinh'];
$component->ton_kho_hien_tai = $data['ton_kho_hien_tai'];
$component->ton_kho_toi_thieu = $data['ton_kho_toi_thieu'];
$component->tong_tien_nhap = $data['tong_tien_nhap'];

// Manual Validation (since we can't fully mock Livewire validation easily in script without testbench)
$validator = Validator::make($data, [
    'ten_nguyen_lieu' => 'required|min:2',
    'don_vi_tinh' => 'required',
    'ton_kho_hien_tai' => 'required|numeric|min:0.01',
    'ton_kho_toi_thieu' => 'required|numeric|min:0',
    'tong_tien_nhap' => 'required|numeric|min:0',
]);

if ($validator->fails()) {
    echo "Validation Failed:\n";
    print_r($validator->errors()->all());
    exit(1);
}

// Calculate Price
$component->gia_nhap = $component->ton_kho_hien_tai > 0
    ? round($component->tong_tien_nhap / $component->ton_kho_hien_tai, 2)
    : 0;

echo "Calculated Price: " . $component->gia_nhap . "\n";

// Save
try {
    $saveData = [
        'ten_nguyen_lieu' => $component->ten_nguyen_lieu,
        'don_vi_tinh' => $component->don_vi_tinh,
        'ton_kho_hien_tai' => $component->ton_kho_hien_tai,
        'ton_kho_toi_thieu' => $component->ton_kho_toi_thieu,
        'gia_nhap' => $component->gia_nhap,
    ];

    $ingredient = new Ingredient($saveData);
    $ingredient->save();

    echo "Ingredient Saved Successfully! ID: " . $ingredient->id . ", Code: " . $ingredient->ma_nguyen_lieu . "\n";

    // Clean up
    $ingredient->delete();
    echo "Test Data Cleaned Up.\n";

} catch (\Exception $e) {
    echo "Error Saving: " . $e->getMessage() . "\n";
    exit(1);
}
