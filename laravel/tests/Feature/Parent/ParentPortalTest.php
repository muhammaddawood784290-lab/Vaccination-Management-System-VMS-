<?php

namespace Tests\Feature\Parent;

use Tests\TestCase;
use App\Models\{User, Child, Hospital, Vaccine, Appointment, VaccinationRecord, VaccinationSchedule, ParentRequest};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class ParentPortalTest extends TestCase
{
    use RefreshDatabase;

    private function createParent(): User
    {
        return User::factory()->create(['role' => 'parent']);
    }

    private function createChild(User $parent): Child
    {
        return Child::factory()->create(['user_id' => $parent->id]);
    }

    private function createHospital(): Hospital
    {
        return Hospital::factory()->create(['status' => 'active']);
    }

    // ===== DASHBOARD =====

    public function test_parent_can_access_dashboard(): void
    {
        $parent = $this->createParent();
        $response = $this->actingAs($parent)->get(route('parent.dashboard'));
        $response->assertStatus(200);
    }

    public function test_admin_cannot_access_parent_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($admin)->get(route('parent.dashboard'));
        $response->assertStatus(403);
    }

    public function test_hospital_cannot_access_parent_dashboard(): void
    {
        $hospital = User::factory()->create(['role' => 'hospital']);
        $response = $this->actingAs($hospital)->get(route('parent.dashboard'));
        $response->assertStatus(403);
    }

    public function test_dashboard_works_with_no_children(): void
    {
        $parent = $this->createParent();
        $response = $this->actingAs($parent)->get(route('parent.dashboard'));
        $response->assertStatus(200);
    }

    public function test_dashboard_shows_children_data(): void
    {
        $parent = $this->createParent();
        $child = $this->createChild($parent);
        $response = $this->actingAs($parent)->get(route('parent.dashboard'));
        $response->assertStatus(200);
        $response->assertSee($child->name);
    }

    // ===== CHILDREN =====

    public function test_parent_can_view_own_children(): void
    {
        $parent = $this->createParent();
        $child = $this->createChild($parent);
        $response = $this->actingAs($parent)->get(route('parent.children.index'));
        $response->assertStatus(200);
        $response->assertSee($child->name);
    }

    public function test_parent_can_add_child(): void
    {
        $parent = $this->createParent();
        $response = $this->actingAs($parent)->post(route('parent.children.store'), [
            'name' => 'Test Child',
            'date_of_birth' => '2020-01-15',
            'gender' => 'male',
            'relationship' => 'son',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('children', ['name' => 'Test Child', 'user_id' => $parent->id]);
    }

    public function test_parent_can_update_own_child(): void
    {
        $parent = $this->createParent();
        $child = $this->createChild($parent);
        $response = $this->actingAs($parent)->put(route('parent.children.update', $child), [
            'name' => 'Updated Name',
            'date_of_birth' => $child->date_of_birth->format('Y-m-d'),
            'gender' => $child->gender,
            'relationship' => $child->relationship,
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('children', ['id' => $child->id, 'name' => 'Updated Name']);
    }

    public function test_parent_cannot_access_other_parents_child(): void
    {
        $parentA = $this->createParent();
        $parentB = $this->createParent();
        $childB = $this->createChild($parentB);

        $response = $this->actingAs($parentA)->get(route('parent.children.show', $childB));
        $response->assertStatus(403);
    }

    public function test_parent_cannot_modify_other_parents_child(): void
    {
        $parentA = $this->createParent();
        $parentB = $this->createParent();
        $childB = $this->createChild($parentB);

        $response = $this->actingAs($parentA)->put(route('parent.children.update', $childB), [
            'name' => 'Hacked Name',
            'date_of_birth' => $childB->date_of_birth->format('Y-m-d'),
            'gender' => $childB->gender,
            'relationship' => $childB->relationship,
        ]);
        $response->assertStatus(403);
        $this->assertDatabaseHas('children', ['id' => $childB->id, 'name' => $parentB->children->first()->name]);
    }

    public function test_parent_can_delete_own_child(): void
    {
        $parent = $this->createParent();
        $child = $this->createChild($parent);
        $response = $this->actingAs($parent)->delete(route('parent.children.destroy', $child));
        $response->assertRedirect();
        $this->assertDatabaseMissing('children', ['id' => $child->id]);
    }

    public function test_child_validation_rejects_invalid_data(): void
    {
        $parent = $this->createParent();
        $response = $this->actingAs($parent)->post(route('parent.children.store'), [
            'name' => '',
            'date_of_birth' => '',
            'gender' => 'invalid',
            'relationship' => '',
        ]);
        $response->assertSessionHasErrors();
    }

    // ===== HOSPITALS =====

    public function test_parent_can_view_hospitals(): void
    {
        $parent = $this->createParent();
        $hospital = $this->createHospital();
        $response = $this->actingAs($parent)->get(route('parent.hospitals.index'));
        $response->assertStatus(200);
        $response->assertSee($hospital->name);
    }

    public function test_parent_can_view_hospital_detail(): void
    {
        $parent = $this->createParent();
        $hospital = $this->createHospital();
        $response = $this->actingAs($parent)->get(route('parent.hospitals.show', $hospital));
        $response->assertStatus(200);
    }

    // ===== APPOINTMENTS =====

    public function test_parent_can_view_own_appointments(): void
    {
        $parent = $this->createParent();
        $response = $this->actingAs($parent)->get(route('parent.appointments.index'));
        $response->assertStatus(200);
    }

    public function test_parent_can_book_appointment(): void
    {
        $parent = $this->createParent();
        $child = $this->createChild($parent);
        $hospital = $this->createHospital();
        $vaccine = Vaccine::factory()->create(['status' => 'active']);

        $response = $this->actingAs($parent)->post(route('parent.appointments.store'), [
            'child_id' => $child->id,
            'hospital_id' => $hospital->id,
            'vaccine_id' => $vaccine->id,
            'dose' => '1st Dose',
            'appointment_date' => now()->addDays(5)->format('Y-m-d'),
            'appointment_time' => '10:00',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('appointments', ['parent_id' => $parent->id, 'child_id' => $child->id, 'status' => 'pending']);
    }

    public function test_parent_cannot_book_appointment_for_other_parents_child(): void
    {
        $parentA = $this->createParent();
        $parentB = $this->createParent();
        $childB = $this->createChild($parentB);
        $hospital = $this->createHospital();
        $vaccine = Vaccine::factory()->create(['status' => 'active']);

        $response = $this->actingAs($parentA)->post(route('parent.appointments.store'), [
            'child_id' => $childB->id,
            'hospital_id' => $hospital->id,
            'vaccine_id' => $vaccine->id,
            'dose' => '1st Dose',
            'appointment_date' => now()->addDays(5)->format('Y-m-d'),
            'appointment_time' => '10:00',
        ]);
        $response->assertStatus(403);
    }

    public function test_parent_cannot_view_other_parents_appointment(): void
    {
        $parentA = $this->createParent();
        $parentB = $this->createParent();
        $childB = $this->createChild($parentB);
        $hospital = $this->createHospital();
        $vaccine = Vaccine::factory()->create();
        $appointment = Appointment::create([
            'child_id' => $childB->id,
            'parent_id' => $parentB->id,
            'hospital_id' => $hospital->id,
            'vaccine_id' => $vaccine->id,
            'dose' => '1st Dose',
            'appointment_date' => now()->addDays(5),
            'appointment_time' => '10:00',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($parentA)->get(route('parent.appointments.show', $appointment));
        $response->assertStatus(403);
    }

    public function test_parent_can_cancel_own_appointment(): void
    {
        $parent = $this->createParent();
        $child = $this->createChild($parent);
        $hospital = $this->createHospital();
        $vaccine = Vaccine::factory()->create();
        $appointment = Appointment::create([
            'child_id' => $child->id,
            'parent_id' => $parent->id,
            'hospital_id' => $hospital->id,
            'vaccine_id' => $vaccine->id,
            'dose' => '1st Dose',
            'appointment_date' => now()->addDays(5),
            'appointment_time' => '10:00',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($parent)->post(route('parent.appointments.cancel', $appointment));
        $response->assertRedirect();
        $this->assertDatabaseHas('appointments', ['id' => $appointment->id, 'status' => 'cancelled']);
    }

    public function test_appointment_validation_rejects_invalid_data(): void
    {
        $parent = $this->createParent();
        $response = $this->actingAs($parent)->post(route('parent.appointments.store'), [
            'child_id' => '',
            'hospital_id' => '',
            'vaccine_id' => '',
        ]);
        $response->assertSessionHasErrors();
    }

    // ===== VACCINATIONS =====

    public function test_parent_can_view_vaccination_schedule(): void
    {
        $parent = $this->createParent();
        $response = $this->actingAs($parent)->get(route('parent.vaccinations.schedule'));
        $response->assertStatus(200);
    }

    public function test_parent_can_view_vaccination_history(): void
    {
        $parent = $this->createParent();
        $response = $this->actingAs($parent)->get(route('parent.vaccinations.history'));
        $response->assertStatus(200);
    }

    public function test_parent_can_view_own_vaccination_record(): void
    {
        $parent = $this->createParent();
        $child = $this->createChild($parent);
        $hospital = $this->createHospital();
        $vaccine = Vaccine::factory()->create();
        $record = VaccinationRecord::create([
            'child_id' => $child->id,
            'hospital_id' => $hospital->id,
            'vaccine_id' => $vaccine->id,
            'dose_number' => 1,
            'administered_at' => now(),
        ]);

        $response = $this->actingAs($parent)->get(route('parent.vaccinations.show', $record));
        $response->assertStatus(200);
    }

    public function test_parent_cannot_view_other_parents_vaccination_record(): void
    {
        $parentA = $this->createParent();
        $parentB = $this->createParent();
        $childB = $this->createChild($parentB);
        $hospital = $this->createHospital();
        $vaccine = Vaccine::factory()->create();
        $record = VaccinationRecord::create([
            'child_id' => $childB->id,
            'hospital_id' => $hospital->id,
            'vaccine_id' => $vaccine->id,
            'dose_number' => 1,
            'administered_at' => now(),
        ]);

        $response = $this->actingAs($parentA)->get(route('parent.vaccinations.show', $record));
        $response->assertStatus(403);
    }

    public function test_schedule_only_shows_own_children(): void
    {
        // Deterministic names: assertDontSee is a substring check, and random faker
        // names can collide (e.g. child "Gus" inside parent "Anabel Gusikowski"),
        // which made this test flaky (~0.07%). No production scoping issue exists.
        $parentA = User::factory()->create(['role' => 'parent', 'name' => 'Alice Parent']);
        $parentB = User::factory()->create(['role' => 'parent', 'name' => 'Bob Parent']);

        // Positive control: Parent A has a real schedule so we prove A's data renders...
        $childA = Child::factory()->create(['user_id' => $parentA->id, 'name' => 'Alpha Child']);
        // ...while Parent B's child must never appear.
        $childB = Child::factory()->create(['user_id' => $parentB->id, 'name' => 'Zeta Child']);

        $vaccine = Vaccine::factory()->create();
        VaccinationSchedule::create(['child_id' => $childA->id, 'vaccine_id' => $vaccine->id, 'dose_number' => 1, 'target_age' => '6 months', 'due_date' => now()->addDays(10), 'status' => 'due']);
        VaccinationSchedule::create(['child_id' => $childB->id, 'vaccine_id' => $vaccine->id, 'dose_number' => 1, 'target_age' => '6 months', 'due_date' => now()->addDays(30), 'status' => 'due']);

        $response = $this->actingAs($parentA)->get(route('parent.vaccinations.schedule'));
        $response->assertStatus(200);
        $response->assertSee($childA->name);
        $response->assertDontSee($childB->name);
    }

    // ===== NOTIFICATIONS =====

    public function test_parent_can_view_notifications(): void
    {
        $parent = $this->createParent();
        $response = $this->actingAs($parent)->get(route('parent.notifications'));
        $response->assertStatus(200);
    }

    public function test_parent_can_mark_notification_read(): void
    {
        $parent = $this->createParent();
        $notification = $parent->notifications()->create([
            'title' => 'Test',
            'message' => 'Test notification',
            'data' => ['title' => 'Test', 'message' => 'Test notification'],
        ]);

        $response = $this->actingAs($parent)->post(route('parent.notifications.mark-read', $notification));
        $response->assertRedirect();
    }

    public function test_parent_cannot_mark_other_users_notification(): void
    {
        $parentA = $this->createParent();
        $parentB = $this->createParent();
        $notification = $parentB->notifications()->create([
            'title' => 'Test',
            'message' => 'Test notification',
            'data' => ['title' => 'Test', 'message' => 'Test'],
        ]);

        $response = $this->actingAs($parentA)->post(route('parent.notifications.mark-read', $notification));
        $response->assertStatus(403);
    }

    // ===== PROFILE =====

    public function test_parent_can_view_profile(): void
    {
        $parent = $this->createParent();
        $response = $this->actingAs($parent)->get(route('parent.profile'));
        $response->assertStatus(200);
    }

    public function test_parent_can_update_profile(): void
    {
        $parent = $this->createParent();
        $response = $this->actingAs($parent)->put(route('parent.profile.update'), [
            'name' => 'Updated Parent',
            'email' => $parent->email,
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['id' => $parent->id, 'name' => 'Updated Parent']);
    }

    public function test_parent_can_change_password(): void
    {
        $parent = $this->createParent();
        $response = $this->actingAs($parent)->put(route('parent.profile.password'), [
            'current_password' => 'password',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);
        $response->assertRedirect();
    }

    // ===== SETTINGS =====

    public function test_parent_can_access_settings(): void
    {
        $parent = $this->createParent();
        $response = $this->actingAs($parent)->get(route('parent.settings'));
        $response->assertStatus(200);
    }

    public function test_parent_can_change_password_via_settings(): void
    {
        $parent = $this->createParent();
        $response = $this->actingAs($parent)->put(route('parent.settings.password'), [
            'current_password' => 'password',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);
        $response->assertRedirect();
    }

    public function test_parent_settings_renders_account_information_block(): void
    {
        $parent = $this->createParent();
        $response = $this->actingAs($parent)->get(route('parent.settings'));
        $response->assertStatus(200);
        $response->assertSee('Change Password');
        $response->assertSee('Account Information');
        $response->assertSee($parent->name);
        $response->assertSee($parent->email);
        $response->assertSee('Parent');
        $response->assertSee($parent->created_at->format('M d, Y'));
    }

    public function test_parent_settings_password_change_persists_new_password(): void
    {
        $parent = $this->createParent();
        $response = $this->actingAs($parent)->put(route('parent.settings.password'), [
            'current_password' => 'password',
            'password' => 'NewSecret123',
            'password_confirmation' => 'NewSecret123',
        ]);
        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertTrue(Hash::check('NewSecret123', $parent->fresh()->password));
    }

    public function test_parent_settings_rejects_wrong_current_password(): void
    {
        $parent = $this->createParent();
        $response = $this->actingAs($parent)->put(route('parent.settings.password'), [
            'current_password' => 'wrongpassword',
            'password' => 'NewSecret123',
            'password_confirmation' => 'NewSecret123',
        ]);
        $response->assertSessionHasErrors('current_password');
        $this->assertTrue(Hash::check('password', $parent->fresh()->password));
    }

    // ===== SECURITY =====

    public function test_unauthenticated_user_cannot_access_parent_routes(): void
    {
        $response = $this->get(route('parent.dashboard'));
        $response->assertRedirect('/login');
    }

    public function test_admin_cannot_access_parent_operations(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($admin)->get(route('parent.children.index'));
        $response->assertStatus(403);
    }

    public function test_hospital_cannot_access_parent_operations(): void
    {
        $hospital = User::factory()->create(['role' => 'hospital']);
        $response = $this->actingAs($hospital)->get(route('parent.children.index'));
        $response->assertStatus(403);
    }

    public function test_parent_cannot_access_admin_operations(): void
    {
        $parent = $this->createParent();
        $response = $this->actingAs($parent)->get(route('admin.dashboard'));
        $response->assertStatus(403);
    }

    public function test_parent_cannot_access_hospital_operations(): void
    {
        $parent = $this->createParent();
        $response = $this->actingAs($parent)->get(route('hospital.dashboard'));
        $response->assertStatus(403);
    }

    // ===== WORKFLOW: Full parent journey =====

    public function test_full_parent_journey(): void
    {
        $parent = $this->createParent();
        $hospital = $this->createHospital();
        $vaccine = Vaccine::factory()->create(['status' => 'active']);

        // 1. Add child
        $this->actingAs($parent)->post(route('parent.children.store'), [
            'name' => 'Baby Ahmed',
            'date_of_birth' => '2024-01-15',
            'gender' => 'male',
            'relationship' => 'son',
        ]);
        $child = Child::where('user_id', $parent->id)->first();
        $this->assertNotNull($child);

        // 2. Book appointment
        $this->actingAs($parent)->post(route('parent.appointments.store'), [
            'child_id' => $child->id,
            'hospital_id' => $hospital->id,
            'vaccine_id' => $vaccine->id,
            'dose' => '1st Dose',
            'appointment_date' => now()->addDays(5)->format('Y-m-d'),
            'appointment_time' => '10:00',
        ]);
        $appointment = Appointment::where('parent_id', $parent->id)->first();
        $this->assertNotNull($appointment);
        $this->assertEquals('pending', $appointment->status);

        // 3. View appointment
        $response = $this->actingAs($parent)->get(route('parent.appointments.show', $appointment));
        $response->assertStatus(200);

        // 4. View schedule
        $response = $this->actingAs($parent)->get(route('parent.vaccinations.schedule'));
        $response->assertStatus(200);

        // 5. View history
        $response = $this->actingAs($parent)->get(route('parent.vaccinations.history'));
        $response->assertStatus(200);

        // 6. View hospitals
        $response = $this->actingAs($parent)->get(route('parent.hospitals.index'));
        $response->assertStatus(200);
    }
}
