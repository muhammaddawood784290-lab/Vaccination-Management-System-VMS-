@extends('parent.layouts.dashboard')
@section('page-title', 'Appointments')
@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">My Appointments</h2>
            <p class="text-sm text-gray-500">View and manage your vaccination appointments</p>
        </div>
        <a href="{{ route('parent.appointments.create') }}" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-medium hover:bg-emerald-700 transition">+ Book Appointment</a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500">Child</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500">Vaccine</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500">Hospital</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500">Date</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500">Status</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($appointments as $apt)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-3 text-sm font-medium text-gray-900">{{ $apt->child->name ?? 'N/A' }}</td>
                            <td class="px-5 py-3 text-sm text-gray-600">{{ $apt->vaccine->name ?? 'N/A' }}</td>
                            <td class="px-5 py-3 text-sm text-gray-600">{{ $apt->hospital->name ?? 'N/A' }}</td>
                            <td class="px-5 py-3 text-sm text-gray-600">{{ $apt->appointment_date?->format('M d, Y') ?? '' }} {{ $apt->appointment_time }}</td>
                            <td class="px-5 py-3">
                                @php
                                    $badgeClass = match($apt->status) {
                                        'completed' => 'bg-green-100 text-green-800',
                                        'confirmed' => 'bg-blue-100 text-blue-800',
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'cancelled' => 'bg-gray-100 text-gray-800',
                                        'no_show' => 'bg-red-100 text-red-800',
                                        default => 'bg-gray-100 text-gray-800',
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badgeClass }}">{{ ucfirst(str_replace('_', ' ', $apt->status)) }}</span>
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('parent.appointments.show', $apt) }}" class="text-xs text-emerald-600 hover:underline">View</a>
                                    @if(in_array($apt->status, ['pending', 'confirmed']))
                                        <form method="POST" action="{{ route('parent.appointments.cancel', $apt) }}" onsubmit="return confirm('Cancel this appointment?')">
                                            @csrf
                                            <button type="submit" class="text-xs text-red-600 hover:underline">Cancel</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-12 text-center text-sm text-gray-400">No appointments yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    {{ $appointments->links() }}
</div>
@endsection
