<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Agency;
use App\Models\NoteType;

class NoteTypeSeeder extends Seeder
{
    public function run(): void
    {
        // NOTE: vat_dung note type is no longer created by default
        // Materials are now managed in a separate module (admin/materials)
        // Each agency will create their own custom note types as needed
        
        /* DISABLED - Materials moved to separate module
        
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
        
        */

        // Note: Custom note types will be added by agencies through UI
    }
}
