<?php

use App\Models\AgencyLocation;
use App\Models\Agency;

$agency = Agency::first();

if (!$agency) {
    echo "No agency found.\n";
    exit;
}

echo "Creating location for Agency: " . $agency->ten_diem_ban . "\n";

$loc = AgencyLocation::create([
    'diem_ban_id' => $agency->id,
    'ma_vi_tri' => 'TEST_DESC',
    'ten_vi_tri' => 'Test Description Save',
    'mo_ta' => 'This is a test description.',
    'dia_chi' => 'Test Address'
]);

echo "Created Location ID: " . $loc->id . "\n";
echo "Description in Object: '" . $loc->mo_ta . "'\n";

$reloaded = AgencyLocation::find($loc->id);
echo "Description in DB: '" . $reloaded->mo_ta . "'\n";
