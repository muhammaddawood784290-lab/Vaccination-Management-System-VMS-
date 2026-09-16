@extends('hospital.layouts.dashboard')
@section('page-title', 'Dashboard')
@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-xl font-bold text-gray-900">{{ auth()->user()->hospital->name ?? 'Hospital' }}</h2>
        <p class="text-sm text-gray-500 mt-1">Welcome to your hospital dashboard</p>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-blue-100"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div>
                <div><p class="text-2xl font-bold text-gray-900">{{ $todayAppointments->count() }}</p><p class="text-xs text-gray-500">Today's Appointments</p></div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-amber-100"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#D97706" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg></div>
                <div><p class="text-2xl font-bold text-gray-900">{{ $pendingCount }}</p><p class="text-xs text-gray-500">Pending Approval</p></div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-green-100"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2"><path d="M9 12l2 2 4-4"/><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"/></svg></div>
                <div><p class="text-2xl font-bold text-gray-900">{{ $completedToday }}</p><p class="text-xs text-gray-500">Vaccinated Today</p></div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-purple-100"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#7C3AED" stroke-width="2"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0016.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 002 8.5c0 2.3 1.5 4.05 3 5.5l7 7z"/></svg></div>
                <div><p class="text-2xl font-bold text-gray-900">{{ $inventoryCount }}</p><p class="text-xs text-gray-500">Vaccines in Stock</p></div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Today's Appointments --}}
        <div class="bg-white rounded-xl border border-gray-200">
            <div class="p-5 border-b border-gray-100">
                <h3 class="text-sm font-semibold text-gray-900">Today's Appointments</h3>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($todayAppointments as $apt)
                    <div class="flex items-center gap-3 px-5 py-3">
                        <div class="w-2 h-2 rounded-full {{ $apt->status === 'confirmed' ? 'bg-green-500' : ($apt->status === 'pending' ? 'bg-yellow-500' : 'bg-gray-400') }}"></div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900">{{ $apt->child->name ?? 'N/A' }}</p>
                            <p class="text-xs text-gray-400">{{ $apt->vaccine->name ?? '' }} · {{ $apt->appointment_time }} · {{ ucfirst(str_replace('_', ' ', $apt->status)) }}</p>
                        </div>
                        <a href="{{ route('hospital.appointments.show', $apt) }}" class="text-xs text-blue-600 hover:underline">View</a>
                    </div>
                @empty
                    <div class="px-5 py-8 text-center text-sm text-gray-400">No appointments today</div>
                @endforelse
            </div>
        </div>

        {{-- Low Stock Alert --}}
        <div class="bg-white rounded-xl border border-gray-200">
            <div class="p-5 border-b border-gray-100">
                <h3 class="text-sm font-semibold text-gray-900">Low Stock Alert</h3>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($lowStockVaccines as $item)
                    <div class="flex items-center gap-3 px-5 py-3">
                        <div class="w-2 h-2 rounded-full bg-red-500"></div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">{{ $item->vaccine->name ?? 'Vaccine' }}</p>
                            <p class="text-xs text-gray-400">{{ $item->available }} remaining</p>
                        </div>
                        <span class="text-xs px-2 py-0.5 rounded-full {{ $item->available <= 2 ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700' }}">{{ $item->available <= 2 ? 'Critical' : 'Low' }}</span>
                    </div>
                @empty
                    <div class="px-5 py-8 text-center text-sm text-gray-400">All vaccines adequately stocked</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Upcoming Appointments --}}
    <div class="bg-white rounded-xl border border-gray-200">
        <div class="p-5 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-900">Upcoming Appointments</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50"><tr>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500">Child</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500">Parent</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500">Vaccine</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500">Date</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500">Action</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($upcomingAppointments as $apt)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-3 text-sm font-medium text-gray-900">{{ $apt->child->name ?? 'N/A' }}</td>
                            <td class="px-5 py-3 text-sm text-gray-600">{{ $apt->parent->name ?? 'N/A' }}</td>
                            <td class="px-5 py-3 text-sm text-gray-600">{{ $apt->vaccine->name ?? 'N/A' }}</td>
                            <td class="px-5 py-3 text-sm text-gray-600">{{ $apt->appointment_date?->format('M d, Y') }} {{ $apt->appointment_time }}</td>
                            <td class="px-5 py-3"><a href="{{ route('hospital.appointments.show', $apt) }}" class="text-xs text-blue-600 hover:underline">View</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-8 text-center text-sm text-gray-400">No upcoming appointments</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
