@extends('admin.layouts.dashboard')
@section('page-title', 'Profile')
@section('content')
<div class="max-w-2xl space-y-6">
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-5">Profile Information</h2>
        <form method="POST" action="{{ route('admin.profile.update') }}" class="space-y-4">
            @csrf @method('PUT')
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Name *</label><input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500">@error('name')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror</div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Email *</label><input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500">@error('email')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror</div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Phone</label><input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">City</label><input type="text" name="city" value="{{ old('city', $user->city) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500"></div>
            <button type="submit" class="px-4 py-2 bg-purple-600 text-white text-sm font-semibold rounded-lg hover:bg-purple-700">Save Changes</button>
        </form>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-5">Change Password</h2>
        <form method="POST" action="{{ route('admin.profile.password') }}" class="space-y-4">
            @csrf @method('PUT')
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Current Password *</label><input type="password" name="current_password" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500">@error('current_password')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror</div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">New Password *</label><input type="password" name="password" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500">@error('password')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror</div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password *</label><input type="password" name="password_confirmation" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500"></div>
            <button type="submit" class="px-4 py-2 bg-purple-600 text-white text-sm font-semibold rounded-lg hover:bg-purple-700">Change Password</button>
        </form>
    </div>
</div>
@endsection
