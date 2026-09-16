@extends('parent.layouts.dashboard')
@section('page-title', $child->name)
@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 font-bold text-xl">
                {{ substr($child->name, 0, 1) }}
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-900">{{ $child->name }}</h2>
                <p class="text-sm text-gray-500">{{ ucfirst($child->gender) }} · Born {{ $child->date_of_birth->format('M d, Y') }} ({{ $child->date_of_birth->age }} yrs)</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('parent.children.edit', $child) }}" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-medium hover:bg-emerald-700 transition">Edit</a>
            <form method="POST" action="{{ route('parent.children.destroy', $child) }}" onsubmit="return confirm('Are you sure?')">
                @csrf @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-50 text-red-600 rounded-lg text-sm font-medium hover:bg-red-100 transition">Delete</button>
            </form>
        </div>
    </div>

    {{-- Info Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @if($child->blood_group)
            <div class="bg-white rounded-xl border border-gray-200 p-4"><p class="text-xs text-gray-500">Blood Group</p><p class="text-lg font-bold text-gray-900">{{ $child->blood_group }}</p></div>
        @endif
        <div class="bg-white rounded-xl border border-gray-200 p-4"><p class="text-xs text-gray-500">Relationship</p><p class="text-lg font-bold text-gray-900">{{ ucfirst($child->relationship) }}</p></div>
        <div class="bg-white rounded-xl border border-gray-200 p-4"><p class="text-xs text-gray-500">Vaccinations</p><p class="text-lg font-bold text-gray-900">{{ $child->vaccinationRecords->count() }}</p></div>
        <div class="bg-white rounded-xl border border-gray-200 p-4"><p class="text-xs text-gray-500">Status</p><p class="text-lg font-bold text-emerald-600">{{ ucfirst($child->status) }}</p></div>
    </div>

    @if($child->allergies)
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
            <p class="text-sm font-medium text-amber-800">Known Allergies</p>
            <p class="text-sm text-amber-700 mt-1">{{ $child->allergies }}</p>
        </div>
    @endif

    {{-- Vaccination Schedule --}}
    <div class="bg-white rounded-xl border border-gray-200">
        <div class="p-5 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-900">Vaccination Schedule</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50"><tr>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500">Vaccine</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500">Dose</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500">Due Date</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500">Status</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($child->vaccinationSchedules as $schedule)
                        <tr>
                            <td class="px-5 py-3 text-sm text-gray-900">{{ $schedule->vaccine->name ?? 'N/A' }}</td>
                            <td class="px-5 py-3 text-sm text-gray-600">Dose {{ $schedule->dose_number }}</td>
                            <td class="px-5 py-3 text-sm text-gray-600">{{ $schedule->due_date?->format('M d, Y') ?? 'N/A' }}</td>
                            <td class="px-5 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ match($schedule->status) { 'completed' => 'bg-green-100 text-green-800', 'due' => 'bg-blue-100 text-blue-800', 'overdue' => 'bg-red-100 text-red-800', 'skipped' => 'bg-gray-100 text-gray-800', default => 'bg-gray-100 text-gray-800' } }}">
                                    {{ ucfirst($schedule->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-5 py-8 text-center text-sm text-gray-400">No schedules yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Vaccination History --}}
    <div class="bg-white rounded-xl border border-gray-200">
        <div class="p-5 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-900">Vaccination History</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50"><tr>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500">Vaccine</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500">Dose</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500">Hospital</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500">Date</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($child->vaccinationRecords as $record)
                        <tr>
                            <td class="px-5 py-3 text-sm text-gray-900">{{ $record->vaccine->name ?? 'N/A' }}</td>
                            <td class="px-5 py-3 text-sm text-gray-600">Dose {{ $record->dose_number }}</td>
                            <td class="px-5 py-3 text-sm text-gray-600">{{ $record->hospital->name ?? 'N/A' }}</td>
                            <td class="px-5 py-3 text-sm text-gray-600">{{ $record->administered_at?->format('M d, Y') ?? 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-5 py-8 text-center text-sm text-gray-400">No vaccinations yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
