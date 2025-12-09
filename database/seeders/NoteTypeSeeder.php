<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Agency;
use App\Models\NoteType;

class NoteTypeSeeder extends Seeder
{
    public function run(): void
    {
        // Get all existing agencies
        $agencies = Agency::all();

        foreach ($agencies as $agency) {
            // Check if vat_dung already exists
            $existing = NoteType::where('diem_ban_id', $agency->id)
                ->where('ma_loai', 'vat_dung')
                ->first();

            if (!$existing) {
                NoteType::createDefaultForAgency($agency->id);
            }
        }

        // Note: Other types will be added by users through UI
    }
}
