@extends('parent.layouts.dashboard')
@section('page-title', 'Dashboard')
@section('content')
<div class="space-y-6">
    {{-- Welcome + KPI Cards --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-xl font-bold text-gray-900">Welcome back, {{ auth()->user()->name }}!</h2>
        <p class="text-sm text-gray-500 mt-1">Here's an overview of your children's vaccination status.</p>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-emerald-100">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalChildren }}</p>
                    <p class="text-xs text-gray-500">My Children</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-blue-100">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="2"><path d="M9 12l2 2 4-4"/><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalVaccinations }}</p>
                    <p class="text-xs text-gray-500">Vaccinations Done</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-amber-100">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#D97706" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $upcomingAppointments }}</p>
                    <p class="text-xs text-gray-500">Upcoming</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-red-100">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $pendingRequests }}</p>
                    <p class="text-xs text-gray-500">Pending</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Next Appointment --}}
    @if($nextAppointment)
        <div class="bg-gradient-to-r from-emerald-600 to-emerald-700 rounded-xl p-6 text-white">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                </div>
                <div class="flex-1">
                    <p class="text-sm text-emerald-100">Next Appointment</p>
                    <p class="text-lg font-bold">{{ $nextAppointment->child->name ?? 'Unknown' }} — {{ $nextAppointment->vaccine->name ?? 'Vaccination' }}</p>
                    <p class="text-sm text-emerald-100">{{ $nextAppointment->appointment_date->format('M d, Y') }} at {{ $nextAppointment->appointment_time }} · {{ $nextAppointment->hospital->name ?? 'Hospital' }}</p>
                </div>
                <a href="{{ route('parent.appointments.show', $nextAppointment) }}" class="px-4 py-2 bg-white text-emerald-700 rounded-lg text-sm font-medium hover:bg-emerald-50 transition">View Details</a>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- My Children --}}
        <div class="bg-white rounded-xl border border-gray-200">
            <div class="p-5 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-900">My Children</h3>
                <a href="{{ route('parent.children.index') }}" class="text-xs text-emerald-600 hover:underline">View all</a>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($children as $child)
                    <div class="flex items-center gap-3 px-5 py-3">
                        <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 font-bold text-sm">
                            {{ substr($child->name, 0, 1) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900">{{ $child->name }}</p>
                            <p class="text-xs text-gray-400">{{ ucfirst($child->gender) }} · {{ $child->date_of_birth->age }} yrs · {{ $child->vaccination_records_count }} vaccinations</p>
                        </div>
                        <a href="{{ route('parent.children.show', $child) }}" class="text-xs text-emerald-600 hover:underline">View</a>
                    </div>
                @empty
                    <div class="px-5 py-8 text-center">
                        <p class="text-sm text-gray-400">No children registered yet.</p>
                        <a href="{{ route('parent.children.create') }}" class="mt-2 inline-block text-sm text-emerald-600 hover:underline">Add your first child</a>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Recent Vaccinations --}}
        <div class="bg-white rounded-xl border border-gray-200">
            <div class="p-5 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-900">Recent Vaccinations</h3>
                <a href="{{ route('parent.vaccinations.history') }}" class="text-xs text-emerald-600 hover:underline">View all</a>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($recentVaccinations as $record)
                    <div class="flex items-start gap-3 px-5 py-3">
                        <div class="w-2 h-2 rounded-full mt-2 bg-emerald-500 flex-shrink-0"></div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900">{{ $record->child->name ?? 'Unknown' }}</p>
                            <p class="text-xs text-gray-500">{{ $record->vaccine->name ?? 'Vaccine' }} · {{ $record->hospital->name ?? 'Hospital' }}</p>
                        </div>
                        <span class="text-xs text-gray-400 flex-shrink-0">{{ $record->administered_at?->format('M d, Y') ?? '' }}</span>
                    </div>
                @empty
                    <div class="px-5 py-8 text-center">
                        <p class="text-sm text-gray-400">No vaccinations yet.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">Quick Actions</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <a href="{{ route('parent.children.create') }}" class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 hover:border-emerald-300 hover:bg-emerald-50 transition">
                <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></div>
                <span class="text-sm font-medium text-gray-700">Add Child</span>
            </a>
            <a href="{{ route('parent.appointments.create') }}" class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 hover:border-emerald-300 hover:bg-emerald-50 transition">
                <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div>
                <span class="text-sm font-medium text-gray-700">Book Appointment</span>
            </a>
            <a href="{{ route('parent.hospitals.index') }}" class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 hover:border-emerald-300 hover:bg-emerald-50 transition">
                <div class="w-8 h-8 rounded-lg bg-purple-100 flex items-center justify-center"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#7C3AED" stroke-width="2"><path d="M3 21h18"/><path d="M5 21V7l7-4 7 4v14"/></svg></div>
                <span class="text-sm font-medium text-gray-700">Find Hospitals</span>
            </a>
            <a href="{{ route('parent.vaccinations.history') }}" class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 hover:border-emerald-300 hover:bg-emerald-50 transition">
                <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#D97706" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></div>
                <span class="text-sm font-medium text-gray-700">View History</span>
            </a>
        </div>
    </div>
</div>
@endsection
