<?php

namespace App\Console\Commands;

use App\Models\Child;
use App\Models\Hospital;
use App\Models\User;
use App\Models\Vaccine;
use App\Models\VaccineInventory;
use App\Support\SeedContract;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class RestoreDemoData extends Command
{
    protected $signature = 'db:restore-demo
                            {--dry-run : Show what would be repaired without changing anything}
                            {--force : Apply repairs without asking for confirmation}';

    protected $description = 'Repair seed drift: remove rows not in the seeder contract and restore missing/changed demo users, children, hospitals, vaccines and inventory (non-destructive to valid data)';

    public function handle(): int
    {
        $findings = SeedContract::driftFindings();

        if ($findings === []) {
            $this->info('No drift detected. Database already matches the seeder contract. Nothing to do.');

            return self::SUCCESS;
        }

        $this->warn('Drift detected against the seeder contract:');
        foreach ($findings as $finding) {
            $this->line('  - ' . $finding);
        }

        $plan = $this->buildPlan();

        if ($plan === []) {
            $this->warn('No automatic repair available for the findings above.');
            $this->line('These need manual attention.');

            return self::FAILURE;
        }

        $this->newLine();
        $this->info($this->option('dry-run')
            ? 'DRY RUN — the following repairs would be applied:'
            : 'Planned repairs:');

        foreach ($plan as $action) {
            $this->line('  * ' . $action);
        }

        if ($this->option('dry-run')) {
            $this->newLine();
            $this->line('Nothing was changed. Re-run without --dry-run to apply.');

            return self::SUCCESS;
        }

        if (!$this->option('force') && !$this->confirm('Apply these repairs now?')) {
            $this->line('Aborted. No changes were made.');

            return self::SUCCESS;
        }

        $this->applyPlan();

        // Verify the repair.
        $remaining = SeedContract::driftFindings();

        $this->newLine();
        if ($remaining === []) {
            $this->info('Repair complete. Database now matches the seeder contract.');
            $this->line('  users: ' . User::count() . ' / expected ' . count(SeedContract::USERS));
            $this->line('  children: ' . Child::count() . ' / expected ' . count(SeedContract::CHILDREN));
            $this->line('  hospitals: ' . Hospital::count() . ' / expected ' . count(SeedContract::HOSPITALS));
            $this->line('  vaccines: ' . Vaccine::count() . ' / expected ' . count(SeedContract::VACCINES));
            $this->line('  inventory: ' . VaccineInventory::count() . ' / expected ' . SeedContract::expectedInventoryCount());

            return self::SUCCESS;
        }

        $this->error('Repair applied, but drift remains:');
        foreach ($remaining as $finding) {
            $this->line('  - ' . $finding);
        }

        return self::FAILURE;
    }

    /**
     * Build the list of repair actions from the current drift.
     * Actions are listed (and executed) in FK-safe order.
     *
     * @return string[] action descriptions
     */
    private function buildPlan(): array
    {
        $plan = [];

        $users = User::all()->keyBy('email');
        $children = Child::all();
        $usersById = $users->keyBy('id');
        $hospitals = Hospital::all()->keyBy('code');
        $vaccines = Vaccine::all()->keyBy('code');

        // 1. Delete users not in the contract (FK cascades remove their notifications,
        //    requests, children, appointments, schedules and vaccination records).
        foreach ($users as $email => $user) {
            if (!array_key_exists($email, SeedContract::USERS)) {
                $plan[] = "delete user {$email} and their dependent rows";
            }
        }

        // 2. Delete hospitals not in the contract (cascades their inventory,
        //    appointments and vaccination records; linked users get re-linked below).
        foreach ($hospitals as $code => $hospital) {
            if (!array_key_exists($code, SeedContract::HOSPITALS)) {
                $plan[] = "delete extra hospital {$hospital->name} ({$code}) and its dependent rows";
            }
        }

        // 3. Delete vaccines not in the contract (cascades inventory, schedules,
        //    appointments and vaccination records).
        foreach ($vaccines as $code => $vaccine) {
            if (!array_key_exists($code, SeedContract::VACCINES)) {
                $plan[] = "delete extra vaccine {$vaccine->name} ({$code}) and its dependent rows";
            }
        }

        // 4. Delete orphaned children and extra children of seeded parents.
        //    Children of extra users are NOT listed: deleting their user (step 1)
        //    already cascades them, so planning it again would be redundant.
        foreach ($children as $child) {
            $parent = $usersById->get($child->user_id);

            if ($parent === null) {
                $plan[] = "delete orphaned child '{$child->name}' (id={$child->id}, parent user missing)";
                continue;
            }

            if (!array_key_exists($child->name, SeedContract::CHILDREN)
                && array_key_exists($parent->email, SeedContract::USERS)) {
                $plan[] = "delete extra child '{$child->name}' (id={$child->id}, parent={$parent->email})";
            }
        }

        // 5. Delete inventory rows that are unmapped to the contract or carry invalid counts
        //    (missing rows are recreated with seeder-style values below).
        foreach (VaccineInventory::with(['hospital:id,code', 'vaccine:id,code'])->get() as $row) {
            $pair = $row->hospital?->code . '|' . $row->vaccine?->code;
            $mapped = $row->hospital !== null && $row->vaccine !== null
                && isset(SeedContract::HOSPITALS[$row->hospital->code])
                && isset(SeedContract::VACCINES[$row->vaccine->code]);

            if (!$mapped) {
                $plan[] = "delete unmapped inventory row id={$row->id}";
            } elseif ($row->capacity < 0 || $row->available < 0 || $row->available > $row->capacity) {
                $plan[] = "recreate inventory row for {$row->hospital->code}/{$row->vaccine->code} (invalid counts available={$row->available}, capacity={$row->capacity})";
            }
        }

        // 6. Create missing hospitals and reset status drift.
        foreach (SeedContract::HOSPITALS as $code => $expected) {
            $hospital = $hospitals->get($code);

            if ($hospital === null) {
                $plan[] = "create missing hospital {$code} with seeded fields";
            } elseif ($hospital->status !== $expected['status']) {
                $plan[] = "reset status of hospital {$code} to '{$expected['status']}'";
            }
        }

        // 7. Create missing vaccines and reset status drift.
        foreach (SeedContract::VACCINES as $code => $expected) {
            $vaccine = $vaccines->get($code);

            if ($vaccine === null) {
                $plan[] = "create missing vaccine {$code} with seeded fields";
            } elseif ($vaccine->status !== $expected['status']) {
                $plan[] = "reset status of vaccine {$code} to '{$expected['status']}'";
            }
        }

        // 8. Create missing users.
        foreach (SeedContract::USERS as $email => $expected) {
            if (!$users->has($email)) {
                $plan[] = "create missing user {$email} (role={$expected['role']}) with seeded password";
            }
        }

        // 9. Fix roles, passwords and hospital links on existing seeded users.
        foreach (SeedContract::USERS as $email => $expected) {
            $user = $users->get($email);

            if ($user === null) {
                continue; // handled by step 8
            }

            if ($user->role !== $expected['role']) {
                $plan[] = "reset role of {$email} to '{$expected['role']}'";
            }

            if (!Hash::check(SeedContract::DEMO_PASSWORD, $user->password)) {
                $plan[] = "reset password of {$email} to the seeded demo password";
            }

            $expectedCode = SeedContract::HOSPITAL_LINKS[$email] ?? null;

            if ($expectedCode !== null) {
                $hospital = $hospitals->get($expectedCode);

                if ($hospital !== null && $user->hospital_id !== $hospital->id) {
                    $plan[] = "re-link {$email} to hospital {$expectedCode}";
                } elseif ($hospital === null) {
                    $plan[] = "MANUAL FIX NEEDED: seeded hospital {$expectedCode} is missing (it will be created by this repair)";
                }
            }
        }

        // 10. Reassign drifted children to their contract parent
        //     (if the correct parent already has a child with that name, delete the duplicate instead).
        $reassignPlanned = [];

        foreach ($children as $child) {
            $expected = SeedContract::CHILDREN[$child->name] ?? null;

            if ($expected === null) {
                continue; // handled by step 4
            }

            $parent = $usersById->get($child->user_id);

            if ($parent !== null && $parent->email === $expected['parent']) {
                continue; // correctly assigned
            }

            $correctParent = $users->get($expected['parent']);

            if ($correctParent === null) {
                continue; // created by step 8; handled again below
            }

            $alreadyHas = Child::where('name', $child->name)
                ->where('user_id', $correctParent->id)
                ->where('id', '!=', $child->id)
                ->exists();

            if ($alreadyHas) {
                $plan[] = "delete duplicate child '{$child->name}' (id={$child->id}); correct parent already has one";
            } else {
                $plan[] = "reassign child '{$child->name}' (id={$child->id}) to {$expected['parent']}";
                $reassignPlanned[$child->name . '|' . $correctParent->id] = true;
            }
        }

        // 11. Create missing children, skipping any that a planned reassignment already satisfies.
        foreach (SeedContract::CHILDREN as $name => $expected) {
            $parent = $users->get($expected['parent']);

            if ($parent === null || Child::where('name', $name)->where('user_id', $parent->id)->exists()) {
                continue;
            }

            if (isset($reassignPlanned[$name . '|' . $parent->id])) {
                continue; // the reassignment above will put this child with the right parent
            }

            $plan[] = "create missing child '{$name}' for {$expected['parent']} with seeded fields";
        }

        // 12. Create missing inventory rows for every contract hospital × vaccine combination
        //     not present after the deletions above.
        $contractPairs = [];

        foreach (array_keys(SeedContract::HOSPITALS) as $hospitalCode) {
            foreach (array_keys(SeedContract::VACCINES) as $vaccineCode) {
                $contractPairs[$hospitalCode . '|' . $vaccineCode] = true;
            }
        }

        foreach (VaccineInventory::with(['hospital:id,code', 'vaccine:id,code'])->get() as $row) {
            $pair = $row->hospital?->code . '|' . $row->vaccine?->code;
            $valid = $row->capacity >= 0 && $row->available >= 0 && $row->available <= $row->capacity;

            if ($valid) {
                unset($contractPairs[$pair]);
            }
        }

        foreach (array_keys($contractPairs) as $pair) {
            [$hospitalCode, $vaccineCode] = explode('|', $pair);
            $plan[] = "create missing inventory row for {$hospitalCode}/{$vaccineCode} with seeder-style stock";
        }

        return $plan;
    }

    /**
     * Execute the repair in FK-safe order:
     * deletions (extras) → creations in dependency order → fixes → inventory.
     */
    private function applyPlan(): void
    {
        // 1. Delete extra users (cascades their dependents).
        User::query()
            ->whereNotIn('email', array_keys(SeedContract::USERS))
            ->get()
            ->each(fn (User $user) => $user->delete());

        // 2. Delete extra hospitals (cascades inventory/appointments/records;
        //    users.hospital_id is set null by the FK and re-linked below).
        Hospital::query()
            ->whereNotIn('code', array_keys(SeedContract::HOSPITALS))
            ->get()
            ->each(fn (Hospital $hospital) => $hospital->delete());

        // 3. Delete extra vaccines (cascades inventory/schedules/appointments/records).
        Vaccine::query()
            ->whereNotIn('code', array_keys(SeedContract::VACCINES))
            ->get()
            ->each(fn (Vaccine $vaccine) => $vaccine->delete());

        // 4. Delete orphaned / extra children (cascades their dependents).
        $usersById = User::all()->keyBy('id');
        Child::all()->each(function (Child $child) use ($usersById): void {
            $parent = $usersById->get($child->user_id);

            if ($parent === null || !array_key_exists($child->name, SeedContract::CHILDREN)) {
                $child->delete();
            }
        });

        // 5. Delete unmapped inventory rows and rows with invalid counts
        //    (deleted valid-pair rows are recreated below with seeder-style values).
        VaccineInventory::with(['hospital:id,code', 'vaccine:id,code'])->get()
            ->each(function (VaccineInventory $row): void {
                $mapped = $row->hospital !== null && $row->vaccine !== null
                    && isset(SeedContract::HOSPITALS[$row->hospital->code])
                    && isset(SeedContract::VACCINES[$row->vaccine->code]);

                $valid = $row->capacity >= 0 && $row->available >= 0 && $row->available <= $row->capacity;

                if (!$mapped || !$valid) {
                    $row->delete();
                }
            });

        // 6. Create missing hospitals (before users, which link to them).
        foreach (SeedContract::HOSPITALS as $code => $expected) {
            $hospital = Hospital::where('code', $code)->first();

            if ($hospital === null) {
                Hospital::create(['code' => $code] + $expected);
            } elseif ($hospital->status !== $expected['status']) {
                $hospital->update(['status' => $expected['status']]);
            }
        }

        // 7. Create missing vaccines and reset status drift.
        foreach (SeedContract::VACCINES as $code => $expected) {
            $vaccine = Vaccine::where('code', $code)->first();

            if ($vaccine === null) {
                Vaccine::create(['code' => $code] + $expected);
            } elseif ($vaccine->status !== $expected['status']) {
                $vaccine->update(['status' => $expected['status']]);
            }
        }

        // 8. Create missing users.
        foreach (SeedContract::USERS as $email => $expected) {
            if (User::where('email', $email)->doesntExist()) {
                $hospitalId = null;

                if (isset(SeedContract::HOSPITAL_LINKS[$email])) {
                    $hospitalId = Hospital::where('code', SeedContract::HOSPITAL_LINKS[$email])->value('id');
                }

                User::create([
                    'name' => $expected['name'],
                    'email' => $email,
                    'password' => Hash::make(SeedContract::DEMO_PASSWORD),
                    'role' => $expected['role'],
                    'phone' => $expected['phone'],
                    'city' => $expected['city'],
                    'hospital_id' => $hospitalId,
                ]);
            }
        }

        // 9. Fix roles, passwords, hospital links.
        foreach (SeedContract::USERS as $email => $expected) {
            $user = User::where('email', $email)->first();

            if ($user === null) {
                continue;
            }

            $updates = [];

            if ($user->role !== $expected['role']) {
                $updates['role'] = $expected['role'];
            }

            if (!Hash::check(SeedContract::DEMO_PASSWORD, $user->password)) {
                $updates['password'] = Hash::make(SeedContract::DEMO_PASSWORD);
            }

            $expectedCode = SeedContract::HOSPITAL_LINKS[$email] ?? null;

            if ($expectedCode !== null) {
                $hospitalId = Hospital::where('code', $expectedCode)->value('id');

                if ($hospitalId !== null && $user->hospital_id !== $hospitalId) {
                    $updates['hospital_id'] = $hospitalId;
                }
            }

            if ($updates !== []) {
                $user->update($updates);
            }
        }

        // 10. Reassign drifted children (or delete duplicates).
        $usersByEmail = User::all()->keyBy('email');
        Child::all()->each(function (Child $child) use ($usersByEmail): void {
            $expected = SeedContract::CHILDREN[$child->name] ?? null;

            if ($expected === null) {
                return;
            }

            $correctParent = $usersByEmail->get($expected['parent']);

            if ($correctParent === null || $child->user_id === $correctParent->id) {
                return;
            }

            $duplicateExists = Child::where('name', $child->name)
                ->where('user_id', $correctParent->id)
                ->where('id', '!=', $child->id)
                ->exists();

            if ($duplicateExists) {
                $child->delete();
            } else {
                $child->update(['user_id' => $correctParent->id]);
            }
        });

        // 11. Create missing children.
        $usersByEmail = User::all()->keyBy('email');
        foreach (SeedContract::CHILDREN as $name => $expected) {
            $parent = $usersByEmail->get($expected['parent']);

            if ($parent === null || Child::where('name', $name)->where('user_id', $parent->id)->exists()) {
                continue;
            }

            Child::create([
                'user_id' => $parent->id,
                'name' => $name,
                'date_of_birth' => $expected['date_of_birth'],
                'gender' => $expected['gender'],
                'blood_group' => $expected['blood_group'],
                'relationship' => $expected['relationship'],
                'allergies' => $expected['allergies'],
                'status' => 'active',
            ]);
        }

        // 12. Create missing inventory rows with seeder-style values
        //     (VaccineInventorySeeder randomises stock, so restored rows use the same ranges).
        $hospitalIdsByCode = Hospital::whereIn('code', array_keys(SeedContract::HOSPITALS))->pluck('id', 'code');
        $vaccineIdsByCode = Vaccine::whereIn('code', array_keys(SeedContract::VACCINES))->pluck('id', 'code');
        $existingPairs = VaccineInventory::query()
            ->get(['hospital_id', 'vaccine_id'])
            ->map(fn (VaccineInventory $row) => $row->hospital_id . '|' . $row->vaccine_id)
            ->flip();

        foreach ($hospitalIdsByCode as $hospitalCode => $hospitalId) {
            foreach ($vaccineIdsByCode as $vaccineCode => $vaccineId) {
                if ($existingPairs->has($hospitalId . '|' . $vaccineId)) {
                    continue;
                }

                $capacity = rand(50, 200);
                $available = rand(0, $capacity);

                VaccineInventory::create([
                    'hospital_id' => $hospitalId,
                    'vaccine_id' => $vaccineId,
                    'available' => $available,
                    'capacity' => $capacity,
                    'batch_number' => 'BATCH-' . strtoupper(substr($vaccineCode, 0, 3)) . '-' . $hospitalId . '-' . $vaccineId,
                    'expiry_date' => now()->addMonths(rand(6, 24)),
                    'last_updated' => now()->subDays(rand(0, 30)),
                ]);
            }
        }
    }
}
