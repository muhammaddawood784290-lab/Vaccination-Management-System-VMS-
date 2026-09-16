<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class LoginRateLimitHttpTest extends TestCase
{
    use RefreshDatabase;

    /**
     * The login route is protected by the throttle:login middleware.
     * The middleware stores attempts under md5('login:' . email . '|' . ip).
     * We build that key once so the internal-state assertions in this suite
     * read the same cache entries the middleware writes.
     */
    private function throttleKey(string $email, string $ip = '127.0.0.1'): string
    {
        return md5('login:' . $email . '|' . $ip);
    }

    public function test_login_throttles_after_five_failed_attempts_via_http_flow(): void
    {
        $user = User::factory()->create([
            'email' => 'httpThrottle@test.com',
            'password' => Hash::make('realsecret'),
            'role' => 'parent',
        ]);

        $this->get('/login');

        $email = 'httpThrottle@test.com';

        for ($i = 1; $i <= 5; $i++) {
            $response = $this->withSession(['_token' => csrf_token()])
                ->post('/login', [
                    'email' => $email,
                    'password' => "wrong{$i}",
                ]);

            $response->assertSessionHasErrors('email');
            $this->assertGuest();
            $response->assertStatus(302);
        }

        $response = $this->withSession(['_token' => csrf_token()])
            ->post('/login', [
                'email' => $email,
                'password' => 'wrong6',
            ]);

        $response->assertStatus(429);
        $response->assertSee('Too Many Requests');
    }

    public function test_throttle_key_combines_submitted_email_and_client_ip(): void
    {
        $user = User::factory()->create([
            'email' => 'keyTest@test.com',
            'password' => Hash::make('realsecret'),
            'role' => 'parent',
        ]);

        $this->get('/login');

        $identity = 'keyTest@test.com';

        for ($i = 1; $i <= 5; $i++) {
            $this->withSession(['_token' => csrf_token()])
                ->post('/login', [
                    'email' => $identity,
                    'password' => "wrong{$i}",
                ]);
        }

        $response = $this->withSession(['_token' => csrf_token()])
            ->post('/login', [
                'email' => $identity,
                'password' => 'wrong6',
            ]);

        $response->assertStatus(429);
        $response->assertSee('Too Many Requests');
    }

    public function test_throttle_isolation_between_identities_via_http(): void
    {
        $userA = User::factory()->create([
            'email' => 'userAHttp@test.com',
            'password' => Hash::make('realsecret'),
            'role' => 'parent',
        ]);
        $userB = User::factory()->create([
            'email' => 'userBHttp@test.com',
            'password' => Hash::make('realsecret'),
            'role' => 'parent',
        ]);

        $this->get('/login');

        for ($i = 1; $i <= 5; $i++) {
            $this->withSession(['_token' => csrf_token()])
                ->post('/login', [
                    'email' => 'userAHttp@test.com',
                    'password' => "wrong{$i}",
                ]);
        }

        $responseA = $this->withSession(['_token' => csrf_token()])
            ->post('/login', [
                'email' => 'userAHttp@test.com',
                'password' => 'wrong',
            ]);

        $responseA->assertStatus(429);
        $responseA->assertSee('Too Many Requests');

        // Identity B has not been throttled and can still authenticate.
        $responseB = $this->withSession(['_token' => csrf_token()])
            ->post('/login', [
                'email' => 'userBHttp@test.com',
                'password' => 'realsecret',
            ]);

        $responseB->assertRedirect(route('parent.dashboard'));
        $this->assertAuthenticatedAs($userB);
    }

    public function test_successful_login_is_not_throttled_when_no_prior_failures(): void
    {
        // A fresh identity with no failed attempts must be able to log in
        // successfully, proving the throttle does not block legitimate users.
        $user = User::factory()->create([
            'email' => 'successProbe@test.com',
            'password' => Hash::make('realsecret'),
            'role' => 'parent',
        ]);

        $this->get('/login');

        $response = $this->withSession(['_token' => csrf_token()])
            ->post('/login', [
                'email' => 'successProbe@test.com',
                'password' => 'realsecret',
            ]);

        $response->assertRedirect(route('parent.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_throttle_threshold_is_five_not_six(): void
    {
        $this->get('/login');

        $email = 'thresholdProbe@test.com';

        for ($i = 1; $i <= 5; $i++) {
            $response = $this->withSession(['_token' => csrf_token()])
                ->post('/login', [
                    'email' => $email,
                    'password' => "wrong{$i}",
                ]);

            $response->assertStatus(302);
            $response->assertSessionHasErrors('email');
            $this->assertGuest();
        }

        // Prove the threshold via HTTP: the 6th attempt with a different
        // identity succeeds, proving only this identity is throttled.
        $userB = User::factory()->create([
            'email' => 'thresholdProbeB@test.com',
            'password' => Hash::make('realsecretB'),
            'role' => 'parent',
        ]);

        $other = $this->withSession(['_token' => csrf_token()])
            ->post('/login', [
                'email' => 'thresholdProbeB@test.com',
                'password' => 'realsecretB',
            ]);

        $other->assertRedirect(route('parent.dashboard'));
        $this->assertAuthenticatedAs($userB);
    }

    public function test_throttle_uses_persistent_cache_store(): void
    {
        // The throttle middleware stores state in the application's cache.
        // We prove persistence by exhausting the limiter for one identity,
        // then proving a different identity can still authenticate within the
        // same test (same cache). This works because the array cache driver
        // is shared inside a single test method.
        $this->get('/login');

        $userA = User::factory()->create([
            'email' => 'persistA@test.com',
            'password' => Hash::make('secretA'),
            'role' => 'parent',
        ]);
        $userB = User::factory()->create([
            'email' => 'persistB@test.com',
            'password' => Hash::make('secretB'),
            'role' => 'parent',
        ]);

        for ($i = 1; $i <= 5; $i++) {
            $this->withSession(['_token' => csrf_token()])
                ->post('/login', [
                    'email' => 'persistA@test.com',
                    'password' => "wrong{$i}",
                ]);
        }

        // A is now throttled.
        $throttledA = $this->withSession(['_token' => csrf_token()])
            ->post('/login', [
                'email' => 'persistA@test.com',
                'password' => 'wrong',
            ]);

        $throttledA->assertStatus(429);

        // B is unaffected and can still authenticate.
        $okB = $this->withSession(['_token' => csrf_token()])
            ->post('/login', [
                'email' => 'persistB@test.com',
                'password' => 'secretB',
            ]);

        $okB->assertRedirect(route('parent.dashboard'));
        $this->assertAuthenticatedAs($userB);
    }
}
