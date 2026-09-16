@extends('parent.layouts.dashboard')
@section('page-title', $hospital->name)
@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-4">
        <div class="w-14 h-14 rounded-xl bg-purple-100 flex items-center justify-center text-purple-600 font-bold text-xl">{{ substr($hospital->name, 0, 1) }}</div>
        <div>
            <h2 class="text-xl font-bold text-gray-900">{{ $hospital->name }}</h2>
            <p class="text-sm text-gray-500">{{ $hospital->city }}@if($hospital->state), {{ $hospital->state }}@endif</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl border border-gray-200 p-5 space-y-3">
            <h3 class="text-sm font-semibold text-gray-900">Contact Information</h3>
            @if($hospital->phone)<p class="text-sm text-gray-600">Phone: {{ $hospital->phone }}</p>@endif
            @if($hospital->email)<p class="text-sm text-gray-600">Email: {{ $hospital->email }}</p>@endif
            @if($hospital->address)<p class="text-sm text-gray-600">Address: {{ $hospital->address }}</p>@endif
            @if($hospital->contact_person)<p class="text-sm text-gray-600">Contact: {{ $hospital->contact_person }}</p>@endif
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5 space-y-3">
            <h3 class="text-sm font-semibold text-gray-900">Available Vaccines</h3>
            @forelse($inventory as $item)
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-700">{{ $item->vaccine->name ?? 'Vaccine' }}</span>
                    <span class="text-xs px-2 py-0.5 rounded-full {{ $item->quantity_available > 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">{{ $item->quantity_available }} available</span>
                </div>
            @empty
                <p class="text-sm text-gray-400">No vaccine inventory listed.</p>
            @endforelse
        </div>
    </div>

    <div class="flex items-center gap-3">
        <a href="{{ route('parent.appointments.create') }}" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-medium hover:bg-emerald-700 transition">Book Appointment</a>
        <a href="{{ route('parent.hospitals.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition">Back to Hospitals</a>
    </div>
</div>
@endsection
