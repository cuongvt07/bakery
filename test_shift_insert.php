<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    DB::table('ca_lam_viec')->insert([
        'diem_ban_id' => 1,
        'nguoi_dung_id' => 1,
        'shift_template_id' => 1,
        'ngay_lam' => '2025-12-11',
        'gio_bat_dau' => '08:00:00',
        'gio_ket_thuc' => '12:00:00',
        'trang_thai' => 'chua_bat_dau',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    echo "✅ Success!\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "File: " . $e->getFile() . "\n";
}
