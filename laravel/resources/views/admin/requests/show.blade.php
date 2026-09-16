@extends('admin.layouts.dashboard')
@section('page-title', 'Request Details')
@section('content')
<div class="mb-5">
    <a href="{{ route('admin.requests.index') }}" class="text-sm text-purple-600 hover:underline mb-2 inline-block">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
        Back to Requests
    </a>
    <h1 class="text-2xl font-semibold text-gray-900">Request Details</h1>
    <p class="text-sm text-gray-500 mt-1">Review the details of this parent request.</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
    <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 divide-y divide-gray-200">
        <div class="p-6">
            <div class="flex items-start justify-between gap-4">
                <div class="flex items-center gap-3">
                    @php
                        $badgeClass = match($request->status) {
                            'pending' => 'bg-yellow-100 text-yellow-800',
                            'in_review' => 'bg-blue-100 text-blue-800',
                            'resolved' => 'bg-green-100 text-green-800',
                            'closed' => 'bg-gray-100 text-gray-800',
                            default => 'bg-gray-100 text-gray-800',
                        };
                    @endphp
                    <span class="text-xs font-medium px-2.5 py-1 rounded-full {{ $badgeClass }}">
                        {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                    </span>
                    <span class="text-sm text-gray-500">{{ ucfirst(str_replace('_', ' ', $request->type)) }}</span>
                </div>
                <span class="text-xs text-gray-400">
                    Submitted {{ $request->created_at->format('M d, Y g:i A') }}
                </span>
            </div>

            <h2 class="text-lg font-semibold text-gray-900 mt-4">{{ $request->subject }}</h2>

            <p class="mt-3 text-sm text-gray-600 whitespace-pre-wrap">{!! nl2br(e($request->details)) !!}</p>

            <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide">Parent</p>
                    <p class="mt-0.5 font-medium text-gray-900">{{ $request->user->name ?? 'N/A' }}</p>
                    <p class="text-xs text-gray-400">{{ $request->user->email ?? '' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide">Hospital</p>
                    <p class="mt-0.5 text-gray-700">
                        @if($request->hospital)
                            {{ $request->hospital->name }}
                        @else
                            Not specified
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide">Submitted</p>
                    <p class="mt-0.5 text-gray-700">{{ $request->created_at->format('M d, Y g:i A') }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide">Last Updated</p>
                    <p class="mt-0.5 text-gray-700">{{ $request->updated_at->format('M d, Y g:i A') }}</p>
                </div>
            </div>
        </div>

        @if($request->admin_response)
            <div class="p-6 bg-gray-50">
                <h3 class="text-sm font-semibold text-gray-900 mb-3">Admin Response</h3>
                <div class="rounded-lg border border-gray-200 p-4">
                    <p class="text-sm text-gray-700 whitespace-pre-wrap">{!! nl2br(e($request->admin_response)) !!}</p>
                </div>
                @if($request->responded_at)
                    <p class="mt-2 text-xs text-gray-400">Responded on {{ $request->responded_at->format('M d, Y g:i A') }}</p>
                @endif
            </div>
        @endif
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <h3 class="text-sm font-semibold text-gray-900 mb-3">Status</h3>
        <div class="rounded-lg border border-gray-200 p-4">
            @if($request->status === 'pending')
                <div class="flex gap-2 mb-2">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#D97706" stroke-width="2" class="shrink-0"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    <span class="text-sm text-gray-700">Awaiting review by the administration team.</span>
                </div>
            @elseif($request->status === 'in_review')
                <div class="flex gap-2 mb-2">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="2" class="shrink-0"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/></svg>
                    <span class="text-sm text-gray-700">This request is currently under review.</span>
                </div>
            @elseif($request->status === 'resolved')
                <div class="flex gap-2 mb-2">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2" class="shrink-0"><path d="M20 6L9 17l-5-5"/></svg>
                    <span class="text-sm text-gray-700">This request has been resolved.</span>
                </div>
            @elseif($request->status === 'closed')
                <div class="flex gap-2 mb-2">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6B7280" stroke-width="2" class="shrink-0"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                    <span class="text-sm text-gray-700">This request has been closed.</span>
                </div>
            @endif
        </div>

        <div class="mt-5 space-y-3">
            @if($request->status === 'pending')
                <form method="POST" action="{{ route('admin.requests.review', $request) }}">
                    @csrf
                    <button type="submit" class="w-full px-4 py-2.5 bg-purple-600 text-white rounded-lg text-sm font-medium hover:bg-purple-700 transition flex items-center justify-center gap-2">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        Review Request
                    </button>
                </form>
            @elseif($request->status === 'in_review')
                <form method="POST" action="{{ route('admin.requests.resolve', $request) }}" class="space-y-3">
                    @csrf
                    <div>
                        <label for="admin_response" class="block text-sm font-medium text-gray-700 mb-1">Admin Response <span class="text-red-500">*</span></label>
                        <textarea id="admin_response" name="admin_response" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500" placeholder="Provide a response to this request..." required>{{ old('admin_response') }}</textarea>
                        @error('admin_response')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit" class="w-full px-4 py-2.5 bg-purple-600 text-white rounded-lg text-sm font-medium hover:bg-purple-700 transition flex items-center justify-center gap-2">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg>
                        Resolve Request
                    </button>
                </form>

                <hr class="border-gray-200">

                <form method="POST" action="{{ route('admin.requests.close', $request) }}">
                    @csrf
                    <button type="submit" class="w-full px-4 py-2.5 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700 transition flex items-center justify-center gap-2">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                        Close Request
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
