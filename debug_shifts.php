<?php

use App\Models\ShiftSchedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

// Mock login or just specify ID
$userId = 5; 

echo "Server Timezone: " . config('app.timezone') . "\n";
echo "Now: " . now()->toDateTimeString() . "\n";
echo "Carbon Today: " . Carbon::today()->toDateTimeString() . "\n";

DB::enableQueryLog();

$shifts = ShiftSchedule::where('nguoi_dung_id', $userId)
    ->whereDate('ngay_lam', Carbon::today())
    ->whereIn('trang_thai', ['approved', 'pending'])
    ->get();

echo "Query count: " . $shifts->count() . "\n";
print_r(DB::getQueryLog());

foreach ($shifts as $s) {
    echo "Found Shift ID: {$s->id}, Date: {$s->ngay_lam->toDateString()}, Status: {$s->trang_thai}\n";
}
