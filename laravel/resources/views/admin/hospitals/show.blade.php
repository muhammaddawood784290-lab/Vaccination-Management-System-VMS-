@extends('admin.layouts.dashboard')
@section('page-title', 'Hospital Details')
@section('content')
<a href="{{ route('admin.hospitals.index') }}" class="text-sm text-gray-500 hover:text-gray-700 mb-4 inline-flex items-center gap-1">&larr; Back to Hospitals</a>
<div class="bg-white rounded-xl border border-gray-200 p-6">
    <div class="flex items-center justify-between mb-6">
        <div><h2 class="text-xl font-semibold text-gray-900">{{ $hospital->name }}</h2><p class="text-sm text-gray-500 font-mono">{{ $hospital->code }}</p></div>
        @php
            $badgeClass = match($hospital->status) {
                'active' => 'bg-green-100 text-green-800',
                'pending' => 'bg-yellow-100 text-yellow-800',
                default => 'bg-gray-100 text-gray-800',
            };
        @endphp
        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badgeClass }}">{{ ucfirst($hospital->status) }}</span>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm mb-6">
        @foreach([['City', $hospital->city], ['State', $hospital->state], ['Phone', $hospital->phone], ['Email', $hospital->email], ['Contact', $hospital->contact_person], ['Designation', $hospital->designation ?? 'N/A'], ['Total Beds', $hospital->total_beds], ['Total Records', number_format($hospital->vaccination_records_count)]] as [$label, $value])
            <div><p class="text-xs text-gray-400 uppercase tracking-wider mb-0.5">{{ $label }}</p><p class="font-medium text-gray-900">{{ $value }}</p></div>
        @endforeach
    </div>
    <div class="mb-6"><p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Address</p><p class="text-sm text-gray-700">{{ $hospital->address }}</p></div>
    @if($hospital->description)<div class="mb-6"><p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Description</p><p class="text-sm text-gray-700">{{ $hospital->description }}</p></div>@endif
    <div class="flex gap-3">
        <a href="{{ route('admin.hospitals.edit', $hospital) }}" class="px-4 py-2 bg-purple-600 text-white text-sm font-semibold rounded-lg hover:bg-purple-700">Edit Hospital</a>
        <form method="POST" action="{{ route('admin.hospitals.toggle-status', $hospital) }}">@csrf<button class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-50">{{ $hospital->status === 'active' ? 'Deactivate' : 'Activate' }}</button></form>
        @if($hospital->status === 'pending')
            <form method="POST" action="{{ route('admin.hospitals.approve', $hospital) }}">@csrf<button class="px-4 py-2 bg-emerald-600 text-white text-sm font-semibold rounded-lg hover:bg-emerald-700">Approve</button></form>
        @endif
    </div>
</div>
@endsection
