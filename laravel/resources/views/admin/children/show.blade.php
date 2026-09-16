@extends('admin.layouts.dashboard')
@section('page-title', 'Child Details')
@section('content')
<a href="{{ route('admin.children.index') }}" class="text-sm text-gray-500 hover:text-gray-700 mb-4 inline-flex items-center gap-1">&larr; Back to Children</a>
<div class="bg-white rounded-xl border border-gray-200 p-6">
    <div class="flex items-center gap-4 mb-6">
        <div class="w-14 h-14 rounded-full bg-purple-100 flex items-center justify-center text-purple-600 text-xl font-bold">{{ substr($child->name, 0, 1) }}</div>
        <div>
            <h2 class="text-xl font-semibold text-gray-900">{{ $child->name }}</h2>
            <p class="text-sm text-gray-500">{{ $child->user->name ?? 'Unknown' }}'s child</p>
            @php
                $badgeClass = match($child->status) {
                    'active' => 'bg-green-100 text-green-800',
                    default => 'bg-gray-100 text-gray-800',
                };
            @endphp
            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badgeClass }}" class="mt-1">{{ ucfirst($child->status) }}</span>
        </div>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm mb-6">
        @foreach([['Date of birth', $child->date_of_birth->format('M d, Y')], ['Gender', ucfirst($child->gender)], ['Blood group', $child->blood_group ?? 'N/A'], ['Relationship', $child->relationship ?? 'N/A']] as [$label, $value])
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wider mb-0.5">{{ $label }}</p>
                <p class="font-medium text-gray-900">{{ $value }}</p>
            </div>
        @endforeach
    </div>
    @if($child->allergies)
        <div class="mb-6"><p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Allergies</p><p class="text-sm text-gray-700">{{ $child->allergies }}</p></div>
    @endif
    @if($child->notes)
        <div class="mb-6"><p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Notes</p><p class="text-sm text-gray-700">{{ $child->notes }}</p></div>
    @endif
    @if($child->vaccinationRecords->count())
        <h3 class="text-sm font-semibold text-gray-900 mb-3">Vaccination History</h3>
        <div class="overflow-x-auto mb-4">
            <table class="w-full text-sm">
                <thead class="bg-gray-50"><tr><th class="px-4 py-2 text-left font-medium text-gray-500">Vaccine</th><th class="px-4 py-2 text-left font-medium text-gray-500">Dose</th><th class="px-4 py-2 text-left font-medium text-gray-500">Hospital</th><th class="px-4 py-2 text-left font-medium text-gray-500">Date</th></tr></thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($child->vaccinationRecords as $record)
                        <tr><td class="px-4 py-2">{{ $record->vaccine->name ?? 'N/A' }}</td><td class="px-4 py-2">{{ $record->dose_number }}</td><td class="px-4 py-2">{{ $record->hospital->name ?? 'N/A' }}</td><td class="px-4 py-2">{{ $record->administered_at?->format('M d, Y') ?? 'N/A' }}</td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
