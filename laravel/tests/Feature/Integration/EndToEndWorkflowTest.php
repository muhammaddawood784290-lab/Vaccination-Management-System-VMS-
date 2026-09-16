<?php

namespace Tests\Feature\Integration;

use Tests\TestCase;
use App\Models\{User, Child, Hospital, Vaccine, VaccineInventory, Appointment, VaccinationRecord, Notification, ParentRequest};
use Illuminate\Foundation\Testing\RefreshDatabase;

class EndToEndWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private function createParent(): User { return User::factory()->create(['role' => 'parent']); }
    private function createAdmin(): User { return User::factory()->create(['role' => 'admin']); }
    private function createHospital(): Hospital { return Hospital::factory()->create(['status' => 'active']); }
    private function createHospitalUser(Hospital $hospital): User { return User::factory()->create(['role' => 'hospital', 'hospital_id' => $hospital->id]); }
    private function createVaccine(): Vaccine { return Vaccine::factory()->create(['status' => 'active']); }

    // ===== FULL PARENT → HOSPITAL JOURNEY =====

    public function test_complete_parent_to_vaccination_journey(): void
    {
        // Setup
        $parent = $this->createParent();
        $admin = $this->createAdmin();
        $hospital = $this->createHospital();
        $hospitalUser = $this->createHospitalUser($hospital);
        $vaccine = $this->createVaccine();

        // Step 1: Parent creates child
        $this->actingAs($parent)->post(route('parent.children.store'), [
            'name' => 'Baby Ahmed',
            'date_of_birth' => '2024-01-15',
            'gender' => 'male',
            'relationship' => 'son',
        ]);
        $child = Child::where('user_id', $parent->id)->first();
        $this->assertNotNull($child);

        // Step 2: Parent views schedule
        $response = $this->actingAs($parent)->get(route('parent.vaccinations.schedule'));
        $response->assertStatus(200);

        // Step 3: Parent discovers hospital
        $response = $this->actingAs($parent)->get(route('parent.hospitals.index'));
        $response->assertStatus(200);
        $response->assertSee($hospital->name);

        // Step 4: Parent creates appointment
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

        // Step 5: Hospital views and confirms appointment
        $this->actingAs($hospitalUser)->post(route('hospital.appointments.confirm', $appointment));
        $appointment->refresh();
        $this->assertEquals('confirmed', $appointment->status);

        // Step 6: Hospital completes vaccination
        $this->actingAs($hospitalUser)->post(route('hospital.appointments.complete', $appointment), [
            'dose_number' => 1,
            'administered_by' => 'Dr. Smith',
            'batch_number' => 'BATCH-001',
            'notes' => 'No adverse reactions',
        ]);
        $appointment->refresh();
        $this->assertEquals('completed', $appointment->status);

        // Step 7: Verify vaccination record created
        $record = VaccinationRecord::where('appointment_id', $appointment->id)->first();
        $this->assertNotNull($record);
        $this->assertEquals($child->id, $record->child_id);
        $this->assertEquals($hospital->id, $record->hospital_id);
        $this->assertEquals($vaccine->id, $record->vaccine_id);

        // Step 8: Parent sees vaccination history
        $response = $this->actingAs($parent)->get(route('parent.vaccinations.history'));
        $response->assertStatus(200);
        $response->assertSee($child->name);

        // Step 9: Parent sees record detail
        $response = $this->actingAs($parent)->get(route('parent.vaccinations.show', $record));
        $response->assertStatus(200);

        // Step 10: Admin sees appointment in reports
        $response = $this->actingAs($admin)->get(route('admin.reports'));
        $response->assertStatus(200);

        // Step 11: Verify notifications were created
        $this->assertDatabaseHas('notifications', ['user_id' => $parent->id, 'type' => 'appointment_confirmed']);
        $this->assertDatabaseHas('notifications', ['user_id' => $parent->id, 'type' => 'vaccination_completed']);
    }

    // ===== APPOINTMENT STATE MACHINE =====

    public function test_pending_to_confirmed_transition(): void
    {
        $hospital = $this->createHospital();
        $user = $this->createHospitalUser($hospital);
        $parent = $this->createParent();
        $child = Child::factory()->create(['user_id' => $parent->id]);
        $vaccine = $this->createVaccine();
        $apt = Appointment::create([
            'child_id' => $child->id, 'parent_id' => $parent->id,
            'hospital_id' => $hospital->id, 'vaccine_id' => $vaccine->id,
            'dose' => '1st', 'appointment_date' => now()->addDays(3), 'appointment_time' => '09:00', 'status' => 'pending',
        ]);

        $this->actingAs($user)->post(route('hospital.appointments.confirm', $apt));
        $this->assertDatabaseHas('appointments', ['id' => $apt->id, 'status' => 'confirmed']);
    }

    public function test_confirmed_to_completed_transition(): void
    {
        $hospital = $this->createHospital();
        $user = $this->createHospitalUser($hospital);
        $parent = $this->createParent();
        $child = Child::factory()->create(['user_id' => $parent->id]);
        $vaccine = $this->createVaccine();
        $apt = Appointment::create([
            'child_id' => $child->id, 'parent_id' => $parent->id,
            'hospital_id' => $hospital->id, 'vaccine_id' => $vaccine->id,
            'dose' => '1st', 'appointment_date' => now(), 'appointment_time' => '09:00', 'status' => 'confirmed',
        ]);

        $this->actingAs($user)->post(route('hospital.appointments.complete', $apt), [
            'dose_number' => 1, 'administered_by' => 'Dr. X',
        ]);
        $this->assertDatabaseHas('appointments', ['id' => $apt->id, 'status' => 'completed']);
        $this->assertDatabaseHas('vaccination_records', ['appointment_id' => $apt->id]);
    }

    public function test_confirmed_to_no_show_transition(): void
    {
        $hospital = $this->createHospital();
        $user = $this->createHospitalUser($hospital);
        $parent = $this->createParent();
        $child = Child::factory()->create(['user_id' => $parent->id]);
        $vaccine = $this->createVaccine();
        $apt = Appointment::create([
            'child_id' => $child->id, 'parent_id' => $parent->id,
            'hospital_id' => $hospital->id, 'vaccine_id' => $vaccine->id,
            'dose' => '1st', 'appointment_date' => now(), 'appointment_time' => '09:00', 'status' => 'confirmed',
        ]);

        $this->actingAs($user)->post(route('hospital.appointments.no-show', $apt));
        $this->assertDatabaseHas('appointments', ['id' => $apt->id, 'status' => 'no_show']);
    }

    public function test_parent_can_cancel_pending_appointment(): void
    {
        $parent = $this->createParent();
        $child = Child::factory()->create(['user_id' => $parent->id]);
        $hospital = $this->createHospital();
        $vaccine = $this->createVaccine();
        $apt = Appointment::create([
            'child_id' => $child->id, 'parent_id' => $parent->id,
            'hospital_id' => $hospital->id, 'vaccine_id' => $vaccine->id,
            'dose' => '1st', 'appointment_date' => now()->addDays(5), 'appointment_time' => '09:00', 'status' => 'pending',
        ]);

        $this->actingAs($parent)->post(route('parent.appointments.cancel', $apt));
        $this->assertDatabaseHas('appointments', ['id' => $apt->id, 'status' => 'cancelled']);
    }

    public function test_cannot_complete_already_completed_appointment(): void
    {
        $hospital = $this->createHospital();
        $user = $this->createHospitalUser($hospital);
        $parent = $this->createParent();
        $child = Child::factory()->create(['user_id' => $parent->id]);
        $vaccine = $this->createVaccine();
        $apt = Appointment::create([
            'child_id' => $child->id, 'parent_id' => $parent->id,
            'hospital_id' => $hospital->id, 'vaccine_id' => $vaccine->id,
            'dose' => '1st', 'appointment_date' => now(), 'appointment_time' => '09:00', 'status' => 'completed',
        ]);

        $response = $this->actingAs($user)->post(route('hospital.appointments.complete', $apt), [
            'dose_number' => 1, 'administered_by' => 'Dr. X',
        ]);
        $response->assertStatus(400);
    }

    // ===== DUPLICATE PREVENTION =====

    public function test_prevent_duplicate_appointment(): void
    {
        $parent = $this->createParent();
        $child = Child::factory()->create(['user_id' => $parent->id]);
        $hospital = $this->createHospital();
        $vaccine = $this->createVaccine();

        // First appointment
        $this->actingAs($parent)->post(route('parent.appointments.store'), [
            'child_id' => $child->id, 'hospital_id' => $hospital->id,
            'vaccine_id' => $vaccine->id, 'dose' => '1st Dose',
            'appointment_date' => now()->addDays(5)->format('Y-m-d'),
            'appointment_time' => '10:00',
        ]);

        // Duplicate appointment
        $response = $this->actingAs($parent)->post(route('parent.appointments.store'), [
            'child_id' => $child->id, 'hospital_id' => $hospital->id,
            'vaccine_id' => $vaccine->id, 'dose' => '1st Dose',
            'appointment_date' => now()->addDays(6)->format('Y-m-d'),
            'appointment_time' => '11:00',
        ]);
        $response->assertSessionHasErrors();
    }

    public function test_prevent_duplicate_vaccination_record(): void
    {
        $hospital = $this->createHospital();
        $user = $this->createHospitalUser($hospital);
        $parent = $this->createParent();
        $child = Child::factory()->create(['user_id' => $parent->id]);
        $vaccine = $this->createVaccine();
        $apt = Appointment::create([
            'child_id' => $child->id, 'parent_id' => $parent->id,
            'hospital_id' => $hospital->id, 'vaccine_id' => $vaccine->id,
            'dose' => '1st', 'appointment_date' => now(), 'appointment_time' => '09:00', 'status' => 'confirmed',
        ]);

        // First vaccination
        $this->actingAs($user)->post(route('hospital.appointments.complete', $apt), [
            'dose_number' => 1, 'administered_by' => 'Dr. X',
        ]);

        // Reload appointment
        $apt->refresh();

        // Try to vaccinate again (should be prevented since status is now completed)
        $response = $this->actingAs($user)->post(route('hospital.appointments.complete', $apt), [
            'dose_number' => 2, 'administered_by' => 'Dr. X',
        ]);
        $response->assertStatus(400);
    }

    // ===== INVENTORY INTEGRATION =====

    public function test_inventory_deducted_on_vaccination(): void
    {
        $hospital = $this->createHospital();
        $user = $this->createHospitalUser($hospital);
        $parent = $this->createParent();
        $child = Child::factory()->create(['user_id' => $parent->id]);
        $vaccine = $this->createVaccine();

        $inventory = VaccineInventory::create([
            'hospital_id' => $hospital->id, 'vaccine_id' => $vaccine->id,
            'available' => 50, 'capacity' => 100,
        ]);

        $apt = Appointment::create([
            'child_id' => $child->id, 'parent_id' => $parent->id,
            'hospital_id' => $hospital->id, 'vaccine_id' => $vaccine->id,
            'dose' => '1st', 'appointment_date' => now(), 'appointment_time' => '09:00', 'status' => 'confirmed',
        ]);

        $this->actingAs($user)->post(route('hospital.appointments.complete', $apt), [
            'dose_number' => 1, 'administered_by' => 'Dr. X',
        ]);

        $inventory->refresh();
        $this->assertEquals(49, $inventory->available);
    }

    public function test_inventory_not_negative(): void
    {
        $hospital = $this->createHospital();
        $user = $this->createHospitalUser($hospital);
        $parent = $this->createParent();
        $child = Child::factory()->create(['user_id' => $parent->id]);
        $vaccine = $this->createVaccine();

        $inventory = VaccineInventory::create([
            'hospital_id' => $hospital->id, 'vaccine_id' => $vaccine->id,
            'available' => 0, 'capacity' => 100,
        ]);

        // Vaccination still completes even with 0 inventory (soft check, not hard block)
        $apt = Appointment::create([
            'child_id' => $child->id, 'parent_id' => $parent->id,
            'hospital_id' => $hospital->id, 'vaccine_id' => $vaccine->id,
            'dose' => '1st', 'appointment_date' => now(), 'appointment_time' => '09:00', 'status' => 'confirmed',
        ]);

        $this->actingAs($user)->post(route('hospital.appointments.complete', $apt), [
            'dose_number' => 1, 'administered_by' => 'Dr. X',
        ]);

        $inventory->refresh();
        $this->assertEquals(0, $inventory->available); // Not negative
    }

    // ===== NOTIFICATION INTEGRATION =====

    public function test_notifications_created_on_key_events(): void
    {
        $parent = $this->createParent();
        $hospital = $this->createHospital();
        $hospitalUser = $this->createHospitalUser($hospital);
        $child = Child::factory()->create(['user_id' => $parent->id]);
        $vaccine = $this->createVaccine();

        // Book appointment
        $apt = Appointment::create([
            'child_id' => $child->id, 'parent_id' => $parent->id,
            'hospital_id' => $hospital->id, 'vaccine_id' => $vaccine->id,
            'dose' => '1st', 'appointment_date' => now()->addDays(5), 'appointment_time' => '09:00', 'status' => 'pending',
        ]);

        // Hospital confirms
        $this->actingAs($hospitalUser)->post(route('hospital.appointments.confirm', $apt));
        $this->assertDatabaseHas('notifications', ['user_id' => $parent->id, 'type' => 'appointment_confirmed']);

        // Hospital completes vaccination
        $this->actingAs($hospitalUser)->post(route('hospital.appointments.complete', $apt), [
            'dose_number' => 1, 'administered_by' => 'Dr. X',
        ]);
        $this->assertDatabaseHas('notifications', ['user_id' => $parent->id, 'type' => 'vaccination_completed']);
    }

    public function test_no_show_creates_notification(): void
    {
        $parent = $this->createParent();
        $hospital = $this->createHospital();
        $hospitalUser = $this->createHospitalUser($hospital);
        $child = Child::factory()->create(['user_id' => $parent->id]);
        $vaccine = $this->createVaccine();

        $apt = Appointment::create([
            'child_id' => $child->id, 'parent_id' => $parent->id,
            'hospital_id' => $hospital->id, 'vaccine_id' => $vaccine->id,
            'dose' => '1st', 'appointment_date' => now(), 'appointment_time' => '09:00', 'status' => 'confirmed',
        ]);

        $this->actingAs($hospitalUser)->post(route('hospital.appointments.no-show', $apt));
        $this->assertDatabaseHas('notifications', ['user_id' => $parent->id, 'type' => 'appointment_no_show']);
    }

    // ===== CROSS-HOSPITAL ISOLATION =====

    public function test_hospital_a_cannot_see_hospital_b_appointments(): void
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

    public function test_hospital_a_cannot_vaccinate_hospital_b_appointment(): void
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

    // ===== CROSS-PARENT ISOLATION =====

    public function test_parent_a_cannot_see_parent_b_appointments(): void
    {
        $parentA = $this->createParent();
        $parentB = $this->createParent();
        $hospital = $this->createHospital();
        $childB = Child::factory()->create(['user_id' => $parentB->id]);
        $vaccine = $this->createVaccine();

        $apt = Appointment::create([
            'child_id' => $childB->id, 'parent_id' => $parentB->id,
            'hospital_id' => $hospital->id, 'vaccine_id' => $vaccine->id,
            'dose' => '1st', 'appointment_date' => now(), 'appointment_time' => '09:00', 'status' => 'pending',
        ]);

        $response = $this->actingAs($parentA)->get(route('parent.appointments.show', $apt));
        $response->assertStatus(403);
    }

    public function test_parent_a_cannot_see_parent_b_vaccination_records(): void
    {
        $parentA = $this->createParent();
        $parentB = $this->createParent();
        $hospital = $this->createHospital();
        $childB = Child::factory()->create(['user_id' => $parentB->id]);
        $vaccine = $this->createVaccine();

        $record = VaccinationRecord::create([
            'child_id' => $childB->id, 'hospital_id' => $hospital->id,
            'vaccine_id' => $vaccine->id, 'dose_number' => 1, 'administered_at' => now(), 'administered_by' => 'Dr. X',
        ]);

        $response = $this->actingAs($parentA)->get(route('parent.vaccinations.show', $record));
        $response->assertStatus(403);
    }

    // ===== CROSS-ROLE SECURITY =====

    public function test_parent_cannot_call_hospital_routes(): void
    {
        $parent = $this->createParent();
        $response = $this->actingAs($parent)->get(route('hospital.dashboard'));
        $response->assertStatus(403);
    }

    public function test_hospital_cannot_call_parent_routes(): void
    {
        $hospital = $this->createHospital();
        $user = $this->createHospitalUser($hospital);
        $response = $this->actingAs($user)->get(route('parent.dashboard'));
        $response->assertStatus(403);
    }

    public function test_parent_cannot_call_admin_routes(): void
    {
        $parent = $this->createParent();
        $response = $this->actingAs($parent)->get(route('admin.dashboard'));
        $response->assertStatus(403);
    }

    public function test_hospital_cannot_call_admin_routes(): void
    {
        $hospital = $this->createHospital();
        $user = $this->createHospitalUser($hospital);
        $response = $this->actingAs($user)->get(route('admin.dashboard'));
        $response->assertStatus(403);
    }

    // ===== ADMIN APPROVE/REJECT =====

    public function test_admin_can_approve_appointment(): void
    {
        $admin = $this->createAdmin();
        $parent = $this->createParent();
        $hospital = $this->createHospital();
        $child = Child::factory()->create(['user_id' => $parent->id]);
        $vaccine = $this->createVaccine();

        $apt = Appointment::create([
            'child_id' => $child->id, 'parent_id' => $parent->id,
            'hospital_id' => $hospital->id, 'vaccine_id' => $vaccine->id,
            'dose' => '1st', 'appointment_date' => now()->addDays(5), 'appointment_time' => '09:00', 'status' => 'pending',
        ]);

        $this->actingAs($admin)->post(route('admin.appointments.approve', $apt));
        $this->assertDatabaseHas('appointments', ['id' => $apt->id, 'status' => 'approved']);
        $this->assertDatabaseHas('notifications', ['user_id' => $parent->id, 'type' => 'appointment_approved']);
    }

    public function test_admin_can_reject_appointment(): void
    {
        $admin = $this->createAdmin();
        $parent = $this->createParent();
        $hospital = $this->createHospital();
        $child = Child::factory()->create(['user_id' => $parent->id]);
        $vaccine = $this->createVaccine();

        $apt = Appointment::create([
            'child_id' => $child->id, 'parent_id' => $parent->id,
            'hospital_id' => $hospital->id, 'vaccine_id' => $vaccine->id,
            'dose' => '1st', 'appointment_date' => now()->addDays(5), 'appointment_time' => '09:00', 'status' => 'pending',
        ]);

        $this->actingAs($admin)->post(route('admin.appointments.reject', $apt));
        $this->assertDatabaseHas('appointments', ['id' => $apt->id, 'status' => 'rejected']);
        $this->assertDatabaseHas('notifications', ['user_id' => $parent->id, 'type' => 'appointment_rejected']);
    }

    // ===== VACCINATION RECORD INTEGRITY =====

    public function test_vaccination_record_linked_correctly(): void
    {
        $hospital = $this->createHospital();
        $user = $this->createHospitalUser($hospital);
        $parent = $this->createParent();
        $child = Child::factory()->create(['user_id' => $parent->id]);
        $vaccine = $this->createVaccine();

        $apt = Appointment::create([
            'child_id' => $child->id, 'parent_id' => $parent->id,
            'hospital_id' => $hospital->id, 'vaccine_id' => $vaccine->id,
            'dose' => '1st', 'appointment_date' => now(), 'appointment_time' => '09:00', 'status' => 'confirmed',
        ]);

        $this->actingAs($user)->post(route('hospital.appointments.complete', $apt), [
            'dose_number' => 1, 'batch_number' => 'BATCH-001', 'administered_by' => 'Dr. Smith',
        ]);

        $record = VaccinationRecord::where('appointment_id', $apt->id)->first();
        $this->assertNotNull($record);
        $this->assertEquals($child->id, $record->child_id);
        $this->assertEquals($hospital->id, $record->hospital_id);
        $this->assertEquals($vaccine->id, $record->vaccine_id);
        $this->assertEquals(1, $record->dose_number);
        $this->assertEquals('BATCH-001', $record->batch_number);
        $this->assertEquals('Dr. Smith', $record->administered_by);
        $this->assertNotNull($record->administered_at);
    }

    public function test_parent_history_reflects_vaccination(): void
    {
        $parent = $this->createParent();
        $hospital = $this->createHospital();
        $hospitalUser = $this->createHospitalUser($hospital);
        $child = Child::factory()->create(['user_id' => $parent->id]);
        $vaccine = $this->createVaccine();

        $apt = Appointment::create([
            'child_id' => $child->id, 'parent_id' => $parent->id,
            'hospital_id' => $hospital->id, 'vaccine_id' => $vaccine->id,
            'dose' => '1st', 'appointment_date' => now(), 'appointment_time' => '09:00', 'status' => 'confirmed',
        ]);

        $this->actingAs($hospitalUser)->post(route('hospital.appointments.complete', $apt), [
            'dose_number' => 1, 'administered_by' => 'Dr. X',
        ]);

        $response = $this->actingAs($parent)->get(route('parent.vaccinations.history'));
        $response->assertStatus(200);
        $response->assertSee($child->name);
        $response->assertSee($vaccine->name);
    }
}
