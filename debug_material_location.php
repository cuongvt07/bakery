<?php

use App\Models\AgencyNote;
use App\Models\AgencyLocation;

echo "--- Debugging Material Location ---\n";

$note = AgencyNote::where('loai', 'vat_dung')->whereNotNull('vi_tri_id')->first();

if ($note) {
    echo "Note ID: " . $note->id . "\n";
    echo "Title: " . $note->tieu_de . "\n";
    echo "Location ID: " . $note->vi_tri_id . "\n";
    
    $location = AgencyLocation::find($note->vi_tri_id);
    
    if ($location) {
        echo "Location Found!\n";
        echo "Code: " . $location->ma_vi_tri . "\n";
        echo "Name: " . $location->ten_vi_tri . "\n";
        echo "Description (raw): '" . $location->mo_ta . "'\n";
        echo "Description length: " . strlen($location->mo_ta ?? '') . "\n";
    } else {
        echo "Location record NOT found in database!\n";
    }
} else {
    echo "No material found with a location assigned.\n";
}
