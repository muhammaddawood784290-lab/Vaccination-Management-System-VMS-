<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Models\Child;
use App\Models\Hospital;
use App\Models\Appointment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_admin_routes(): void
    {
        $user = User::factory()->create(["role" => "admin"]);
        $response = $this->actingAs($user)->get("/admin/dashboard");
        $response->assertStatus(200);
    }

    public function test_parent_can_access_parent_routes(): void
    {
        $user = User::factory()->create(["role" => "parent"]);
        $response = $this->actingAs($user)->get("/parent/dashboard");
        $response->assertStatus(200);
    }

    public function test_hospital_can_access_hospital_routes(): void
    {
        $user = User::factory()->create(["role" => "hospital"]);
        $response = $this->actingAs($user)->get("/hospital/dashboard");
        $response->assertStatus(200);
    }

    public function test_parent_cannot_access_admin_routes(): void
    {
        $user = User::factory()->create(["role" => "parent"]);
        $response = $this->actingAs($user)->get("/admin/dashboard");
        $response->assertStatus(403);
    }

    public function test_parent_cannot_access_hospital_routes(): void
    {
        $user = User::factory()->create(["role" => "parent"]);
        $response = $this->actingAs($user)->get("/hospital/dashboard");
        $response->assertStatus(403);
    }

    public function test_hospital_cannot_access_admin_routes(): void
    {
        $user = User::factory()->create(["role" => "hospital"]);
        $response = $this->actingAs($user)->get("/admin/dashboard");
        $response->assertStatus(403);
    }

    public function test_hospital_cannot_access_parent_routes(): void
    {
        $user = User::factory()->create(["role" => "hospital"]);
        $response = $this->actingAs($user)->get("/parent/dashboard");
        $response->assertStatus(403);
    }

    public function test_admin_cannot_access_parent_routes(): void
    {
        $user = User::factory()->create(["role" => "admin"]);
        $response = $this->actingAs($user)->get("/parent/dashboard");
        $response->assertStatus(403);
    }

    public function test_admin_cannot_access_hospital_routes(): void
    {
        $user = User::factory()->create(["role" => "admin"]);
        $response = $this->actingAs($user)->get("/hospital/dashboard");
        $response->assertStatus(403);
    }

    // ===== ADMIN SETTINGS (regression: View [admin.settings.show] not found) =====

    public function test_admin_can_access_settings_page(): void
    {
        $user = User::factory()->create(["role" => "admin"]);
        $response = $this->actingAs($user)->get("/admin/settings");
        $response->assertStatus(200);
        $response->assertSee("Settings");
    }

    public function test_admin_can_change_password_via_settings(): void
    {
        $user = User::factory()->create(["role" => "admin", "password" => Hash::make("currentsecret")]);
        $response = $this->actingAs($user)->put("/admin/settings/password", [
            "current_password" => "currentsecret",
            "password" => "NewSecret123",
            "password_confirmation" => "NewSecret123",
        ]);
        $response->assertRedirect();
        $response->assertSessionHas("success");
        $this->assertTrue(Hash::check("NewSecret123", $user->fresh()->password));
    }

    public function test_admin_settings_password_rejects_wrong_current_password(): void
    {
        $user = User::factory()->create(["role" => "admin", "password" => Hash::make("currentsecret")]);
        $response = $this->actingAs($user)->put("/admin/settings/password", [
            "current_password" => "wrongpassword",
            "password" => "NewSecret123",
            "password_confirmation" => "NewSecret123",
        ]);
        $response->assertSessionHasErrors("current_password");
        $this->assertTrue(Hash::check("currentsecret", $user->fresh()->password));
    }

    public function test_unauthenticated_user_cannot_access_admin_settings(): void
    {
        $response = $this->get("/admin/settings");
        $response->assertRedirect("/login");
    }

    public function test_parent_cannot_access_admin_settings(): void
    {
        $user = User::factory()->create(["role" => "parent"]);
        $response = $this->actingAs($user)->get("/admin/settings");
        $response->assertStatus(403);
    }

    public function test_hospital_cannot_access_admin_settings(): void
    {
        $user = User::factory()->create(["role" => "hospital"]);
        $response = $this->actingAs($user)->get("/admin/settings");
        $response->assertStatus(403);
    }

    public function test_parent_cannot_access_another_parents_child_via_policy(): void
    {
        $parent1 = User::factory()->create(["role" => "parent"]);
        $parent2 = User::factory()->create(["role" => "parent"]);
        $child = Child::factory()->create(["user_id" => $parent2->id]);
        $this->assertFalse($parent1->can("view", $child));
    }

    public function test_parent_registration_screen_can_be_rendered(): void
    {
        $response = $this->get("/register/parent");
        $response->assertStatus(200);
        $response->assertSee("Create parent account");
    }

    public function test_hospital_registration_screen_can_be_rendered(): void
    {
        $response = $this->get("/register/hospital");
        $response->assertStatus(200);
        $response->assertSee("Register your hospital");
    }

    public function test_parent_registration_works(): void
    {
        $response = $this->post("/register/parent", [
            "name" => "Test Parent",
            "email" => "newparent@test.com",
            "phone" => "+1-555-0000",
            "city" => "Test City",
            "password" => "Password1!",
            "password_confirmation" => "Password1!",
        ]);
        $this->assertDatabaseHas("users", ["email" => "newparent@test.com", "role" => "parent"]);
        $this->assertAuthenticated();
        $response->assertRedirect(route("parent.dashboard"));
    }

    public function test_hospital_registration_creates_pending_hospital(): void
    {
        $response = $this->post("/register/hospital", [
            "name" => "Test Hospital",
            "code" => "TEST-001",
            "email" => "test@hospital.com",
            "phone" => "+1-555-0001",
            "city" => "Test City",
            "state" => "Test State",
            "address" => "123 Test Street",
            "contact_person" => "Dr. Test",
            "designation" => "Director",
            "password" => "Password1!",
            "password_confirmation" => "Password1!",
        ]);
        $this->assertDatabaseHas("hospitals", ["code" => "TEST-001", "status" => "pending"]);
        $this->assertDatabaseHas("users", ["email" => "test@hospital.com", "role" => "hospital"]);
        $this->assertGuest();
        $response->assertRedirect("/login");
    }

    public function test_duplicate_email_registration_fails(): void
    {
        User::factory()->create(["email" => "existing@test.com"]);
        $response = $this->post("/register/parent", [
            "name" => "Test Parent",
            "email" => "existing@test.com",
            "phone" => "+1-555-0000",
            "city" => "Test City",
            "password" => "Password1!",
            "password_confirmation" => "Password1!",
        ]);
        $response->assertSessionHasErrors("email");
    }

    public function test_weak_password_registration_fails(): void
    {
        $response = $this->post("/register/parent", [
            "name" => "Test Parent",
            "email" => "new@test.com",
            "phone" => "+1-555-0000",
            "city" => "Test City",
            "password" => "weak",
            "password_confirmation" => "weak",
        ]);
        $response->assertSessionHasErrors("password");
    }

    public function test_admin_cannot_self_register(): void
    {
        $response = $this->post("/register/parent", [
            "name" => "Fake Admin",
            "email" => "fakeadmin@test.com",
            "phone" => "+1-555-0000",
            "city" => "Test City",
            "password" => "Password1!",
            "password_confirmation" => "Password1!",
        ]);
        $this->assertDatabaseHas("users", ["email" => "fakeadmin@test.com", "role" => "parent"]);
        $this->assertDatabaseMissing("users", ["email" => "fakeadmin@test.com", "role" => "admin"]);
    }

    public function test_logged_in_user_redirected_from_login_page(): void
    {
        $user = User::factory()->create(["role" => "parent"]);
        $response = $this->actingAs($user)->get("/login");
        $response->assertRedirect(url("/"));
    }
}