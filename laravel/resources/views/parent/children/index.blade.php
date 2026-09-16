@extends('parent.layouts.dashboard')
@section('page-title', 'My Children')
@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">My Children</h2>
            <p class="text-sm text-gray-500">Manage your children's vaccination profiles</p>
        </div>
        <a href="{{ route('parent.children.create') }}" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-medium hover:bg-emerald-700 transition">+ Add Child</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($children as $child)
            <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md transition">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 font-bold text-lg">
                        {{ substr($child->name, 0, 1) }}
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">{{ $child->name }}</h3>
                        <p class="text-xs text-gray-400">{{ ucfirst($child->gender) }} · Born {{ $child->date_of_birth->format('M d, Y') }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2 text-xs text-gray-500 mb-3">
                    @if($child->blood_group)
                        <span class="px-2 py-0.5 bg-blue-50 text-blue-700 rounded">{{ $child->blood_group }}</span>
                    @endif
                    <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 rounded">{{ $child->vaccination_records_count }} vaccinations</span>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('parent.children.show', $child) }}" class="flex-1 px-3 py-2 bg-gray-100 text-gray-700 rounded-lg text-xs font-medium text-center hover:bg-gray-200 transition">View Details</a>
                    <a href="{{ route('parent.children.edit', $child) }}" class="flex-1 px-3 py-2 bg-emerald-50 text-emerald-700 rounded-lg text-xs font-medium text-center hover:bg-emerald-100 transition">Edit</a>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-xl border border-gray-200 p-12 text-center">
                <div class="w-16 h-16 rounded-full bg-emerald-100 flex items-center justify-center mx-auto mb-4">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-1">No Children Yet</h3>
                <p class="text-sm text-gray-500 mb-4">Add your first child to start managing vaccinations.</p>
                <a href="{{ route('parent.children.create') }}" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-medium hover:bg-emerald-700 transition">+ Add Child</a>
            </div>
        @endforelse
    </div>
</div>
@endsection
