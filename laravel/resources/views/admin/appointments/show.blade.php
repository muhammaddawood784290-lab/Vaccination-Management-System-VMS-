@extends('admin.layouts.dashboard')
@section('page-title', 'Appointment Details')
@section('content')
<a href="{{ route('admin.appointments.index') }}" class="text-sm text-gray-500 hover:text-gray-700 mb-4 inline-flex items-center gap-1">&larr; Back to Appointments</a>
<div class="bg-white rounded-xl border border-gray-200 p-6 max-w-2xl">
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3"><div class="w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center text-purple-600 font-bold">{{ substr($appointment->child->name ?? 'C', 0, 1) }}</div><div><h2 class="text-xl font-semibold text-gray-900">{{ $appointment->child->name ?? 'N/A' }}</h2><p class="text-sm text-gray-500">Parent: {{ $appointment->parent->name ?? 'N/A' }}</p></div></div>
        @php
            $badgeClass = match($appointment->status) {
                'completed' => 'bg-green-100 text-green-800',
                'confirmed' => 'bg-blue-100 text-blue-800',
                'pending' => 'bg-yellow-100 text-yellow-800',
                'cancelled' => 'bg-gray-100 text-gray-800',
                'rejected' => 'bg-red-100 text-red-800',
                'no_show' => 'bg-red-100 text-red-800',
                default => 'bg-gray-100 text-gray-800',
            };
        @endphp
        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badgeClass }}">{{ ucfirst(str_replace('_',' ',$appointment->status)) }}</span>
    </div>
    <div class="grid grid-cols-2 gap-4 text-sm mb-6">
        @foreach([['Vaccine', $appointment->vaccine->name ?? 'N/A'],['Dose', $appointment->dose],['Hospital', $appointment->hospital->name ?? 'N/A'],['Date', $appointment->appointment_date->format('M d, Y')],['Time', $appointment->appointment_time],['Notes', $appointment->notes ?? 'None']] as [$k,$v])
            <div><p class="text-xs text-gray-400 uppercase tracking-wider mb-0.5">{{ $k }}</p><p class="font-medium text-gray-900">{{ $v }}</p></div>
        @endforeach
    </div>
    <div class="flex gap-3">
        @if($appointment->status === 'pending')
            <form method="POST" action="{{ route('admin.appointments.approve', $appointment) }}">@csrf<button class="px-4 py-2 bg-emerald-600 text-white text-sm font-semibold rounded-lg hover:bg-emerald-700">Approve</button></form>
            <form method="POST" action="{{ route('admin.appointments.reject', $appointment) }}">@csrf<button class="px-4 py-2 bg-red-600 text-white text-sm font-semibold rounded-lg hover:bg-red-700">Reject</button></form>
        @endif
        @if(!in_array($appointment->status, ['completed', 'cancelled']))
            <form method="POST" action="{{ route('admin.appointments.cancel', $appointment) }}">@csrf<button class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-50">Cancel</button></form>
        @endif
    </div>
</div>
@endsection
