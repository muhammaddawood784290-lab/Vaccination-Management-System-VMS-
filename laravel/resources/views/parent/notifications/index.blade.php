@extends('parent.layouts.dashboard')
@section('page-title', 'Notifications')
@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">Notifications</h2>
            <p class="text-sm text-gray-500">Stay updated on your children's vaccination status</p>
        </div>
        <form method="POST" action="{{ route('parent.notifications.mark-all-read') }}">
            @csrf
            <button type="submit" class="px-4 py-2 bg-emerald-50 text-emerald-700 rounded-lg text-sm font-medium hover:bg-emerald-100 transition">Mark All Read</button>
        </form>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 divide-y divide-gray-100">
        @forelse($notifications as $notification)
            <div class="px-5 py-4 {{ is_null($notification->read_at) ? 'bg-emerald-50' : '' }}">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-medium text-gray-900">{{ $notification->title }}</span>
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
                        <p class="text-xs text-gray-500 mt-1">{{ $notification->message }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                    </div>
                    @if(is_null($notification->read_at))
                        <form method="POST" action="{{ route('parent.notifications.mark-read', $notification) }}">
                            @csrf
                            <button type="submit" class="text-xs text-emerald-600 hover:underline">Mark Read</button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="px-5 py-12 text-center text-sm text-gray-400">No notifications yet.</div>
        @endforelse
    </div>
    {{ $notifications->links() }}
</div>
@endsection
