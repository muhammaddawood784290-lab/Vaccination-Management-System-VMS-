@extends('parent.layouts.dashboard')
@section('page-title', 'Appointment Details')
@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-semibold text-gray-900">Appointment Details</h2>
            @php
                $badgeClass = match($appointment->status) {
                    'completed' => 'bg-green-100 text-green-800',
                    'confirmed' => 'bg-blue-100 text-blue-800',
                    'pending' => 'bg-yellow-100 text-yellow-800',
                    'cancelled' => 'bg-gray-100 text-gray-800',
                    'no_show' => 'bg-red-100 text-red-800',
                    default => 'bg-gray-100 text-gray-800',
                };
            @endphp
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $badgeClass }}">{{ ucfirst(str_replace('_', ' ', $appointment->status)) }}</span>
        </div>
        <div class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div><p class="text-xs text-gray-500">Child</p><p class="text-sm font-medium text-gray-900">{{ $appointment->child->name ?? 'N/A' }}</p></div>
                <div><p class="text-xs text-gray-500">Vaccine</p><p class="text-sm font-medium text-gray-900">{{ $appointment->vaccine->name ?? 'N/A' }}</p></div>
                <div><p class="text-xs text-gray-500">Dose</p><p class="text-sm font-medium text-gray-900">{{ $appointment->dose }}</p></div>
                <div><p class="text-xs text-gray-500">Hospital</p><p class="text-sm font-medium text-gray-900">{{ $appointment->hospital->name ?? 'N/A' }}</p></div>
                <div><p class="text-xs text-gray-500">Date</p><p class="text-sm font-medium text-gray-900">{{ $appointment->appointment_date?->format('M d, Y') ?? '' }}</p></div>
                <div><p class="text-xs text-gray-500">Time</p><p class="text-sm font-medium text-gray-900">{{ $appointment->appointment_time }}</p></div>
            </div>
            @if($appointment->notes)
                <div><p class="text-xs text-gray-500">Notes</p><p class="text-sm text-gray-700">{{ $appointment->notes }}</p></div>
            @endif
        </div>
    </div>
    @if(in_array($appointment->status, ['pending', 'confirmed']))
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-3">Cancel Appointment</h3>
            <p class="text-sm text-gray-500 mb-4">Are you sure you want to cancel this appointment?</p>
            <form method="POST" action="{{ route('parent.appointments.cancel', $appointment) }}" onsubmit="return confirm('Are you sure?')">
                @csrf
                <button type="submit" class="px-4 py-2 bg-red-50 text-red-600 rounded-lg text-sm font-medium hover:bg-red-100 transition">Cancel Appointment</button>
            </form>
        </div>
    @endif
    <a href="{{ route('parent.appointments.index') }}" class="inline-block px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition">← Back to Appointments</a>
</div>
@endsection
