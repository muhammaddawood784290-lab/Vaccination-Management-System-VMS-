@extends('parent.layouts.dashboard')
@section('page-title', 'Vaccination Record')
@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-6">Vaccination Record</h2>
        <div class="grid grid-cols-2 gap-4">
            <div><p class="text-xs text-gray-500">Child</p><p class="text-sm font-medium text-gray-900">{{ $record->child->name ?? 'N/A' }}</p></div>
            <div><p class="text-xs text-gray-500">Vaccine</p><p class="text-sm font-medium text-gray-900">{{ $record->vaccine->name ?? 'N/A' }}</p></div>
            <div><p class="text-xs text-gray-500">Dose Number</p><p class="text-sm font-medium text-gray-900">{{ $record->dose_number }}</p></div>
            <div><p class="text-xs text-gray-500">Batch Number</p><p class="text-sm font-medium text-gray-900">{{ $record->batch_number ?? 'N/A' }}</p></div>
            <div><p class="text-xs text-gray-500">Hospital</p><p class="text-sm font-medium text-gray-900">{{ $record->hospital->name ?? 'N/A' }}</p></div>
            <div><p class="text-xs text-gray-500">Administered By</p><p class="text-sm font-medium text-gray-900">{{ $record->administered_by ?? 'N/A' }}</p></div>
            <div><p class="text-xs text-gray-500">Date</p><p class="text-sm font-medium text-gray-900">{{ $record->administered_at?->format('M d, Y H:i') ?? 'N/A' }}</p></div>
        </div>
        @if($record->notes)
            <div class="mt-4"><p class="text-xs text-gray-500">Notes</p><p class="text-sm text-gray-700">{{ $record->notes }}</p></div>
        @endif
    </div>
    <a href="{{ route('parent.vaccinations.history') }}" class="inline-block px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition">← Back to History</a>
</div>
@endsection
