<?php

namespace Tests\Feature;

use App\Models\{Child, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Guards the seeder contract: users and children must match what
 * UserSeeder / ChildSeeder define. Catches the exact seed drift
 * observed during the adversarial audit (leftover audit users/children).
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

    // ===== db:restore-demo =====

    public function test_restore_demo_is_a_no_op_when_there_is_no_drift(): void
    {
        $this->artisan('db:restore-demo', ['--force' => true])->assertExitCode(0);

        $this->assertSame(11, User::count());
        $this->assertSame(6, Child::count());
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
