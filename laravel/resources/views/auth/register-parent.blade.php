@extends('auth.layout')
@section('auth-content')
<a href="{{ route('login') }}" class="flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 mb-6">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
    Back to sign in
</a>

<h1 class="text-2xl font-bold text-gray-900 mb-1">Create parent account</h1>
<p class="text-sm text-gray-500 mb-6">Register to manage your children's vaccinations</p>

<form method="POST" action="{{ route('register.parent.submit') }}" class="space-y-4">
    @csrf

    {{-- Name --}}
    <div>
        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full name <span class="text-red-500">*</span></label>
        <input id="name" type="text" name="name" value="{{ old('name') }}" required placeholder="Your full name"
               class="block w-full px-3 py-2 border {{ $errors->has('name') ? 'border-red-300 ring-1 ring-red-300' : 'border-gray-300' }} rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>

    {{-- Email --}}
    <div>
        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email address <span class="text-red-500">*</span></label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required placeholder="you@example.com"
               class="block w-full px-3 py-2 border {{ $errors->has('email') ? 'border-red-300 ring-1 ring-red-300' : 'border-gray-300' }} rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>

    {{-- Phone --}}
    <div>
        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone number <span class="text-red-500">*</span></label>
        <input id="phone" type="tel" name="phone" value="{{ old('phone') }}" required placeholder="+1 555 123 4567"
               class="block w-full px-3 py-2 border {{ $errors->has('phone') ? 'border-red-300 ring-1 ring-red-300' : 'border-gray-300' }} rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        @error('phone') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>

    {{-- City --}}
    <div>
        <label for="city" class="block text-sm font-medium text-gray-700 mb-1">City <span class="text-red-500">*</span></label>
        <input id="city" type="text" name="city" value="{{ old('city') }}" required placeholder="Your city"
               class="block w-full px-3 py-2 border {{ $errors->has('city') ? 'border-red-300 ring-1 ring-red-300' : 'border-gray-300' }} rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        @error('city') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>

    {{-- Password --}}
    <div>
        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password <span class="text-red-500">*</span></label>
        <input id="password" type="password" name="password" required placeholder="Min. 8 characters"
               class="block w-full px-3 py-2 border {{ $errors->has('password') ? 'border-red-300 ring-1 ring-red-300' : 'border-gray-300' }} rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        <p class="mt-1 text-xs text-gray-400">Must contain uppercase, number and special character</p>
        @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>

    {{-- Confirm Password --}}
    <div>
        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm password <span class="text-red-500">*</span></label>
        <input id="password_confirmation" type="password" name="password_confirmation" required placeholder="Repeat password"
               class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
    </div>

    {{-- Submit --}}
    <button type="submit"
            class="w-full py-2.5 px-4 text-sm font-semibold text-white rounded-lg bg-blue-600 hover:bg-blue-700 transition focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
        Create account
    </button>
</form>

<p class="text-sm text-center text-gray-500 mt-6">
    Already have an account?
    <a href="{{ route('login') }}" class="text-blue-600 hover:underline font-medium">Sign in</a>
</p>
@endsection
