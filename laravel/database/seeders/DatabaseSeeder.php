<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Order matters due to foreign key constraints
        $this->call([
            HospitalSeeder::class,       // 1. Hospitals first (referenced by users.hospital_id)
            VaccineSeeder::class,        // 2. Vaccine catalogue
            UserSeeder::class,           // 3. Users (admin, parent, hospital — links to hospitals)
            ChildSeeder::class,          // 4. Children (links to parent users)
            VaccineInventorySeeder::class, // 5. Hospital vaccine stock (links to hospitals + vaccines)
        ]);
    }
}
