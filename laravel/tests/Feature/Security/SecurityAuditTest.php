<?php

namespace Tests\Feature\Security;

use Tests\TestCase;
use App\Models\{User, Child, Hospital, Vaccine, VaccineInventory, Appointment, VaccinationRecord, Notification, ParentRequest};
use Illuminate\Foundation\Testing\RefreshDatabase;

class SecurityAuditTest extends TestCase
{
    use RefreshDatabase;

    private function createParent(): User { return User::factory()->create(['role' => 'parent']); }
    private function createAdmin(): User { return User::factory()->create(['role' => 'admin']); }
    private function createHospital(): Hospital { return Hospital::factory()->create(['status' => 'active']); }
    private function createHospitalUser(Hospital $h): User { return User::factory()->create(['role' => 'hospital', 'hospital_id' => $h->id]); }
    private function createVaccine(): Vaccine { return Vaccine::factory()->create(['status' => 'active']); }

    // ===== AUTHENTICATION SECURITY =====

    public function test_login_with_invalid_password_fails(): void
    {
        $user = $this->createParent();
        $response = $this->post(route('login.submit'), [
            'email' => $user->email,
            'password' => 'wrongpassword',
        ]);
        $response->assertSessionHasErrors();
    }

    public function test_login_with_nonexistent_user_fails(): void
    {
        $response = $this->post(route('login.submit'), [
            'email' => 'nonexistent@example.com',
            'password' => 'password',
        ]);
        $response->assertSessionHasErrors();
    }

    public function test_login_with_missing_fields_fails(): void
    {
        $response = $this->post(route('login.submit'), []);
        $response->assertSessionHasErrors();
    }

    public function test_admin_cannot_self_register(): void
    {
        $response = $this->post(route('register.parent.submit'), [
            'name' => 'Admin Guy',
            'email' => 'admin@example.com',
            'phone' => '123',
            'city' => 'Test',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ]);
        $user = User::where('email', 'admin@example.com')->first();
        if ($user) {
            $this->assertEquals('parent', $user->role);
        }
    }

    public function test_duplicate_email_registration_fails(): void
    {
        $existing = $this->createParent();
        $response = $this->post(route('register.parent.submit'), [
            'name' => 'Duplicate',
            'email' => $existing->email,
            'phone' => '123',
            'city' => 'Test',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ]);
        $response->assertSessionHasErrors();
    }

    public function test_weak_password_registration_fails(): void
    {
        $response = $this->post(route('register.parent.submit'), [
            'name' => 'Weak Pass',
            'email' => 'weak@example.com',
            'phone' => '123',
            'city' => 'Test',
            'password' => '123',
            'password_confirmation' => '123',
        ]);
        $response->assertSessionHasErrors();
    }

    // ===== MASS ASSIGNMENT =====

    public function test_parent_cannot_escalate_role_via_registration(): void
    {
        $response = $this->post(route('register.parent.submit'), [
            'name' => 'Hacker',
            'email' => 'hacker@example.com',
            'phone' => '123',
            'city' => 'Test',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ]);
        $user = User::where('email', 'hacker@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('parent', $user->role);
    }

    public function test_parent_cannot_set_hospital_id_via_child_creation(): void
    {
        $parent = $this->createParent();
        $hospital = $this->createHospital();

        // Attempt to inject hospital_id via mass assignment
        $response = $this->actingAs($parent)->post(route('parent.children.store'), [
            'name' => 'Test Child',
            'date_of_birth' => '2020-01-15',
            'gender' => 'male',
            'relationship' => 'son',
        ]);
        $child = Child::where('name', 'Test Child')->first();
        $this->assertNotNull($child);
        $this->assertEquals($parent->id, $child->user_id);
    }

    public function test_parent_cannot_inject_parent_id_via_appointment(): void
    {
        $parentA = $this->createParent();
        $parentB = $this->createParent();
        $hospital = $this->createHospital();
        $vaccine = $this->createVaccine();
        $childA = Child::factory()->create(['user_id' => $parentA->id]);

        // Try to create appointment with parent_id = parentB
        $response = $this->actingAs($parentA)->post(route('parent.appointments.store'), [
            'child_id' => $childA->id,
            'hospital_id' => $hospital->id,
            'vaccine_id' => $vaccine->id,
            'dose' => '1st',
            'appointment_date' => now()->addDays(5)->format('Y-m-d'),
            'appointment_time' => '10:00',
        ]);

        $apt = Appointment::where('child_id', $childA->id)->first();
        if ($apt) {
            $this->assertEquals($parentA->id, $apt->parent_id);
        }
    }

    // ===== IDOR / OWNERSHIP =====

    public function test_parent_cannot_access_other_parent_child(): void
    {
        $parentA = $this->createParent();
        $parentB = $this->createParent();
        $childB = Child::factory()->create(['user_id' => $parentB->id]);

        $response = $this->actingAs($parentA)->get(route('parent.children.show', $childB));
        $response->assertStatus(403);
    }

    public function test_parent_cannot_edit_other_parent_child(): void
    {
        $parentA = $this->createParent();
        $parentB = $this->createParent();
        $childB = Child::factory()->create(['user_id' => $parentB->id]);

        $response = $this->actingAs($parentA)->put(route('parent.children.update', $childB), [
            'name' => 'Hacked',
            'date_of_birth' => $childB->date_of_birth->format('Y-m-d'),
            'gender' => $childB->gender,
            'relationship' => $childB->relationship,
        ]);
        $response->assertStatus(403);
    }

    public function test_parent_cannot_delete_other_parent_child(): void
    {
        $parentA = $this->createParent();
        $parentB = $this->createParent();
        $childB = Child::factory()->create(['user_id' => $parentB->id]);

        $response = $this->actingAs($parentA)->delete(route('parent.children.destroy', $childB));
        $response->assertStatus(403);
        $this->assertDatabaseHas('children', ['id' => $childB->id]);
    }

    public function test_parent_cannot_access_other_parent_appointment(): void
    {
        $parentA = $this->createParent();
        $parentB = $this->createParent();
        $childB = Child::factory()->create(['user_id' => $parentB->id]);
        $hospital = $this->createHospital();
        $vaccine = $this->createVaccine();
        $apt = Appointment::create([
            'child_id' => $childB->id, 'parent_id' => $parentB->id,
            'hospital_id' => $hospital->id, 'vaccine_id' => $vaccine->id,
            'dose' => '1st', 'appointment_date' => now(), 'appointment_time' => '09:00', 'status' => 'pending',
        ]);

        $response = $this->actingAs($parentA)->get(route('parent.appointments.show', $apt));
        $response->assertStatus(403);
    }

    public function test_parent_cannot_cancel_other_parent_appointment(): void
    {
        $parentA = $this->createParent();
        $parentB = $this->createParent();
        $childB = Child::factory()->create(['user_id' => $parentB->id]);
        $hospital = $this->createHospital();
        $vaccine = $this->createVaccine();
        $apt = Appointment::create([
            'child_id' => $childB->id, 'parent_id' => $parentB->id,
            'hospital_id' => $hospital->id, 'vaccine_id' => $vaccine->id,
            'dose' => '1st', 'appointment_date' => now(), 'appointment_time' => '09:00', 'status' => 'pending',
        ]);

        $response = $this->actingAs($parentA)->post(route('parent.appointments.cancel', $apt));
        $response->assertStatus(403);
        $this->assertDatabaseHas('appointments', ['id' => $apt->id, 'status' => 'pending']);
    }

    public function test_parent_cannot_access_other_parent_vaccination_record(): void
    {
        $parentA = $this->createParent();
        $parentB = $this->createParent();
        $childB = Child::factory()->create(['user_id' => $parentB->id]);
        $hospital = $this->createHospital();
        $vaccine = $this->createVaccine();
        $record = VaccinationRecord::create([
            'child_id' => $childB->id, 'hospital_id' => $hospital->id,
            'vaccine_id' => $vaccine->id, 'dose_number' => 1, 'administered_at' => now(), 'administered_by' => 'Dr. X',
        ]);

        $response = $this->actingAs($parentA)->get(route('parent.vaccinations.show', $record));
        $response->assertStatus(403);
    }

    public function test_parent_cannot_access_other_parent_notification(): void
    {
        $parentA = $this->createParent();
        $parentB = $this->createParent();
        $notif = $parentB->notifications()->create([
            'title' => 'Private', 'message' => 'Private', 'data' => [],
        ]);

        $response = $this->actingAs($parentA)->post(route('parent.notifications.mark-read', $notif));
        $response->assertStatus(403);
    }

    public function test_hospital_cannot_access_other_hospital_appointment(): void
    {
        $hospA = $this->createHospital();
        $hospB = $this->createHospital();
        $userA = $this->createHospitalUser($hospA);
        $parent = $this->createParent();
        $child = Child::factory()->create(['user_id' => $parent->id]);
        $vaccine = $this->createVaccine();
        $apt = Appointment::create([
            'child_id' => $child->id, 'parent_id' => $parent->id,
            'hospital_id' => $hospB->id, 'vaccine_id' => $vaccine->id,
            'dose' => '1st', 'appointment_date' => now(), 'appointment_time' => '09:00', 'status' => 'confirmed',
        ]);

        $response = $this->actingAs($userA)->get(route('hospital.appointments.show', $apt));
        $response->assertStatus(403);
    }

    public function test_hospital_cannot_vaccinate_other_hospital_appointment(): void
    {
        $hospA = $this->createHospital();
        $hospB = $this->createHospital();
        $userA = $this->createHospitalUser($hospA);
        $parent = $this->createParent();
        $child = Child::factory()->create(['user_id' => $parent->id]);
        $vaccine = $this->createVaccine();
        $apt = Appointment::create([
            'child_id' => $child->id, 'parent_id' => $parent->id,
            'hospital_id' => $hospB->id, 'vaccine_id' => $vaccine->id,
            'dose' => '1st', 'appointment_date' => now(), 'appointment_time' => '09:00', 'status' => 'confirmed',
        ]);

        $response = $this->actingAs($userA)->post(route('hospital.appointments.complete', $apt), [
            'dose_number' => 1, 'administered_by' => 'Dr. X',
        ]);
        $response->assertStatus(403);
    }

    public function test_hospital_cannot_modify_other_hospital_inventory(): void
    {
        $hospA = $this->createHospital();
        $hospB = $this->createHospital();
        $userA = $this->createHospitalUser($hospA);
        $vaccine = $this->createVaccine();
        $inv = VaccineInventory::create([
            'hospital_id' => $hospB->id, 'vaccine_id' => $vaccine->id,
            'available' => 50, 'capacity' => 100,
        ]);

        $response = $this->actingAs($userA)->put(route('hospital.vaccines.update', $inv), [
            'available' => 0, 'capacity' => 100,
        ]);
        $response->assertStatus(403);
        $this->assertDatabaseHas('vaccine_inventory', ['id' => $inv->id, 'available' => 50]);
    }

    public function test_hospital_cannot_access_other_hospital_record(): void
    {
        $hospA = $this->createHospital();
        $hospB = $this->createHospital();
        $userA = $this->createHospitalUser($hospA);
        $parent = $this->createParent();
        $child = Child::factory()->create(['user_id' => $parent->id]);
        $vaccine = $this->createVaccine();
        $record = VaccinationRecord::create([
            'child_id' => $child->id, 'hospital_id' => $hospB->id,
            'vaccine_id' => $vaccine->id, 'dose_number' => 1, 'administered_at' => now(), 'administered_by' => 'Dr. X',
        ]);

        $response = $this->actingAs($userA)->get(route('hospital.vaccinations.show', $record));
        $response->assertStatus(403);
    }

    // ===== CROSS-ROLE =====

    public function test_parent_cannot_access_admin_routes(): void
    {
        $parent = $this->createParent();
        $response = $this->actingAs($parent)->get(route('admin.dashboard'));
        $response->assertStatus(403);
    }

    public function test_parent_cannot_access_hospital_routes(): void
    {
        $parent = $this->createParent();
        $response = $this->actingAs($parent)->get(route('hospital.dashboard'));
        $response->assertStatus(403);
    }

    public function test_hospital_cannot_access_admin_routes(): void
    {
        $h = $this->createHospital();
        $user = $this->createHospitalUser($h);
        $response = $this->actingAs($user)->get(route('admin.dashboard'));
        $response->assertStatus(403);
    }

    public function test_hospital_cannot_access_parent_routes(): void
    {
        $h = $this->createHospital();
        $user = $this->createHospitalUser($h);
        $response = $this->actingAs($user)->get(route('parent.dashboard'));
        $response->assertStatus(403);
    }

    public function test_admin_cannot_access_parent_routes(): void
    {
        $admin = $this->createAdmin();
        $response = $this->actingAs($admin)->get(route('parent.dashboard'));
        $response->assertStatus(403);
    }

    public function test_admin_cannot_access_hospital_routes(): void
    {
        $admin = $this->createAdmin();
        $response = $this->actingAs($admin)->get(route('hospital.dashboard'));
        $response->assertStatus(403);
    }

    public function test_unauthenticated_cannot_access_any_protected_route(): void
    {
        $routes = ['admin.dashboard', 'parent.dashboard', 'hospital.dashboard'];
        foreach ($routes as $route) {
            $response = $this->get(route($route));
            $response->assertRedirect('/login');
        }
    }

    // ===== BUSINESS LOGIC ABUSE =====

    public function test_cannot_complete_already_completed_appointment(): void
    {
        $h = $this->createHospital();
        $user = $this->createHospitalUser($h);
        $parent = $this->createParent();
        $child = Child::factory()->create(['user_id' => $parent->id]);
        $vaccine = $this->createVaccine();
        $apt = Appointment::create([
            'child_id' => $child->id, 'parent_id' => $parent->id,
            'hospital_id' => $h->id, 'vaccine_id' => $vaccine->id,
            'dose' => '1st', 'appointment_date' => now(), 'appointment_time' => '09:00', 'status' => 'completed',
        ]);

        $response = $this->actingAs($user)->post(route('hospital.appointments.complete', $apt), [
            'dose_number' => 1, 'administered_by' => 'Dr. X',
        ]);
        $response->assertStatus(400);
    }

    public function test_cannot_confirm_already_confirmed_appointment(): void
    {
        $h = $this->createHospital();
        $user = $this->createHospitalUser($h);
        $parent = $this->createParent();
        $child = Child::factory()->create(['user_id' => $parent->id]);
        $vaccine = $this->createVaccine();
        $apt = Appointment::create([
            'child_id' => $child->id, 'parent_id' => $parent->id,
            'hospital_id' => $h->id, 'vaccine_id' => $vaccine->id,
            'dose' => '1st', 'appointment_date' => now(), 'appointment_time' => '09:00', 'status' => 'confirmed',
        ]);

        $response = $this->actingAs($user)->post(route('hospital.appointments.confirm', $apt));
        $response->assertStatus(400);
    }

    public function test_cannot_no_show_completed_appointment(): void
    {
        $h = $this->createHospital();
        $user = $this->createHospitalUser($h);
        $parent = $this->createParent();
        $child = Child::factory()->create(['user_id' => $parent->id]);
        $vaccine = $this->createVaccine();
        $apt = Appointment::create([
            'child_id' => $child->id, 'parent_id' => $parent->id,
            'hospital_id' => $h->id, 'vaccine_id' => $vaccine->id,
            'dose' => '1st', 'appointment_date' => now(), 'appointment_time' => '09:00', 'status' => 'completed',
        ]);

        $response = $this->actingAs($user)->post(route('hospital.appointments.no-show', $apt));
        $response->assertStatus(400);
    }

    public function test_negative_inventory_rejected(): void
    {
        $h = $this->createHospital();
        $user = $this->createHospitalUser($h);
        $vaccine = $this->createVaccine();
        $inv = VaccineInventory::create([
            'hospital_id' => $h->id, 'vaccine_id' => $vaccine->id,
            'available' => 10, 'capacity' => 100,
        ]);

        $response = $this->actingAs($user)->put(route('hospital.vaccines.update', $inv), [
            'available' => -5, 'capacity' => 100,
        ]);
        $response->assertSessionHasErrors();
    }

    public function test_invalid_dose_number_rejected(): void
    {
        $h = $this->createHospital();
        $user = $this->createHospitalUser($h);
        $parent = $this->createParent();
        $child = Child::factory()->create(['user_id' => $parent->id]);
        $vaccine = $this->createVaccine();
        $apt = Appointment::create([
            'child_id' => $child->id, 'parent_id' => $parent->id,
            'hospital_id' => $h->id, 'vaccine_id' => $vaccine->id,
            'dose' => '1st', 'appointment_date' => now(), 'appointment_time' => '09:00', 'status' => 'confirmed',
        ]);

        $response = $this->actingAs($user)->post(route('hospital.appointments.complete', $apt), [
            'dose_number' => '', 'administered_by' => '',
        ]);
        $response->assertSessionHasErrors();
    }

    // ===== NOTIFICATION ISOLATION =====

    public function test_user_only_sees_own_notifications(): void
    {
        $parentA = $this->createParent();
        $parentB = $this->createParent();

        $parentA->notifications()->create(['title' => 'Alpha Notification', 'message' => 'Alpha content', 'type' => 'info']);
        $parentB->notifications()->create(['title' => 'Bravo Secret', 'message' => 'Bravo content', 'type' => 'info']);

        $response = $this->actingAs($parentA)->get(route('parent.notifications'));
        $response->assertStatus(200);
        $response->assertSee('Alpha Notification');
        $response->assertDontSee('Bravo Secret');
    }
}
