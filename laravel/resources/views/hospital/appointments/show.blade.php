@extends('hospital.layouts.dashboard')
@section('page-title', 'Appointment Details')
@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-semibold text-gray-900">Appointment Details</h2>
            @php $badgeClass = match($appointment->status) { 'completed' => 'bg-green-100 text-green-800', 'confirmed' => 'bg-blue-100 text-blue-800', 'pending' => 'bg-yellow-100 text-yellow-800', 'cancelled' => 'bg-gray-100 text-gray-800', 'no_show' => 'bg-red-100 text-red-800', default => 'bg-gray-100 text-gray-800' }; @endphp
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $badgeClass }}">{{ ucfirst(str_replace('_', ' ', $appointment->status)) }}</span>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div><p class="text-xs text-gray-500">Child</p><p class="text-sm font-medium text-gray-900">{{ $appointment->child->name ?? 'N/A' }}</p></div>
            <div><p class="text-xs text-gray-500">Parent</p><p class="text-sm font-medium text-gray-900">{{ $appointment->parent->name ?? 'N/A' }}</p></div>
            <div><p class="text-xs text-gray-500">Vaccine</p><p class="text-sm font-medium text-gray-900">{{ $appointment->vaccine->name ?? 'N/A' }}</p></div>
            <div><p class="text-xs text-gray-500">Dose</p><p class="text-sm font-medium text-gray-900">{{ $appointment->dose }}</p></div>
            <div><p class="text-xs text-gray-500">Date</p><p class="text-sm font-medium text-gray-900">{{ $appointment->appointment_date?->format('M d, Y') ?? '' }}</p></div>
            <div><p class="text-xs text-gray-500">Time</p><p class="text-sm font-medium text-gray-900">{{ $appointment->appointment_time }}</p></div>
            @if($appointment->notes)
                <div class="col-span-2"><p class="text-xs text-gray-500">Notes</p><p class="text-sm text-gray-700">{{ $appointment->notes }}</p></div>
            @endif
        </div>
    </div>

    {{-- Action Buttons --}}
    @if($appointment->status === 'pending')
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-3">Actions</h3>
            <div class="flex items-center gap-3">
                <form method="POST" action="{{ route('hospital.appointments.confirm', $appointment) }}">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition">Confirm Appointment</button>
                </form>
            </div>
        </div>
    @endif

    @if($appointment->status === 'confirmed')
        {{-- Complete Vaccination Form --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Record Vaccination</h3>
            <form method="POST" action="{{ route('hospital.appointments.complete', $appointment) }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Dose Number *</label>
                        <input type="number" name="dose_number" value="1" min="1" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('dose_number') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Batch Number</label>
                        <input type="text" name="batch_number" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Administered By *</label>
                    <input type="text" name="administered_by" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Doctor/Nurse name">
                    @error('administered_by') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <textarea name="notes" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>
                <div class="flex items-center gap-3">
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition">Mark as Vaccinated</button>
                </div>
            </form>
        </div>

        {{-- No-Show --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-3">Mark No-Show</h3>
            <p class="text-sm text-gray-500 mb-3">Patient did not arrive for the appointment.</p>
            <form method="POST" action="{{ route('hospital.appointments.no-show', $appointment) }}" onsubmit="return confirm('Mark this appointment as no-show?')">
                @csrf
                <button type="submit" class="px-4 py-2 bg-red-50 text-red-600 rounded-lg text-sm font-medium hover:bg-red-100 transition">Mark as No-Show</button>
            </form>
        </div>
    @endif

    {{-- Vaccination Record (if completed) --}}
    @if($appointment->vaccinationRecord)
        <div class="bg-green-50 border border-green-200 rounded-xl p-6">
            <h3 class="text-sm font-semibold text-green-800 mb-3">Vaccination Record</h3>
            <div class="grid grid-cols-2 gap-4">
                <div><p class="text-xs text-green-600">Dose</p><p class="text-sm font-medium text-green-900">Dose {{ $appointment->vaccinationRecord->dose_number }}</p></div>
                <div><p class="text-xs text-green-600">Batch</p><p class="text-sm font-medium text-green-900">{{ $appointment->vaccinationRecord->batch_number ?? 'N/A' }}</p></div>
                <div><p class="text-xs text-green-600">Administered By</p><p class="text-sm font-medium text-green-900">{{ $appointment->vaccinationRecord->administered_by }}</p></div>
                <div><p class="text-xs text-green-600">Date</p><p class="text-sm font-medium text-green-900">{{ $appointment->vaccinationRecord->administered_at?->format('M d, Y H:i') }}</p></div>
            </div>
        </div>
    @endif

    <a href="{{ route('hospital.appointments.index') }}" class="inline-block px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition">← Back to Appointments</a>
</div>
@endsection
