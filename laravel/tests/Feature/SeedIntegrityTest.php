<?php

namespace Tests\Feature;

use App\Models\{Child, Hospital, User, Vaccine, VaccineInventory};
use App\Support\SeedContract;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Guards the seeder contract: users, children, hospitals, vaccines and
 * inventory must match what UserSeeder / ChildSeeder / HospitalSeeder /
 * VaccineSeeder / VaccineInventorySeeder define. Catches the exact seed
 * drift observed during the adversarial audit (leftover audit rows).
 */
class SeedIntegrityTest extends TestCase
{
    use RefreshDatabase;

    /**
     * RefreshDatabase runs migrate:fresh on the in-memory database,
     * then this hook seeds it — so every test starts fully seeded.
     */
    protected function afterRefreshingDatabase(): void
    {
        $this->artisan('db:seed');
    }

    public function test_seed_check_passes_on_a_freshly_seeded_database(): void
    {
        $this->artisan('db:seed-check')->assertExitCode(0);
    }

    public function test_seeded_users_match_the_seeder_contract(): void
    {
        $expectedUsers = [
            'admin@vms.permetheon.com' => 'admin',
            'sarah@example.com' => 'parent',
            'michael@example.com' => 'parent',
            'emily@example.com' => 'parent',
            'david@example.com' => 'parent',
            'lisa@example.com' => 'parent',
            'citygeneral@vms.permetheon.com' => 'hospital',
            'community@vms.permetheon.com' => 'hospital',
            'childrens@vms.permetheon.com' => 'hospital',
            'westside@vms.permetheon.com' => 'hospital',
            'downtown@vms.permetheon.com' => 'hospital',
        ];

        foreach ($expectedUsers as $email => $role) {
            $this->assertDatabaseHas('users', ['email' => $email, 'role' => $role]);
        }

        $this->assertSame(11, User::count(), 'Seeder should create exactly 11 users');
        $this->assertSame(6, Child::count(), 'Seeder should create exactly 6 children');
    }

    public function test_every_seeded_account_uses_the_advertised_demo_password(): void
    {
        User::all()->each(function (User $user): void {
            $this->assertTrue(
                Hash::check('password', $user->password),
                "Seeded user {$user->email} does not use the advertised demo password"
            );
        });
    }

    public function test_seeded_children_belong_to_their_seeder_assigned_parents(): void
    {
        $expectedChildren = [
            'Emma Johnson' => 'sarah@example.com',
            'Liam Johnson' => 'sarah@example.com',
            'Olivia Johnson' => 'sarah@example.com',
            'Noah Brown' => 'michael@example.com',
            'Ava Davis' => 'emily@example.com',
            'Ethan Davis' => 'emily@example.com',
        ];

        foreach ($expectedChildren as $name => $parentEmail) {
            $parent = User::where('email', $parentEmail)->first();
            $this->assertNotNull($parent, "Seeded parent {$parentEmail} missing");

            $this->assertDatabaseHas('children', [
                'name' => $name,
                'user_id' => $parent->id,
            ]);
        }
    }

    public function test_hospital_users_are_linked_to_their_seeded_hospitals(): void
    {
        $links = [
            'citygeneral@vms.permetheon.com' => 'CGH-001',
            'community@vms.permetheon.com' => 'CHC-002',
            'childrens@vms.permetheon.com' => 'CMC-003',
            'westside@vms.permetheon.com' => 'WSH-004',
            'downtown@vms.permetheon.com' => 'DMC-005',
        ];

        foreach ($links as $email => $code) {
            $user = User::where('email', $email)->first();
            $this->assertNotNull($user, "Seeded hospital user {$email} missing");

            $this->assertSame(
                $code,
                $user->hospital?->code,
                "Hospital user {$email} is not linked to hospital {$code}"
            );
        }
    }

    public function test_seeded_hospitals_and_vaccines_match_the_seeder_contract(): void
    {
        foreach (SeedContract::HOSPITALS as $code => $expected) {
            $this->assertDatabaseHas('hospitals', [
                'code' => $code,
                'name' => $expected['name'],
                'city' => $expected['city'],
                'total_beds' => $expected['total_beds'],
                'status' => $expected['status'],
            ]);
        }

        $this->assertSame(5, Hospital::count(), 'Seeder should create exactly 5 hospitals');

        foreach (SeedContract::VACCINES as $code => $expected) {
            $this->assertDatabaseHas('vaccines', [
                'code' => $code,
                'name' => $expected['name'],
                'doses' => $expected['doses'],
                'status' => $expected['status'],
            ]);
        }

        $this->assertSame(10, Vaccine::count(), 'Seeder should create exactly 10 vaccines');
    }

    public function test_seeded_inventory_covers_the_full_active_hospital_vaccine_matrix(): void
    {
        // VaccineInventorySeeder randomises stock values, so this is a structural contract:
        // exactly one row per active hospital × active vaccine, with valid counts.
        $this->assertSame(50, VaccineInventory::count(), '5 hospitals × 10 vaccines = 50 inventory rows');

        $pairs = VaccineInventory::all()->map(
            fn (VaccineInventory $row) => $row->hospital?->code . '|' . $row->vaccine?->code
        )->all();

        $expectedPairs = [];

        foreach (array_keys(SeedContract::HOSPITALS) as $hospitalCode) {
            foreach (array_keys(SeedContract::VACCINES) as $vaccineCode) {
                $expectedPairs[] = $hospitalCode . '|' . $vaccineCode;
            }
        }

        $this->assertSame([], array_diff($expectedPairs, $pairs), 'Missing inventory combinations');
        $this->assertSame([], array_diff($pairs, $expectedPairs), 'Inventory rows outside the contract');

        VaccineInventory::all()->each(function (VaccineInventory $row): void {
            $this->assertGreaterThanOrEqual(0, $row->capacity);
            $this->assertGreaterThanOrEqual(0, $row->available);
            $this->assertLessThanOrEqual($row->capacity, $row->available);
        });
    }

    public function test_seed_check_fails_when_an_unseeded_user_is_added(): void
    {
        User::create([
            'name' => 'Audit Leftover',
            'email' => 'parentB_audit2@test.com',
            'password' => Hash::make('whatever'),
            'role' => 'parent',
        ]);

        $this->artisan('db:seed-check')->assertExitCode(1);
    }

    public function test_seed_check_fails_when_a_child_drifts_to_the_wrong_parent(): void
    {
        $michael = User::where('email', 'michael@example.com')->firstOrFail();
        $emma = Child::where('name', 'Emma Johnson')->firstOrFail();

        $emma->update(['user_id' => $michael->id]);

        $this->artisan('db:seed-check')->assertExitCode(1);
    }

    public function test_seed_check_fails_when_a_seeded_child_is_missing(): void
    {
        Child::where('name', 'Ethan Davis')->delete();

        $this->artisan('db:seed-check')->assertExitCode(1);
    }

    public function test_seed_check_fails_when_a_seeded_user_uses_a_changed_password(): void
    {
        $lisa = User::where('email', 'lisa@example.com')->firstOrFail();
        $lisa->update(['password' => Hash::make('drifted-secret')]);

        $this->artisan('db:seed-check')->assertExitCode(1);
    }

    public function test_seed_check_fails_when_a_seeded_hospital_is_deleted(): void
    {
        Hospital::where('code', 'DMC-005')->delete();

        $this->artisan('db:seed-check')->assertExitCode(1);
    }

    public function test_seed_check_fails_when_a_seeded_vaccine_is_deleted(): void
    {
        Vaccine::where('code', 'HPV')->delete();

        $this->artisan('db:seed-check')->assertExitCode(1);
    }

    public function test_seed_check_fails_when_a_seeded_status_drifts(): void
    {
        Hospital::where('code', 'DMC-005')->update(['status' => 'inactive']);
        Vaccine::where('code', 'MMR')->update(['status' => 'inactive']);

        $this->artisan('db:seed-check')->assertExitCode(1);
    }

    public function test_seed_check_fails_when_an_extra_hospital_is_added(): void
    {
        Hospital::create([
            'name' => 'Fake Clinic',
            'code' => 'FAKE-999',
            'email' => 'fake@clinic.com',
            'phone' => '+1-555-9999',
            'address' => '1 Nowhere St',
            'city' => 'Nowhere',
            'state' => 'Nowhere',
            'contact_person' => 'Dr Fake',
            'designation' => 'Admin',
            'total_beds' => 10,
            'description' => 'Not seeded.',
            'status' => 'active',
            'rating' => 3.0,
        ]);

        $this->artisan('db:seed-check')->assertExitCode(1);
    }

    public function test_seed_check_fails_when_inventory_combinations_are_incomplete_or_unmapped(): void
    {
        // Delete a contract combination and unmap another onto a non-contract hospital.
        VaccineInventory::whereHas('vaccine', fn ($q) => $q->where('code', 'PCV'))
            ->whereHas('hospital', fn ($q) => $q->where('code', 'WSH-004'))->delete();

        $this->artisan('db:seed-check')->assertExitCode(1);
    }

    public function test_seed_check_fails_when_inventory_counts_are_invalid(): void
    {
        VaccineInventory::whereHas('vaccine', fn ($q) => $q->where('code', 'IPV'))
            ->whereHas('hospital', fn ($q) => $q->where('code', 'CGH-001'))
            ->firstOrFail()
            ->update(['available' => 500, 'capacity' => 100]);

        $this->artisan('db:seed-check')->assertExitCode(1);
    }

    // ===== db:restore-demo =====

    public function test_restore_demo_is_a_no_op_when_there_is_no_drift(): void
    {
        $this->artisan('db:restore-demo', ['--force' => true])->assertExitCode(0);

        $this->assertSame(11, User::count());
        $this->assertSame(6, Child::count());
        $this->assertSame(5, Hospital::count());
        $this->assertSame(10, Vaccine::count());
        $this->assertSame(50, VaccineInventory::count());
    }

    public function test_restore_demo_repairs_full_drift_and_passes_seed_check(): void
    {
        // Exact audit-leftover drift plus reassignment, missing child and password drift.
        $driftParent = User::create([
            'name' => 'Parent B',
            'email' => 'parentB_audit2@test.com',
            'password' => Hash::make('whatever'),
            'role' => 'parent',
        ]);
        Child::create([
            'user_id' => $driftParent->id,
            'name' => 'AuditTestChild',
            'date_of_birth' => '2023-01-01',
            'gender' => 'male',
            'relationship' => 'father',
            'status' => 'active',
        ]);

        $michael = User::where('email', 'michael@example.com')->firstOrFail();
        Child::where('name', 'Emma Johnson')->update(['user_id' => $michael->id]);
        Child::where('name', 'Ethan Davis')->delete();
        User::where('email', 'lisa@example.com')->update(['password' => Hash::make('changed-secret')]);

        $this->artisan('db:seed-check')->assertExitCode(1);

        $this->artisan('db:restore-demo', ['--force' => true])->assertExitCode(0);

        $this->artisan('db:seed-check')->assertExitCode(0);

        // Audit leftovers removed.
        $this->assertDatabaseMissing('users', ['email' => 'parentB_audit2@test.com']);
        $this->assertDatabaseMissing('children', ['name' => 'AuditTestChild']);

        // Exactly one Emma, back with Sarah, with seeded fields.
        $sarah = User::where('email', 'sarah@example.com')->firstOrFail();
        $this->assertSame(1, Child::where('name', 'Emma Johnson')->count());
        $this->assertDatabaseHas('children', [
            'name' => 'Emma Johnson',
            'user_id' => $sarah->id,
            'blood_group' => 'A+',
            'relationship' => 'mother',
        ]);

        // Missing child restored with seeded fields.
        $emily = User::where('email', 'emily@example.com')->firstOrFail();
        $this->assertDatabaseHas('children', [
            'name' => 'Ethan Davis',
            'user_id' => $emily->id,
            'blood_group' => 'O-',
            'relationship' => 'mother',
        ]);

        // Password drift repaired.
        $lisa = User::where('email', 'lisa@example.com')->firstOrFail();
        $this->assertTrue(Hash::check('password', $lisa->fresh()->password));

        // Counts back to contract.
        $this->assertSame(11, User::count());
        $this->assertSame(6, Child::count());
    }

    public function test_restore_demo_repairs_hospital_vaccine_and_inventory_drift(): void
    {
        // Delete a seeded vaccine (cascades its 5 inventory rows), add an extra
        // hospital (with an inventory row), flip two statuses, corrupt one row's
        // counts and delete another contract combination.
        Vaccine::where('code', 'HPV')->delete();

        $fake = Hospital::create([
            'name' => 'Fake Clinic',
            'code' => 'FAKE-999',
            'email' => 'fake@clinic.com',
            'phone' => '+1-555-9999',
            'address' => '1 Nowhere St',
            'city' => 'Nowhere',
            'state' => 'Nowhere',
            'contact_person' => 'Dr Fake',
            'designation' => 'Admin',
            'total_beds' => 10,
            'description' => 'Not seeded.',
            'status' => 'active',
            'rating' => 3.0,
        ]);
        $bcg = Vaccine::where('code', 'BCG')->firstOrFail();
        VaccineInventory::create([
            'hospital_id' => $fake->id,
            'vaccine_id' => $bcg->id,
            'available' => 5,
            'capacity' => 10,
            'batch_number' => 'BATCH-FAKE-1',
            'expiry_date' => now()->addYear(),
            'last_updated' => now(),
        ]);

        Hospital::where('code', 'DMC-005')->update(['status' => 'inactive']);
        Vaccine::where('code', 'MMR')->update(['status' => 'inactive']);
        VaccineInventory::whereHas('vaccine', fn ($q) => $q->where('code', 'IPV'))
            ->whereHas('hospital', fn ($q) => $q->where('code', 'CGH-001'))
            ->firstOrFail()
            ->update(['available' => 500, 'capacity' => 100]);
        VaccineInventory::whereHas('vaccine', fn ($q) => $q->where('code', 'PCV'))
            ->whereHas('hospital', fn ($q) => $q->where('code', 'WSH-004'))->delete();

        $this->artisan('db:seed-check')->assertExitCode(1);

        $this->artisan('db:restore-demo', ['--force' => true])->assertExitCode(0);

        $this->artisan('db:seed-check')->assertExitCode(0);

        // Extra hospital gone (its inventory row cascaded with it).
        $this->assertDatabaseMissing('hospitals', ['code' => 'FAKE-999']);

        // Missing vaccine recreated with seeder-identical fields.
        $this->assertDatabaseHas('vaccines', [
            'code' => 'HPV',
            'name' => 'Human Papillomavirus (HPV)',
            'doses' => 2,
            'type' => 'Recombinant',
            'manufacturer' => 'Merck & Co.',
            'status' => 'active',
        ]);

        // Statuses restored.
        $this->assertSame('active', Hospital::where('code', 'DMC-005')->value('status'));
        $this->assertSame('active', Vaccine::where('code', 'MMR')->value('status'));

        // Full matrix restored with valid counts.
        $this->assertSame(50, VaccineInventory::count());
        VaccineInventory::all()->each(function (VaccineInventory $row): void {
            $this->assertGreaterThanOrEqual(0, $row->available);
            $this->assertLessThanOrEqual($row->capacity, $row->available);
        });

        // The corrupted counts row was recreated, not patched.
        $this->assertDatabaseMissing('vaccine_inventory', ['available' => 500, 'capacity' => 100]);
    }

    public function test_restore_demo_dry_run_changes_nothing(): void
    {
        $driftParent = User::create([
            'name' => 'Drift Parent',
            'email' => 'drift_dryrun@test.com',
            'password' => Hash::make('whatever'),
            'role' => 'parent',
        ]);
        Child::where('name', 'Noah Brown')->delete();

        $usersBefore = User::count();
        $childrenBefore = Child::count();

        $this->artisan('db:restore-demo', ['--dry-run' => true, '--force' => true])->assertExitCode(0);

        $this->assertSame($usersBefore, User::count(), 'dry-run must not delete or create users');
        $this->assertSame($childrenBefore, Child::count(), 'dry-run must not delete or create children');
        $this->assertDatabaseHas('users', ['email' => $driftParent->email]);
        $this->assertDatabaseMissing('children', ['name' => 'Noah Brown']);

        // And seed-check still fails because nothing was repaired.
        $this->artisan('db:seed-check')->assertExitCode(1);
    }

    public function test_restore_demo_deletes_dependents_of_removed_drift_users(): void
    {
        $driftParent = User::create([
            'name' => 'Drift Parent',
            'email' => 'drift_cascade@test.com',
            'password' => Hash::make('whatever'),
            'role' => 'parent',
        ]);
        $child = Child::create([
            'user_id' => $driftParent->id,
            'name' => 'CascadeChild',
            'date_of_birth' => '2023-02-02',
            'gender' => 'female',
            'relationship' => 'mother',
            'status' => 'active',
        ]);

        $this->artisan('db:restore-demo', ['--force' => true])->assertExitCode(0);

        $this->assertDatabaseMissing('users', ['email' => 'drift_cascade@test.com']);
        $this->assertDatabaseMissing('children', ['name' => 'CascadeChild']);
        $this->assertDatabaseMissing('children', ['id' => $child->id]);
        $this->assertSame(11, User::count());
        $this->assertSame(6, Child::count());
    }
}
