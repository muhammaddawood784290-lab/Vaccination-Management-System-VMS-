@extends('admin.layouts.dashboard')
@section('page-title', 'Notifications')
@section('content')
<div class="flex items-center justify-between mb-5">
    <div><h1 class="text-2xl font-semibold text-gray-900">Notifications</h1></div>
    @if(auth()->user()->unreadNotifications()->count() > 0)
        <form method="POST" action="{{ route('admin.notifications.mark-all-read') }}">@csrf<button class="text-sm text-purple-600 hover:underline">Mark all read</button></form>
    @endif
</div>
<div class="bg-white rounded-xl border border-gray-200">
    @forelse($notifications as $notification)
        <div class="flex gap-4 px-5 py-4 border-b border-gray-100 {{ is_null($notification->read_at) ? 'bg-blue-50' : '' }}">
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2">
                    <span class="text-sm font-medium {{ is_null($notification->read_at) ? 'text-gray-900' : 'text-gray-600' }}">{{ $notification->title }}</span>
                    @php
                        $badgeClass = match($notification->type) {
                            'appointment_approved', 'vaccination_completed' => 'bg-green-100 text-green-800',
                            'appointment_rejected' => 'bg-red-100 text-red-800',
                            'appointment_cancelled', 'appointment_no_show' => 'bg-yellow-100 text-yellow-800',
                            'appointment_confirmed' => 'bg-blue-100 text-blue-800',
                            default => 'bg-gray-100 text-gray-800',
                        };
                    @endphp
                    <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $badgeClass }}">{{ ucfirst(str_replace('_', ' ', $notification->type)) }}</span>
                </div>
                <p class="text-sm text-gray-500 mt-0.5">{{ $notification->message }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
            </div>
            @if(is_null($notification->read_at))
                <form method="POST" action="{{ route('admin.notifications.mark-read', $notification) }}">@csrf<button class="text-xs text-purple-600 hover:underline">Mark read</button></form>
            @endif
        </div>
    @empty
        <div class="text-center py-12 text-gray-400"><p class="text-sm">No notifications</p></div>
    @endforelse
@endsection