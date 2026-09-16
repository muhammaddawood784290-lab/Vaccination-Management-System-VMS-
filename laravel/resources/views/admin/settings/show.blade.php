@extends('admin.layouts.dashboard')

@section('page-title', 'Settings')

@section('content')
<div class="max-w-2xl space-y-6">
    {{-- Account Summary (read-only) --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-5">Account Settings</h2>
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Name</label>
                <p class="text-gray-900">{{ $user->name }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Email</label>
                <p class="text-gray-900">{{ $user->email }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Phone</label>
                <p class="text-gray-900">{{ $user->phone ?? 'Not set' }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">City</label>
                <p class="text-gray-900">{{ $user->city ?? 'Not set' }}</p>
            </div>
            <p class="text-xs text-gray-400 mt-2">Contact details are managed via the Profile page.</p>
        </div>
    </div>

    {{-- Change Password --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-5">Security</h2>
        <form method="POST" action="{{ route('admin.settings.password') }}" class="space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Current Password *</label>
                <input type="password" name="current_password" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500" />
                @error('current_password')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">New Password *</label>
                <input type="password" name="password" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500" />
                @error('password')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password *</label>
                <input type="password" name="password_confirmation" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500" />
            </div>
            <button type="submit"
                class="px-4 py-2 bg-purple-600 text-white text-sm font-semibold rounded-lg hover:bg-purple-700">
                Change Password
            </button>
        </form>
    </div>
</div>
@endsection
