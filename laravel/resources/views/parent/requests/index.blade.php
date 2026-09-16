@extends('parent.layouts.dashboard')
@section('page-title', 'My Requests')
@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">My Requests</h1>
            <p class="text-sm text-gray-500 mt-1">View and track your submitted requests.</p>
        </div>
        <a href="{{ route('parent.requests.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-medium hover:bg-emerald-700 transition">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            New Request
        </a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200">
        @forelse($requests as $request)
            <div class="px-5 py-4 border-b border-gray-100 last:border-b-0">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $request->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : ($request->status === 'in_review' ? 'bg-blue-100 text-blue-800' : ($request->status === 'resolved' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800')) }}">
                                {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                            </span>
                            <span class="text-xs text-gray-500">{{ ucfirst(str_replace('_', ' ', $request->type)) }}</span>
                        </div>
                        <p class="text-sm font-medium text-gray-900 mt-2">{{ $request->subject }}</p>
                        <p class="text-sm text-gray-500 mt-1 line-clamp-2">{{ $request->details }}</p>
                        <div class="flex items-center gap-4 mt-2 text-xs text-gray-400">
                            @if($request->hospital)
                                <span class="flex items-center gap-1">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18"/><path d="M5 21V7l7-4 7 4v14"/></svg>
                                    {{ $request->hospital->name }}
                                </span>
                            @endif
                            <span>Submitted {{ $request->created_at->format('M d, Y') }}</span>
                        </div>
                    </div>
                    <a href="{{ route('parent.requests.show', $request) }}" class="shrink-0 text-xs font-medium text-emerald-600 hover:underline">View Details</a>
                </div>
            </div>
        @empty
            <div class="px-5 py-10 text-center">
                <div class="w-12 h-12 mx-auto rounded-full bg-gray-100 flex items-center justify-center mb-3">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#6B7280" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6"/></svg>
                </div>
                <p class="text-sm text-gray-500">No requests yet.</p>
                <a href="{{ route('parent.requests.create') }}" class="mt-3 inline-block text-sm text-emerald-600 hover:underline">Submit your first request</a>
            </div>
        @endforelse

        @if($requests->hasPages())
            <div class="px-5 py-4 border-t border-gray-100">
                {{ $requests->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
