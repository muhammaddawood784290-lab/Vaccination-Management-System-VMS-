@extends('auth.layout')
@section('auth-content')
<h1 class="text-2xl font-bold text-gray-900 mb-1">Welcome back</h1>
<p class="text-sm text-gray-500 mb-6">Sign in to your account to continue</p>

{{-- Role Tabs --}}
<div class="bg-white rounded-[10px] p-1 flex gap-1 mb-6 border border-gray-200 shadow-sm">
    <a href="{{ route('login') }}?role=parent"
       class="flex-1 py-2 text-xs font-semibold rounded-lg capitalize transition {{ request('role', 'parent') === 'parent' ? 'bg-gray-900 text-white shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
        Parent
    </a>
    <a href="{{ route('login') }}?role=hospital"
       class="flex-1 py-2 text-xs font-semibold rounded-lg capitalize transition {{ request('role') === 'hospital' ? 'bg-gray-900 text-white shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
        Hospital
    </a>
    <a href="{{ route('login') }}?role=admin"
       class="flex-1 py-2 text-xs font-semibold rounded-lg capitalize transition {{ request('role') === 'admin' ? 'bg-gray-900 text-white shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
        Admin
    </a>
</div>

{{-- Demo Credentials --}}
@php
    $role = request('role', 'parent');
    $demoCredentials = [
        'parent' => ['email' => 'sarah@example.com', 'password' => 'password'],
        'hospital' => ['email' => 'citygeneral@vms.permetheon.com', 'password' => 'password'],
        'admin' => ['email' => 'admin@vms.permetheon.com', 'password' => 'password'],
    ];
@endphp
<div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-5">
    <p class="text-xs text-blue-800 font-medium mb-1">Demo credentials ({{ $role }})</p>
    <p class="text-xs text-blue-600 font-mono">{{ $demoCredentials[$role]['email'] }}</p>
    <p class="text-xs text-blue-600 font-mono">{{ $demoCredentials[$role]['password'] }}</p>
</div>

{{-- Login Form --}}
<form method="POST" action="{{ route('login.submit') }}" class="space-y-4">
    @csrf
    <input type="hidden" name="role" value="{{ $role }}">

    {{-- Email --}}
    <div>
        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
            Email address <span class="text-red-500">*</span>
        </label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-4 w-4 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                    <polyline points="22,6 12,13 2,6"/>
                </svg>
            </div>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                   placeholder="you@example.com"
                   class="block w-full pl-10 pr-3 py-2 border {{ $errors->has('email') ? 'border-red-300 ring-1 ring-red-300' : 'border-gray-300' }} rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent placeholder-gray-400">
        </div>
        @error('email')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Password --}}
    <div>
        <div class="flex justify-between mb-1">
            <label for="password" class="block text-sm font-medium text-gray-700">
                Password <span class="text-red-500">*</span>
            </label>
        </div>
        <input id="password" type="password" name="password" required
               placeholder="••••••••"
               class="block w-full px-3 py-2 border {{ $errors->has('password') ? 'border-red-300 ring-1 ring-red-300' : 'border-gray-300' }} rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent placeholder-gray-400">
        @error('password')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Submit --}}
    @php
        $roleColors = [
            'parent' => 'bg-blue-600 hover:bg-blue-700',
            'hospital' => 'bg-cyan-600 hover:bg-cyan-700',
            'admin' => 'bg-purple-600 hover:bg-purple-700',
        ];
    @endphp
    <button type="submit"
            class="w-full py-2.5 px-4 text-sm font-semibold text-white rounded-lg transition focus:outline-none focus:ring-2 focus:ring-offset-2 {{ $roleColors[$role] ?? 'bg-blue-600 hover:bg-blue-700' }}">
        Sign in as {{ ucfirst($role) }}
    </button>
</form>

{{-- Registration Links --}}
<div class="mt-6 pt-6 border-t border-gray-200">
    <p class="text-sm text-gray-500 text-center mb-3">New to VMS?</p>
    <div class="grid grid-cols-2 gap-3">
        <a href="{{ route('register.parent') }}"
           class="py-2 px-3 text-xs font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition text-center">
            Register as Parent
        </a>
        <a href="{{ route('register.hospital') }}"
           class="py-2 px-3 text-xs font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition text-center">
            Register Hospital
        </a>
    </div>
</div>
@endsection
