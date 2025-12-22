<?php

use App\Models\AgencyLocation;

echo "--- Debugging ANY Location with Description ---\n";

$location = AgencyLocation::whereNotNull('mo_ta')->where('mo_ta', '!=', '')->first();

if ($location) {
    echo "Found Location ID: " . $location->id . "\n";
    echo "Name: " . $location->ten_vi_tri . "\n";
    echo "Description: " . $location->mo_ta . "\n";
} else {
    echo "NO Location found with description!\n";
}
