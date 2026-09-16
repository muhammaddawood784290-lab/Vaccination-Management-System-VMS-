<?php

namespace Database\Seeders;

use App\Models\Hospital;
use App\Models\Vaccine;
use App\Models\VaccineInventory;
use Illuminate\Database\Seeder;

class VaccineInventorySeeder extends Seeder
{
    public function run(): void
    {
        $hospitals = Hospital::where('status', 'active')->get();
        $vaccines = Vaccine::where('status', 'active')->get();

        foreach ($hospitals as $hospital) {
            foreach ($vaccines as $vaccine) {
                // Random stock for each hospital-vaccine combination
                $capacity = rand(50, 200);
                $available = rand(0, $capacity);

                VaccineInventory::create([
                    'hospital_id' => $hospital->id,
                    'vaccine_id' => $vaccine->id,
                    'available' => $available,
                    'capacity' => $capacity,
                    'batch_number' => 'BATCH-' . strtoupper(substr($vaccine->code, 0, 3)) . '-' . $hospital->id . '-' . $vaccine->id,
                    'expiry_date' => now()->addMonths(rand(6, 24)),
                    'last_updated' => now()->subDays(rand(0, 30)),
                ]);
            }
        }
    }
}
