<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Hospital;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (auth()->check()) {
            return $this->redirectByRole(auth()->user());
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::clear(md5('login:' . $request->input('email') . '|' . $request->ip()));

            $request->session()->regenerate();

            return $this->redirectByRole(auth()->user());
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    public function showRegisterParent()
    {
        if (auth()->check()) {
            return $this->redirectByRole(auth()->user());
        }
        return view('auth.register-parent');
    }

    public function registerParent(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:20'],
            'city' => ['required', 'string', 'max:100'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'city' => $validated['city'],
            'password' => Hash::make($validated['password']),
            'role' => 'parent',
        ]);

        Auth::login($user);
        return redirect()->route('parent.dashboard');
    }

    public function showRegisterHospital()
    {
        if (auth()->check()) {
            return $this->redirectByRole(auth()->user());
        }
        return view('auth.register-hospital');
    }

    public function registerHospital(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:hospitals,code'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:hospitals,email'],
            'phone' => ['required', 'string', 'max:20'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'max:100'],
            'address' => ['required', 'string', 'max:500'],
            'contact_person' => ['required', 'string', 'max:255'],
            'designation' => ['required', 'string', 'max:100'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ]);

        $hospital = Hospital::create([
            'name' => $validated['name'],
            'code' => $validated['code'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'city' => $validated['city'],
            'state' => $validated['state'],
            'contact_person' => $validated['contact_person'],
            'designation' => $validated['designation'],
            'status' => 'pending',
        ]);

        $user = User::create([
            'name' => $validated['contact_person'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'hospital',
            'phone' => $validated['phone'],
            'hospital_id' => $hospital->id,
        ]);

        return redirect()->route('login')->with('success', 'Hospital registration submitted. Your account is pending admin approval.');
    }

    private function redirectByRole(User $user)
    {
        return match ($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'parent' => redirect()->route('parent.dashboard'),
            'hospital' => redirect()->route('hospital.dashboard'),
            default => redirect()->route('login'),
        };
    }
}
