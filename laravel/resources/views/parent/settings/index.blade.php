@extends('parent.layouts.dashboard')
@section('page-title', 'Settings')
@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-6">Change Password</h2>
        <form method="POST" action="{{ route('parent.settings.password') }}" class="space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Current Password *</label>
                <input type="password" name="current_password" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('current_password') border-red-500 @enderror">
                @error('current_password') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">New Password *</label>
                <input type="password" name="password" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('password') border-red-500 @enderror">
                @error('password') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password *</label>
                <input type="password" name="password_confirmation" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            </div>
            <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-medium hover:bg-emerald-700 transition">Update Password</button>
        </form>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Account Information</h2>
        <div class="space-y-2">
            <p class="text-sm text-gray-600"><span class="font-medium text-gray-700">Name:</span> {{ auth()->user()->name }}</p>
            <p class="text-sm text-gray-600"><span class="font-medium text-gray-700">Email:</span> {{ auth()->user()->email }}</p>
            <p class="text-sm text-gray-600"><span class="font-medium text-gray-700">Role:</span> Parent</p>
            <p class="text-sm text-gray-600"><span class="font-medium text-gray-700">Joined:</span> {{ auth()->user()->created_at->format('M d, Y') }}</p>
        </div>
        <a href="{{ route('parent.profile') }}" class="mt-4 inline-block text-sm text-emerald-600 hover:underline">Edit Profile →</a>
    </div>
</div>
@endsection
