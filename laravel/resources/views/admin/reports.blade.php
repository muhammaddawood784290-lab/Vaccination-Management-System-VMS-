@extends('admin.layouts.dashboard')
@section('page-title', 'Reports')
@section('content')
<div class="mb-5"><h1 class="text-2xl font-semibold text-gray-900">Reports</h1><p class="text-sm text-gray-500">Analytics and insights across the VMS network</p></div>
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
    @foreach([['Total Records', $totalRecords, '#1C64F2'], ['Total Children', $totalChildren, '#057A55'], ['Active Hospitals', $totalHospitals, '#0891B2'], ['Total Appointments', $totalAppointments, '#D97706']] as [$label, $val, $c])
        <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center gap-3"><div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background:{{ $c }}18"><span class="text-sm font-bold" style="color:{{ $c }}">{{ $val }}</span></div><div><p class="text-lg font-bold text-gray-900">{{ number_format($val) }}</p><p class="text-xs text-gray-500">{{ $label }}</p></div></div>
    @endforeach
</div>
<div class="bg-white rounded-xl border border-gray-200 mb-5">
    <div class="p-5 border-b border-gray-100"><h3 class="text-sm font-semibold text-gray-900">Hospital Performance</h3></div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm"><thead class="bg-gray-50 text-left"><tr><th class="px-5 py-3 font-medium text-gray-500">Hospital</th><th class="px-5 py-3 font-medium text-gray-500">City</th><th class="px-5 py-3 font-medium text-gray-500">Total Records</th><th class="px-5 py-3 font-medium text-gray-500">Status</th></tr></thead>
            <tbody class="divide-y divide-gray-100">@forelse($hospitalPerformance as $h)<tr><td class="px-5 py-3 font-medium text-gray-900">{{ $h->name }}</td><td class="px-5 py-3 text-gray-600">{{ $h->city }}</td><td class="px-5 py-3 font-mono font-semibold">{{ number_format($h->vaccination_records_count) }}</td><td class="px-5 py-3"><span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 text-xs font-medium rounded-full border bg-emerald-50 text-emerald-700 border-emerald-200"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Active</span></td></tr>@empty<tr><td colspan="4" class="px-5 py-8 text-center text-gray-400">No data yet</td></tr>@endforelse</tbody></table>
    </div>
</div>
<div class="bg-white rounded-xl border border-gray-200">
    <div class="p-5 border-b border-gray-100"><h3 class="text-sm font-semibold text-gray-900">Recent Vaccination Records</h3></div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm"><thead class="bg-gray-50 text-left"><tr><th class="px-5 py-3 font-medium text-gray-500">Child</th><th class="px-5 py-3 font-medium text-gray-500">Vaccine</th><th class="px-5 py-3 font-medium text-gray-500">Hospital</th><th class="px-5 py-3 font-medium text-gray-500">Date</th></tr></thead>
            <tbody class="divide-y divide-gray-100">@forelse($recentRecords as $r)<tr><td class="px-5 py-3 font-medium text-gray-900">{{ $r->child->name ?? 'N/A' }}</td><td class="px-5 py-3">{{ $r->vaccine->name ?? 'N/A' }}</td><td class="px-5 py-3 text-gray-600">{{ $r->hospital->name ?? 'N/A' }}</td><td class="px-5 py-3">{{ $r->administered_at?->format('M d, Y') ?? 'N/A' }}</td></tr>@empty<tr><td colspan="4" class="px-5 py-8 text-center text-gray-400">No records yet</td></tr>@endforelse</tbody></table>
    </div>
</div>
@endsection
