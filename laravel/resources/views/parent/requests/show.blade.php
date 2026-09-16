@extends('parent.layouts.dashboard')
@section('page-title', 'Request Details')
@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('parent.requests.index') }}" class="text-sm text-emerald-600 hover:underline mb-2 inline-block">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
            Back to My Requests
        </a>
        <h1 class="text-2xl font-semibold text-gray-900">Request Details</h1>
        <p class="text-sm text-gray-500 mt-1">Review the status and details of your request.</p>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 divide-y divide-gray-200">
        <div class="p-6">
            <div class="flex items-start justify-between gap-4">
                <div class="flex items-center gap-3">
                    <span class="text-xs font-medium px-2.5 py-1 rounded-full {{ $requestModel->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : ($requestModel->status === 'in_review' ? 'bg-blue-100 text-blue-800' : ($requestModel->status === 'resolved' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800')) }}">
                        {{ ucfirst(str_replace('_', ' ', $requestModel->status)) }}
                    </span>
                    <span class="text-sm text-gray-500">{{ ucfirst(str_replace('_', ' ', $requestModel->type)) }}</span>
                </div>
                <span class="text-xs text-gray-400">
                    Submitted {{ $requestModel->created_at->format('M d, Y g:i A') }}
                </span>
            </div>

            <h2 class="text-lg font-semibold text-gray-900 mt-4">{{ $requestModel->subject }}</h2>

            <p class="mt-3 text-sm text-gray-600 whitespace-pre-wrap">{!! nl2br(e($requestModel->details)) !!}</p>

            <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide">Hospital</p>
                    <p class="mt-0.5 text-gray-700">
                        @if($requestModel->hospital)
                            {{ $requestModel->hospital->name }}
                        @else
                            Not specified
                        @endif
                    </p>
                </div>
                <div class="md:pr-8">
                    <p class="text-xs text-gray-400 uppercase tracking-wide">Submitted</p>
                    <p class="mt-0.5 text-gray-700">{{ $requestModel->created_at->format('M d, Y g:i A') }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide">Last Updated</p>
                    <p class="mt-0.5 text-gray-700">{{ $requestModel->updated_at->format('M d, Y g:i A') }}</p>
                </div>
            </div>
        </div>

        <div class="p-6 bg-gray-50">
            <h3 class="text-sm font-semibold text-gray-900 mb-3">Status</h3>
            <div class="rounded-lg border border-gray-200 p-4">
                @if($requestModel->status === 'pending')
                    <div class="flex gap-2 mb-2">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#D97706" stroke-width="2" class="shrink-0"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        <span class="text-sm text-gray-700">Your request has been submitted and is awaiting review by the administration team.</span>
                    </div>
                @elseif($requestModel->status === 'in_review')
                    <div class="flex gap-2 mb-2">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="2" class="shrink-0"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/></svg>
                        <span class="text-sm text-gray-700">An administrator is currently reviewing your request.</span>
                    </div>
                @elseif($requestModel->status === 'resolved')
                    <div class="flex gap-2 mb-2">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2" class="shrink-0"><path d="M20 6L9 17l-5-5"/></svg>
                        <span class="text-sm text-gray-700">Your request has been resolved.</span>
                    </div>
                    @if($requestModel->admin_response)
                        <div class="mt-3 rounded-lg bg-green-50 border border-green-200 p-3">
                            <p class="text-xs text-green-700 uppercase tracking-wide mb-1">Admin Response</p>
                            <p class="text-sm text-green-800 whitespace-pre-wrap">{!! nl2br(e($requestModel->admin_response)) !!}</p>
                        </div>
                    @endif
                    @if($requestModel->responded_at)
                        <p class="mt-2 text-xs text-gray-400">Responded on {{ $requestModel->responded_at->format('M d, Y g:i A') }}</p>
                    @endif
                @elseif($requestModel->status === 'closed')
                    <div class="flex gap-2 mb-2">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6B7280" stroke-width="2" class="shrink-0"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                        <span class="text-sm text-gray-700">This request has been closed.</span>
                    </div>
                    @if($requestModel->admin_response)
                        <div class="mt-3 rounded-lg bg-gray-100 border border-gray-200 p-3">
                            <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Admin Response</p>
                            <p class="text-sm text-gray-700 whitespace-pre-wrap">{!! nl2br(e($requestModel->admin_response)) !!}</p>
                        </div>
                    @endif
                    @if($requestModel->responded_at)
                        <p class="mt-2 text-xs text-gray-400">Responded on {{ $requestModel->responded_at->format('M d, Y g:i A') }}</p>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
