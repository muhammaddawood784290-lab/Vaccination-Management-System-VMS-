@extends('auth.layout')
@section('auth-content')
<a href="{{ route('login') }}" class="flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 mb-6">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
    Back to sign in
</a>

<h1 class="text-2xl font-bold text-gray-900 mb-1">Register your hospital</h1>
<p class="text-sm text-gray-500 mb-2">Join the VMS network to manage vaccinations efficiently</p>

<div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-5 text-sm text-blue-700">
    Hospital registrations are reviewed by the VMS admin team before approval (typically 2–3 business days).
</div>

<form method="POST" action="{{ route('register.hospital.submit') }}" class="space-y-4">
    @csrf

    <div>
        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Hospital name <span class="text-red-500">*</span></label>
        <input id="name" type="text" name="name" value="{{ old('name') }}" required placeholder="Apollo Children's Hospital"
               class="block w-full px-3 py-2 border {{ $errors->has('name') ? 'border-red-300 ring-1 ring-red-300' : 'border-gray-300' }} rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="code" class="block text-sm font-medium text-gray-700 mb-1">Registration / license number <span class="text-red-500">*</span></label>
        <input id="code" type="text" name="code" value="{{ old('code') }}" required placeholder="HOS-2024-XXXXX"
               class="block w-full px-3 py-2 border {{ $errors->has('code') ? 'border-red-300 ring-1 ring-red-300' : 'border-gray-300' }} rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        @error('code') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Hospital email <span class="text-red-500">*</span></label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required placeholder="admin@hospital.com"
               class="block w-full px-3 py-2 border {{ $errors->has('email') ? 'border-red-300 ring-1 ring-red-300' : 'border-gray-300' }} rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone number <span class="text-red-500">*</span></label>
        <input id="phone" type="tel" name="phone" value="{{ old('phone') }}" required placeholder="+1 234 567 8900"
               class="block w-full px-3 py-2 border {{ $errors->has('phone') ? 'border-red-300 ring-1 ring-red-300' : 'border-gray-300' }} rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        @error('phone') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="grid grid-cols-2 gap-3">
        <div>
            <label for="city" class="block text-sm font-medium text-gray-700 mb-1">City <span class="text-red-500">*</span></label>
            <input id="city" type="text" name="city" value="{{ old('city') }}" required placeholder="New York"
                   class="block w-full px-3 py-2 border {{ $errors->has('city') ? 'border-red-300 ring-1 ring-red-300' : 'border-gray-300' }} rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            @error('city') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="state" class="block text-sm font-medium text-gray-700 mb-1">State <span class="text-red-500">*</span></label>
            <input id="state" type="text" name="state" value="{{ old('state') }}" required placeholder="New York"
                   class="block w-full px-3 py-2 border {{ $errors->has('state') ? 'border-red-300 ring-1 ring-red-300' : 'border-gray-300' }} rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            @error('state') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>

    <div>
        <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Full address <span class="text-red-500">*</span></label>
        <input id="address" type="text" name="address" value="{{ old('address') }}" required placeholder="Street, area, zip code"
               class="block w-full px-3 py-2 border {{ $errors->has('address') ? 'border-red-300 ring-1 ring-red-300' : 'border-gray-300' }} rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        @error('address') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="grid grid-cols-2 gap-3">
        <div>
            <label for="contact_person" class="block text-sm font-medium text-gray-700 mb-1">Contact person name <span class="text-red-500">*</span></label>
            <input id="contact_person" type="text" name="contact_person" value="{{ old('contact_person') }}" required placeholder="Dr. James Wilson"
                   class="block w-full px-3 py-2 border {{ $errors->has('contact_person') ? 'border-red-300 ring-1 ring-red-300' : 'border-gray-300' }} rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            @error('contact_person') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="designation" class="block text-sm font-medium text-gray-700 mb-1">Designation <span class="text-red-500">*</span></label>
            <input id="designation" type="text" name="designation" value="{{ old('designation') }}" required placeholder="Medical Director"
                   class="block w-full px-3 py-2 border {{ $errors->has('designation') ? 'border-red-300 ring-1 ring-red-300' : 'border-gray-300' }} rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            @error('designation') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>

    <div>
        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password <span class="text-red-500">*</span></label>
        <input id="password" type="password" name="password" required placeholder="Min. 8 characters"
               class="block w-full px-3 py-2 border {{ $errors->has('password') ? 'border-red-300 ring-1 ring-red-300' : 'border-gray-300' }} rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm password <span class="text-red-500">*</span></label>
        <input id="password_confirmation" type="password" name="password_confirmation" required placeholder="Repeat password"
               class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
    </div>

    <button type="submit"
            class="w-full py-2.5 px-4 text-sm font-semibold text-white rounded-lg bg-cyan-600 hover:bg-cyan-700 transition focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2">
        Submit registration
    </button>
</form>

<p class="text-sm text-center text-gray-500 mt-6">
    Already have an account?
    <a href="{{ route('login') }}" class="text-blue-600 hover:underline font-medium">Sign in</a>
</p>
@endsection
