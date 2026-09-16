@extends('admin.layouts.dashboard')
@section('page-title', 'System Dashboard')
@section('content')
<div class="space-y-6">
    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background:#EBF5FF">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#1C64F2" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($totalChildren) }}</p>
                    <p class="text-xs text-gray-500">Total Children</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background:#F0FDF4">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#057A55" stroke-width="2"><path d="M9 12l2 2 4-4"/><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($completedVaccinations) }}</p>
                    <p class="text-xs text-gray-500">Total Vaccinations</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background:#FFFBEB">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#D97706" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($upcomingAppointments) }}</p>
                    <p class="text-xs text-gray-500">Upcoming Appointments</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background:#FEF2F2">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#C81E1E" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($pendingRequests) }}</p>
                    <p class="text-xs text-gray-500">Pending Requests</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Bottom Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        {{-- Hospital Overview --}}
        <div class="bg-white rounded-xl border border-gray-200">
            <div class="p-5 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-900">Hospital Overview</h3>
                <a href="{{ route('admin.hospitals.index') }}" class="text-xs text-purple-600 hover:underline">View all</a>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($topHospitals as $hospital)
                    <div class="flex items-center gap-3 px-5 py-3">
                        <div class="w-8 h-8 rounded-lg bg-purple-100 flex items-center justify-center text-purple-600 text-xs font-bold">{{ substr($hospital->name, 0, 1) }}</div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $hospital->name }}</p>
                            <p class="text-xs text-gray-400">{{ $hospital->city }}</p>
                        </div>
                        @php
                            $badgeClass = match($hospital->status) {
                                'active' => 'bg-green-100 text-green-800',
                                'pending' => 'bg-yellow-100 text-yellow-800',
                                default => 'bg-gray-100 text-gray-800',
                            };
                        @endphp
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badgeClass }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ match($hospital->status) { 'active' => 'bg-green-500', 'pending' => 'bg-yellow-500', default => 'bg-gray-500' } }}"></span>
                            {{ ucfirst($hospital->status) }}
                        </span>
                    </div>
                @empty
                    <div class="px-5 py-4 text-sm text-gray-400">No hospitals yet</div>
                @endforelse
            </div>
        </div>

        {{-- Pending Requests --}}
        <div class="bg-white rounded-xl border border-gray-200">
            <div class="p-5 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-900">Pending Requests</h3>
                <a href="{{ route('admin.requests.index') }}" class="text-xs text-purple-600 hover:underline">View all</a>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($recentRequests->where('status', 'pending') as $req)
                    <div class="px-5 py-3">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $req->user->name ?? 'Unknown' }}</p>
                                <p class="text-xs text-gray-400">{{ ucfirst(str_replace('_', ' ', $req->type)) }}</p>
                            </div>
                            <span class="text-xs text-gray-400">{{ $req->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                @empty
                    <div class="px-5 py-4 text-sm text-gray-400">No pending requests</div>
                @endforelse
            </div>
        </div>

        {{-- Vaccine Usage --}}
        <div class="bg-white rounded-xl border border-gray-200">
            <div class="p-5 border-b border-gray-100">
                <h3 class="text-sm font-semibold text-gray-900">Vaccine Usage</h3>
                <p class="text-xs text-gray-400 mt-0.5">Top vaccines by doses administered</p>
            </div>
            <div class="p-5 space-y-3">
                @php $maxCount = $vaccineUsage->max('count') ?: 1; @endphp
                @forelse($vaccineUsage as $vu)
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-medium text-gray-700">{{ $vu->vaccine->name ?? 'Unknown' }}</span>
                            <span class="text-xs text-gray-400">{{ $vu->count }}</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-1.5">
                            <div class="bg-purple-500 h-1.5 rounded-full" style="width: {{ min(($vu->count / $maxCount) * 100, 100) }}%"></div>
                        </div>
                    </div>
                @empty
                    <div class="text-sm text-gray-400">No vaccination data yet</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Recent Activity --}}
    <div class="bg-white rounded-xl border border-gray-200">
        <div class="p-5 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-900">Recent Activity</h3>
        </div>
        <div class="divide-y divide-gray-100">
            @forelse($recentRecords->take(5) as $record)
                <div class="flex items-start gap-4 px-5 py-3">
                    <div class="w-2 h-2 rounded-full mt-2 bg-emerald-500 flex-shrink-0"></div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900">Vaccination completed</p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $record->child->name ?? 'Unknown' }} — {{ $record->vaccine->name ?? 'Unknown' }} at {{ $record->hospital->name ?? 'Unknown' }}</p>
                    </div>
                    <span class="text-xs text-gray-400 flex-shrink-0">{{ $record->administered_at?->diffForHumans() ?? '' }}</span>
                </div>
            @empty
                <div class="px-5 py-4 text-sm text-gray-400">No recent activity</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
