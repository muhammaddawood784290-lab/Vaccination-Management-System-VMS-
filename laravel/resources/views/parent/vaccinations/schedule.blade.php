@extends('parent.layouts.dashboard')
@section('page-title', 'Vaccination Schedule')
@section('content')
<div class="space-y-6">
    <div>
        <h2 class="text-lg font-semibold text-gray-900">Vaccination Schedule</h2>
        <p class="text-sm text-gray-500">Upcoming vaccination schedules for your children</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500">Child</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500">Vaccine</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500">Dose</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500">Due Date</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($schedules as $schedule)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-3 text-sm font-medium text-gray-900">{{ $schedule->child->name ?? 'N/A' }}</td>
                            <td class="px-5 py-3 text-sm text-gray-600">{{ $schedule->vaccine->name ?? 'N/A' }}</td>
                            <td class="px-5 py-3 text-sm text-gray-600">Dose {{ $schedule->dose_number }}</td>
                            <td class="px-5 py-3 text-sm text-gray-600">{{ $schedule->due_date?->format('M d, Y') ?? 'N/A' }}</td>
                            <td class="px-5 py-3">
                                @php
                                    $badgeClass = match($schedule->status) { 'completed' => 'bg-green-100 text-green-800', 'due' => 'bg-blue-100 text-blue-800', 'overdue' => 'bg-red-100 text-red-800', 'skipped' => 'bg-gray-100 text-gray-800', default => 'bg-gray-100 text-gray-800' };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badgeClass }}">{{ ucfirst($schedule->status) }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-12 text-center text-sm text-gray-400">No vaccination schedules yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    {{ $schedules->links() }}
</div>
@endsection
