@extends('hospital.layouts.dashboard')
@section('page-title', 'Hospital Profile')
@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-6">Hospital Information</h2>
        <div class="grid grid-cols-2 gap-4">
            <div><p class="text-xs text-gray-500">Hospital Name</p><p class="text-sm font-medium text-gray-900">{{ $user->hospital->name ?? 'N/A' }}</p></div>
            <div><p class="text-xs text-gray-500">Code</p><p class="text-sm font-medium text-gray-900">{{ $user->hospital->code ?? 'N/A' }}</p></div>
            <div><p class="text-xs text-gray-500">Email</p><p class="text-sm font-medium text-gray-900">{{ $user->hospital->email ?? 'N/A' }}</p></div>
            <div><p class="text-xs text-gray-500">Phone</p><p class="text-sm font-medium text-gray-900">{{ $user->hospital->phone ?? 'N/A' }}</p></div>
            <div><p class="text-xs text-gray-500">City</p><p class="text-sm font-medium text-gray-900">{{ $user->hospital->city ?? 'N/A' }}</p></div>
            <div><p class="text-xs text-gray-500">State</p><p class="text-sm font-medium text-gray-900">{{ $user->hospital->state ?? 'N/A' }}</p></div>
            <div class="col-span-2"><p class="text-xs text-gray-500">Address</p><p class="text-sm font-medium text-gray-900">{{ $user->hospital->address ?? 'N/A' }}</p></div>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-6">Account Information</h2>
        <form method="POST" action="{{ route('hospital.profile.update') }}" class="space-y-4">
            @csrf @method('PUT')
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Contact Person *</label><input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror">@error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror</div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Email *</label><input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror">@error('email') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror</div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Phone</label><input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></div>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition">Save Changes</button>
        </form>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-6">Change Password</h2>
        <form method="POST" action="{{ route('hospital.profile.password') }}" class="space-y-4">
            @csrf @method('PUT')
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Current Password *</label><input type="password" name="current_password" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('current_password') border-red-500 @enderror">@error('current_password') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror</div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">New Password *</label><input type="password" name="password" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-500 @enderror">@error('password') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror</div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password *</label><input type="password" name="password_confirmation" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></div>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition">Change Password</button>
        </form>
    </div>
</div>
@endsection
