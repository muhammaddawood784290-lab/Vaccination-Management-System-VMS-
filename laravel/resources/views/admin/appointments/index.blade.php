@extends('admin.layouts.dashboard')
@section('page-title', 'Appointments')
@section('content')
<div class="mb-5"><h1 class="text-2xl font-semibold text-gray-900">Appointments</h1><p class="text-sm text-gray-500">Manage all vaccination appointments</p></div>
<div class="grid grid-cols-4 gap-4 mb-5">
    @foreach([['Total', $stats['total'], '#1C64F2'], ['Pending', $stats['pending'], '#D97706'], ['Confirmed', $stats['confirmed'], '#0891B2'], ['Completed', $stats['completed'], '#057A55']] as [$label, $count, $color])
        <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background:{{ $color }}18"><span class="text-sm font-bold" style="color:{{ $color }}">{{ $count }}</span></div>
            <div><p class="text-lg font-bold text-gray-900">{{ number_format($count) }}</p><p class="text-xs text-gray-500">{{ $label }}</p></div>
        </div>
    @endforeach
</div>
<div class="bg-white rounded-xl border border-gray-200">
    <div class="p-4 border-b border-gray-100 flex gap-3">
        <form method="GET" class="flex-1 max-w-sm"><input type="text" name="search" value="{{ request('search') }}" placeholder="Search by child, hospital..." class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500"></form>
        <form method="GET"><select name="status" onchange="this.form.submit()" class="px-3 py-2 border border-gray-300 rounded-lg text-sm"><option value="all" {{ request('status','all')==='all'?'selected':'' }}>All status</option>@foreach(['pending','approved','confirmed','completed','cancelled','rejected','no_show'] as $s)<option value="{{ $s }}" {{ request('status')===$s?'selected':'' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>@endforeach</select></form>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left"><tr><th class="px-5 py-3 font-medium text-gray-500">Child</th><th class="px-5 py-3 font-medium text-gray-500">Vaccine</th><th class="px-5 py-3 font-medium text-gray-500">Hospital</th><th class="px-5 py-3 font-medium text-gray-500">Date & Time</th><th class="px-5 py-3 font-medium text-gray-500">Status</th><th class="px-5 py-3 font-medium text-gray-500">Actions</th></tr></thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($appointments as $apt)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3"><p class="font-medium text-gray-900">{{ $apt->child->name ?? 'N/A' }}</p><p class="text-xs text-gray-400">{{ $apt->parent->name ?? '' }}</p></td>
                        <td class="px-5 py-3"><p>{{ $apt->vaccine->name ?? 'N/A' }}</p><p class="text-xs text-gray-400">{{ $apt->dose }}</p></td>
                        <td class="px-5 py-3 text-gray-600">{{ $apt->hospital->name ?? 'N/A' }}</td>
                        <td class="px-5 py-3"><p class="font-medium">{{ $apt->appointment_date->format('M d, Y') }}</p><p class="text-xs text-gray-400">{{ $apt->appointment_time }}</p></td>
                        <td class="px-5 py-3">@php
                            $badgeClass = match($apt->status) {
                                'completed' => 'bg-green-100 text-green-800',
                                'confirmed' => 'bg-blue-100 text-blue-800',
                                'pending' => 'bg-yellow-100 text-yellow-800',
                                'cancelled' => 'bg-gray-100 text-gray-800',
                                'rejected' => 'bg-red-100 text-red-800',
                                'no_show' => 'bg-red-100 text-red-800',
                                default => 'bg-gray-100 text-gray-800',
                            };
                        @endphp<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badgeClass }}">{{ ucfirst(str_replace('_',' ',$apt->status)) }}</span></td>
                        <td class="px-5 py-3"><a href="{{ route('admin.appointments.show', $apt) }}" class="text-purple-600 hover:underline text-xs">View</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-8 text-center text-gray-400">No appointments found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4">{{ $appointments->links() }}</div>
</div>
@endsection
