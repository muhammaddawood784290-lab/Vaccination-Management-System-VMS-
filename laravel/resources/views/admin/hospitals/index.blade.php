@extends('admin.layouts.dashboard')
@section('page-title', 'Hospitals')
@section('content')
<div class="flex items-start justify-between mb-5">
    <div><h1 class="text-2xl font-semibold text-gray-900">Hospitals</h1><p class="text-sm text-gray-500">Partner hospitals in the VMS network</p></div>
    <a href="{{ route('admin.hospitals.create') }}" class="px-4 py-2 bg-purple-600 text-white text-sm font-semibold rounded-lg hover:bg-purple-700 transition">+ Add Hospital</a>
</div>
<div class="grid grid-cols-3 gap-4 mb-5">
    <div class="bg-white rounded-xl border border-gray-200 p-5">Stat Card</div><path d="M5 21V5a2 2 0 012-2h10a2 2 0 012 2v16"/></svg>' />
    <div class="bg-white rounded-xl border border-gray-200 p-5">Stat Card</div></svg>' />
    <div class="bg-white rounded-xl border border-gray-200 p-5">Stat Card</div><polyline points="12 6 12 12 16 14"/></svg>' />
</div>
<div class="bg-white rounded-xl border border-gray-200">
    <div class="p-4 border-b border-gray-100 flex gap-3">
        <form method="GET" class="flex-1 max-w-sm"><input type="text" name="search" value="{{ request('search') }}" placeholder="Search hospitals..." class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent"></form>
        <form method="GET"><select name="status" onchange="this.form.submit()" class="px-3 py-2 border border-gray-300 rounded-lg text-sm"><option value="all" {{ request('status', 'all') === 'all' ? 'selected' : '' }}>All status</option><option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option><option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option><option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option></select></form>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left"><tr><th class="px-5 py-3 font-medium text-gray-500">Hospital</th><th class="px-5 py-3 font-medium text-gray-500">Location</th><th class="px-5 py-3 font-medium text-gray-500">Contact</th><th class="px-5 py-3 font-medium text-gray-500">Records</th><th class="px-5 py-3 font-medium text-gray-500">Status</th><th class="px-5 py-3 font-medium text-gray-500">Actions</th></tr></thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($hospitals as $hospital)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3"><a href="{{ route('admin.hospitals.show', $hospital) }}" class="font-medium text-gray-900 hover:text-purple-600">{{ $hospital->name }}</a><p class="text-xs text-gray-400 font-mono">{{ $hospital->code }}</p></td>
                        <td class="px-5 py-3 text-gray-600">{{ $hospital->city }}, {{ $hospital->state }}</td>
                        <td class="px-5 py-3 text-gray-600">{{ $hospital->contact_person }}</td>
                        <td class="px-5 py-3 font-mono font-semibold">{{ number_format($hospital->vaccination_records_count) }}</td>
                        <td class="px-5 py-3">@php
                            $badgeClass = match($hospital->status) {
                                'active' => 'bg-green-100 text-green-800',
                                'pending' => 'bg-yellow-100 text-yellow-800',
                                default => 'bg-gray-100 text-gray-800',
                            };
                        @endphp<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badgeClass }}">{{ ucfirst($hospital->status) }}</span></td>
                        <td class="px-5 py-3 flex gap-2">
                            @if($hospital->status === 'pending')
                                <form method="POST" action="{{ route('admin.hospitals.approve', $hospital) }}">@csrf<button class="text-emerald-600 hover:underline text-xs">Approve</button></form>
                            @endif
                            <a href="{{ route('admin.hospitals.edit', $hospital) }}" class="text-purple-600 hover:underline text-xs">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-8 text-center text-gray-400">No hospitals found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4">{{ $hospitals->links() }}</div>
</div>
@endsection
