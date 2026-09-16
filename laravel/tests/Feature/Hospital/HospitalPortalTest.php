<?php

namespace Tests\Feature\Hospital;

use Tests\TestCase;
use App\Models\{User, Child, Hospital, Vaccine, VaccineInventory, Appointment, VaccinationRecord};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class HospitalPortalTest extends TestCase
{
    use RefreshDatabase;

    private function createHospital(): Hospital
    {
        return Hospital::factory()->create(['status' => 'active']);
    }

    private function createHospitalUser(Hospital $hospital): User
    {
        return User::factory()->create([
            'role' => 'hospital',
            'hospital_id' => $hospital->id,
        ]);
    }

    private function createParent(): User
    {
        return User::factory()->create(['role' => 'parent']);
    }

    // ===== DASHBOARD =====

    public function test_hospital_can_access_dashboard(): void
    {
        $hospital = $this->createHospital();
        $user = $this->createHospitalUser($hospital);
        $response = $this->actingAs($user)->get(route('hospital.dashboard'));
        $response->assertStatus(200);
    }

    public function test_parent_cannot_access_hospital_dashboard(): void
    {
        $parent = User::factory()->create(['role' => 'parent']);
        $response = $this->actingAs($parent)->get(route('hospital.dashboard'));
        $response->assertStatus(403);
    }

    public function test_admin_cannot_access_hospital_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($admin)->get(route('hospital.dashboard'));
        $response->assertStatus(403);
    }

    public function test_dashboard_shows_only_own_hospital_data(): void
    {
        $hospitalA = $this->createHospital();
        $hospitalB = $this->createHospital();
        $userA = $this->createHospitalUser($hospitalA);

        $parent = $this->createParent();
        $child = Child::factory()->create(['user_id' => $parent->id]);
        $vaccine = Vaccine::factory()->create();

        // Create appointment for Hospital B
        Appointment::create([
            'child_id' => $child->id, 'parent_id' => $parent->id,
            'hospital_id' => $hospitalB->id, 'vaccine_id' => $vaccine->id,
            'dose' => '1st', 'appointment_date' => now(), 'appointment_time' => '10:00', 'status' => 'confirmed',
        ]);

        $response = $this->actingAs($userA)->get(route('hospital.dashboard'));
        $response->assertStatus(200);
        // Hospital A dashboard should not show Hospital B's appointments
    }

    // ===== APPOINTMENTS =====

    public function test_hospital_can_view_its_appointments(): void
    {
        $hospital = $this->createHospital();
        $user = $this->createHospitalUser($hospital);
        $response = $this->actingAs($user)->get(route('hospital.appointments.index'));
        $response->assertStatus(200);
    }

    public function test_hospital_cannot_view_other_hospitals_appointment(): void
    {
        $hospitalA = $this->createHospital();
        $hospitalB = $this->createHospital();
        $userA = $this->createHospitalUser($hospitalA);
        $parent = $this->createParent();
        $child = Child::factory()->create(['user_id' => $parent->id]);
        $vaccine = Vaccine::factory()->create();

        $appointment = Appointment::create([
            'child_id' => $child->id, 'parent_id' => $parent->id,
            'hospital_id' => $hospitalB->id, 'vaccine_id' => $vaccine->id,
            'dose' => '1st', 'appointment_date' => now()->addDays(5), 'appointment_time' => '10:00', 'status' => 'confirmed',
        ]);

        $response = $this->actingAs($userA)->get(route('hospital.appointments.show', $appointment));
        $response->assertStatus(403);
    }

    public function test_hospital_can_view_its_appointment_details(): void
    {
        $hospital = $this->createHospital();
        $user = $this->createHospitalUser($hospital);
        $parent = $this->createParent();
        $child = Child::factory()->create(['user_id' => $parent->id]);
        $vaccine = Vaccine::factory()->create();

        $appointment = Appointment::create([
            'child_id' => $child->id, 'parent_id' => $parent->id,
            'hospital_id' => $hospital->id, 'vaccine_id' => $vaccine->id,
            'dose' => '1st', 'appointment_date' => now()->addDays(5), 'appointment_time' => '10:00', 'status' => 'confirmed',
        ]);

        $response = $this->actingAs($user)->get(route('hospital.appointments.show', $appointment));
        $response->assertStatus(200);
    }

    public function test_hospital_can_confirm_appointment(): void
    {
        $hospital = $this->createHospital();
        $user = $this->createHospitalUser($hospital);
        $parent = $this->createParent();
        $child = Child::factory()->create(['user_id' => $parent->id]);
        $vaccine = Vaccine::factory()->create();

        $appointment = Appointment::create([
            'child_id' => $child->id, 'parent_id' => $parent->id,
            'hospital_id' => $hospital->id, 'vaccine_id' => $vaccine->id,
            'dose' => '1st', 'appointment_date' => now()->addDays(5), 'appointment_time' => '10:00', 'status' => 'pending',
        ]);

        $response = $this->actingAs($user)->post(route('hospital.appointments.confirm', $appointment));
        $response->assertRedirect();
        $this->assertDatabaseHas('appointments', ['id' => $appointment->id, 'status' => 'confirmed']);
    }

    public function test_hospital_can_complete_vaccination(): void
    {
        $hospital = $this->createHospital();
        $user = $this->createHospitalUser($hospital);
        $parent = $this->createParent();
        $child = Child::factory()->create(['user_id' => $parent->id]);
        $vaccine = Vaccine::factory()->create();

        $appointment = Appointment::create([
            'child_id' => $child->id, 'parent_id' => $parent->id,
            'hospital_id' => $hospital->id, 'vaccine_id' => $vaccine->id,
            'dose' => '1st', 'appointment_date' => now(), 'appointment_time' => '10:00', 'status' => 'confirmed',
        ]);

        $response = $this->actingAs($user)->post(route('hospital.appointments.complete', $appointment), [
            'dose_number' => 1,
            'administered_by' => 'Dr. Smith',
            'batch_number' => 'BATCH-001',
            'notes' => 'No adverse reactions',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('appointments', ['id' => $appointment->id, 'status' => 'completed']);
        $this->assertDatabaseHas('vaccination_records', [
            'child_id' => $child->id,
            'hospital_id' => $hospital->id,
            'vaccine_id' => $vaccine->id,
            'appointment_id' => $appointment->id,
        ]);
    }

    public function test_hospital_can_mark_no_show(): void
    {
        $hospital = $this->createHospital();
        $user = $this->createHospitalUser($hospital);
        $parent = $this->createParent();
        $child = Child::factory()->create(['user_id' => $parent->id]);
        $vaccine = Vaccine::factory()->create();

        $appointment = Appointment::create([
            'child_id' => $child->id, 'parent_id' => $parent->id,
            'hospital_id' => $hospital->id, 'vaccine_id' => $vaccine->id,
            'dose' => '1st', 'appointment_date' => now(), 'appointment_time' => '10:00', 'status' => 'confirmed',
        ]);

        $response = $this->actingAs($user)->post(route('hospital.appointments.no-show', $appointment));
        $response->assertRedirect();
        $this->assertDatabaseHas('appointments', ['id' => $appointment->id, 'status' => 'no_show']);
    }

    public function test_hospital_cannot_vaccinate_another_hospitals_appointment(): void
    {
        $hospitalA = $this->createHospital();
        $hospitalB = $this->createHospital();
        $userA = $this->createHospitalUser($hospitalA);
        $parent = $this->createParent();
        $child = Child::factory()->create(['user_id' => $parent->id]);
        $vaccine = Vaccine::factory()->create();

        $appointment = Appointment::create([
            'child_id' => $child->id, 'parent_id' => $parent->id,
            'hospital_id' => $hospitalB->id, 'vaccine_id' => $vaccine->id,
            'dose' => '1st', 'appointment_date' => now(), 'appointment_time' => '10:00', 'status' => 'confirmed',
        ]);

        $response = $this->actingAs($userA)->post(route('hospital.appointments.complete', $appointment), [
            'dose_number' => 1, 'administered_by' => 'Dr. X',
        ]);
        $response->assertStatus(403);
    }

    public function test_hospital_cannot_no_show_another_hospitals_appointment(): void
    {
        $hospitalA = $this->createHospital();
        $hospitalB = $this->createHospital();
        $userA = $this->createHospitalUser($hospitalA);
        $parent = $this->createParent();
        $child = Child::factory()->create(['user_id' => $parent->id]);
        $vaccine = Vaccine::factory()->create();

        $appointment = Appointment::create([
            'child_id' => $child->id, 'parent_id' => $parent->id,
            'hospital_id' => $hospitalB->id, 'vaccine_id' => $vaccine->id,
            'dose' => '1st', 'appointment_date' => now(), 'appointment_time' => '10:00', 'status' => 'confirmed',
        ]);

        $response = $this->actingAs($userA)->post(route('hospital.appointments.no-show', $appointment));
        $response->assertStatus(403);
    }

    public function test_invalid_complete_without_dose_number_rejected(): void
    {
        $hospital = $this->createHospital();
        $user = $this->createHospitalUser($hospital);
        $parent = $this->createParent();
        $child = Child::factory()->create(['user_id' => $parent->id]);
        $vaccine = Vaccine::factory()->create();

        $appointment = Appointment::create([
            'child_id' => $child->id, 'parent_id' => $parent->id,
            'hospital_id' => $hospital->id, 'vaccine_id' => $vaccine->id,
            'dose' => '1st', 'appointment_date' => now(), 'appointment_time' => '10:00', 'status' => 'confirmed',
        ]);

        $response = $this->actingAs($user)->post(route('hospital.appointments.complete', $appointment), [
            'dose_number' => '',
            'administered_by' => '',
        ]);
        $response->assertSessionHasErrors();
    }

    // ===== VACCINATION RECORDS =====

    public function test_hospital_can_view_its_records(): void
    {
        $hospital = $this->createHospital();
        $user = $this->createHospitalUser($hospital);
        $response = $this->actingAs($user)->get(route('hospital.vaccinations.index'));
        $response->assertStatus(200);
    }

    public function test_hospital_cannot_view_other_hospitals_records(): void
    {
        $hospitalA = $this->createHospital();
        $hospitalB = $this->createHospital();
        $userA = $this->createHospitalUser($hospitalA);
        $parent = $this->createParent();
        $child = Child::factory()->create(['user_id' => $parent->id]);
        $vaccine = Vaccine::factory()->create();

        $record = VaccinationRecord::create([
            'child_id' => $child->id, 'hospital_id' => $hospitalB->id,
            'vaccine_id' => $vaccine->id, 'dose_number' => 1, 'administered_at' => now(), 'administered_by' => 'Dr. X',
        ]);

        $response = $this->actingAs($userA)->get(route('hospital.vaccinations.show', $record));
        $response->assertStatus(403);
    }

    public function test_hospital_can_view_its_record_detail(): void
    {
        $hospital = $this->createHospital();
        $user = $this->createHospitalUser($hospital);
        $parent = $this->createParent();
        $child = Child::factory()->create(['user_id' => $parent->id]);
        $vaccine = Vaccine::factory()->create();

        $record = VaccinationRecord::create([
            'child_id' => $child->id, 'hospital_id' => $hospital->id,
            'vaccine_id' => $vaccine->id, 'dose_number' => 1, 'administered_at' => now(), 'administered_by' => 'Dr. X',
        ]);

        $response = $this->actingAs($user)->get(route('hospital.vaccinations.show', $record));
        $response->assertStatus(200);
    }

    // ===== VACCINE INVENTORY =====

    public function test_hospital_can_view_inventory(): void
    {
        $hospital = $this->createHospital();
        $user = $this->createHospitalUser($hospital);
        $response = $this->actingAs($user)->get(route('hospital.vaccines.index'));
        $response->assertStatus(200);
    }

    public function test_hospital_can_update_inventory(): void
    {
        $hospital = $this->createHospital();
        $user = $this->createHospitalUser($hospital);
        $vaccine = Vaccine::factory()->create();
        $inventory = VaccineInventory::create([
            'hospital_id' => $hospital->id, 'vaccine_id' => $vaccine->id,
            'available' => 50, 'capacity' => 100,
        ]);

        $response = $this->actingAs($user)->put(route('hospital.vaccines.update', $inventory), [
            'available' => 45, 'capacity' => 100,
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('vaccine_inventory', ['id' => $inventory->id, 'available' => 45]);
    }

    public function test_hospital_cannot_modify_other_hospitals_inventory(): void
    {
        $hospitalA = $this->createHospital();
        $hospitalB = $this->createHospital();
        $userA = $this->createHospitalUser($hospitalA);
        $vaccine = Vaccine::factory()->create();
        $inventory = VaccineInventory::create([
            'hospital_id' => $hospitalB->id, 'vaccine_id' => $vaccine->id,
            'available' => 50, 'capacity' => 100,
        ]);

        $response = $this->actingAs($userA)->put(route('hospital.vaccines.update', $inventory), [
            'available' => 0, 'capacity' => 100,
        ]);
        $response->assertStatus(403);
    }

    public function test_negative_inventory_rejected(): void
    {
        $hospital = $this->createHospital();
        $user = $this->createHospitalUser($hospital);
        $vaccine = Vaccine::factory()->create();
        $inventory = VaccineInventory::create([
            'hospital_id' => $hospital->id, 'vaccine_id' => $vaccine->id,
            'available' => 50, 'capacity' => 100,
        ]);

        $response = $this->actingAs($user)->put(route('hospital.vaccines.update', $inventory), [
            'available' => -5, 'capacity' => 100,
        ]);
        $response->assertSessionHasErrors();
    }

    // ===== PROFILE =====

    public function test_hospital_can_view_profile(): void
    {
        $hospital = $this->createHospital();
        $user = $this->createHospitalUser($hospital);
        $response = $this->actingAs($user)->get(route('hospital.profile'));
        $response->assertStatus(200);
    }

    public function test_hospital_can_update_profile(): void
    {
        $hospital = $this->createHospital();
        $user = $this->createHospitalUser($hospital);
        $response = $this->actingAs($user)->put(route('hospital.profile.update'), [
            'name' => 'Updated Contact',
            'email' => $user->email,
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Updated Contact']);
    }

    // ===== SETTINGS =====

    public function test_hospital_can_access_settings(): void
    {
        $hospital = $this->createHospital();
        $user = $this->createHospitalUser($hospital);
        $response = $this->actingAs($user)->get(route('hospital.settings'));
        $response->assertStatus(200);
    }

    public function test_hospital_can_change_password(): void
    {
        $hospital = $this->createHospital();
        $user = $this->createHospitalUser($hospital);
        $response = $this->actingAs($user)->put(route('hospital.settings.password'), [
            'current_password' => 'password',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);
        $response->assertRedirect();
    }

    public function test_hospital_settings_renders_hospital_account_block(): void
    {
        $hospital = $this->createHospital();
        $user = $this->createHospitalUser($hospital);
        $response = $this->actingAs($user)->get(route('hospital.settings'));
        $response->assertStatus(200);
        $response->assertSee('Change Password');
        $response->assertSee('Hospital Account');
        $response->assertSee($hospital->name);
        $response->assertSee($user->name);
        $response->assertSee('Hospital');
    }

    public function test_hospital_settings_password_change_persists_new_password(): void
    {
        $hospital = $this->createHospital();
        $user = $this->createHospitalUser($hospital);
        $response = $this->actingAs($user)->put(route('hospital.settings.password'), [
            'current_password' => 'password',
            'password' => 'NewSecret123',
            'password_confirmation' => 'NewSecret123',
        ]);
        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertTrue(Hash::check('NewSecret123', $user->fresh()->password));
    }

    public function test_hospital_settings_rejects_wrong_current_password(): void
    {
        $hospital = $this->createHospital();
        $user = $this->createHospitalUser($hospital);
        $response = $this->actingAs($user)->put(route('hospital.settings.password'), [
            'current_password' => 'wrongpassword',
            'password' => 'NewSecret123',
            'password_confirmation' => 'NewSecret123',
        ]);
        $response->assertSessionHasErrors('current_password');
        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }

    // ===== NOTIFICATIONS =====

    public function test_hospital_can_view_notifications(): void
    {
        $hospital = $this->createHospital();
        $user = $this->createHospitalUser($hospital);
        $response = $this->actingAs($user)->get(route('hospital.notifications'));
        $response->assertStatus(200);
    }

    public function test_hospital_cannot_view_other_users_notifications(): void
    {
        $hospitalA = $this->createHospital();
        $hospitalB = $this->createHospital();
        $userA = $this->createHospitalUser($hospitalA);
        $userB = $this->createHospitalUser($hospitalB);

        $notification = $userB->notifications()->create([
            'title' => 'Private', 'message' => 'Hospital B notification',
            'data' => ['title' => 'Private'],
        ]);

        $response = $this->actingAs($userA)->post(route('hospital.notifications.mark-read', $notification));
        $response->assertStatus(403);
    }

    // ===== CROSS-ROLE SECURITY =====

    public function test_unauthenticated_user_cannot_access_hospital_routes(): void
    {
        $response = $this->get(route('hospital.dashboard'));
        $response->assertRedirect('/login');
    }

    public function test_parent_cannot_access_hospital_operations(): void
    {
        $parent = User::factory()->create(['role' => 'parent']);
        $response = $this->actingAs($parent)->get(route('hospital.appointments.index'));
        $response->assertStatus(403);
    }

    public function test_hospital_cannot_access_parent_routes(): void
    {
        $hospital = $this->createHospital();
        $user = $this->createHospitalUser($hospital);
        $response = $this->actingAs($user)->get(route('parent.dashboard'));
        $response->assertStatus(403);
    }

    public function test_hospital_cannot_access_admin_routes(): void
    {
        $hospital = $this->createHospital();
        $user = $this->createHospitalUser($hospital);
        $response = $this->actingAs($user)->get(route('admin.dashboard'));
        $response->assertStatus(403);
    }
}
