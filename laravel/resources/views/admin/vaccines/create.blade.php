@extends('admin.layouts.dashboard')
@section('page-title', 'Add Vaccine')
@section('content')
<a href="{{ route('admin.vaccines.index') }}" class="text-sm text-gray-500 hover:text-gray-700 mb-4 inline-flex items-center gap-1">&larr; Back to Vaccines</a>
<div class="bg-white rounded-xl border border-gray-200 p-6 max-w-2xl">
    <h2 class="text-xl font-semibold text-gray-900 mb-5">Add New Vaccine</h2>
    <form method="POST" action="{{ route('admin.vaccines.store') }}" class="space-y-4">
        @csrf
        <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Vaccine name *</label><input type="text" name="name" value="{{ old('name') }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent">@error('name')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror</div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Vaccine code *</label><input type="text" name="code" value="{{ old('code') }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent">@error('code')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror</div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Number of doses *</label><input type="number" name="doses" value="{{ old('doses') }}" min="1" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent">@error('doses')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror</div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Age range *</label><input type="text" name="age_range" value="{{ old('age_range') }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent">@error('age_range')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror</div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Vaccine type *</label><select name="type" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent"><option value="">Select type</option>@foreach(['Live attenuated','Inactivated','Recombinant','Conjugate','Toxoid','Combination'] as $type)<option value="{{ $type }}" {{ old('type') === $type ? 'selected' : '' }}>{{ $type }}</option>@endforeach</select>@error('type')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror</div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Manufacturer *</label><input type="text" name="manufacturer" value="{{ old('manufacturer') }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent">@error('manufacturer')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror</div>
        </div>
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Description</label><textarea name="description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent">{{ old('description') }}</textarea></div>
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Status *</label><select name="status" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"><option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option><option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option></select></div>
        <div class="flex gap-3 pt-2"><a href="{{ route('admin.vaccines.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-50">Cancel</a><button type="submit" class="px-4 py-2 bg-purple-600 text-white text-sm font-semibold rounded-lg hover:bg-purple-700">Save Vaccine</button></div>
    </form>
</div>
@endsection
