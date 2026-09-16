<?php

namespace App\Console\Commands;

use App\Models\{Child, Hospital, User, Vaccine, VaccineInventory};
use App\Support\SeedContract;
use Illuminate\Console\Command;

class SeedIntegrityCheck extends Command
{
    protected $signature = 'db:seed-check';

    protected $description = 'Verify users, children, hospitals, vaccines and inventory match the seeder contract (detects seed drift)';

    public function handle(): int
    {
        $failures = SeedContract::driftFindings();

        if ($failures === []) {
            $this->info('Seed integrity OK: users, children, hospitals, vaccines and inventory match the seeder contract.');
            $this->line('  users: ' . User::count() . ' / expected ' . count(SeedContract::USERS));
            $this->line('  children: ' . Child::count() . ' / expected ' . count(SeedContract::CHILDREN));
            $this->line('  hospitals: ' . Hospital::count() . ' / expected ' . count(SeedContract::HOSPITALS));
            $this->line('  vaccines: ' . Vaccine::count() . ' / expected ' . count(SeedContract::VACCINES));
            $this->line('  inventory: ' . VaccineInventory::count() . ' / expected ' . SeedContract::expectedInventoryCount());

            return self::SUCCESS;
        }

        $this->error('Seed integrity FAILED — database has drifted from the seeder contract:');

        foreach ($failures as $failure) {
            $this->line('  - ' . $failure);
        }

        $this->newLine();
        $this->line('Fix: run "php artisan db:restore-demo --dry-run" to preview an automatic repair,');
        $this->line('or "php artisan migrate:fresh --seed" (destroys all data).');

        return self::FAILURE;
    }
}
