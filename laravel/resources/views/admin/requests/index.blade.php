@extends('admin.layouts.dashboard')
@section('page-title', 'Requests')
@section('content')
<div class="mb-5">
    <h1 class="text-2xl font-semibold text-gray-900">Requests</h1>
    <p class="text-sm text-gray-500">Review and manage parent requests.</p>
</div>

@if($stats['pending'] > 0)
    <div class="mb-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 flex items-start gap-3">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#D97706" stroke-width="2" class="shrink-0 mt-0.5"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        <div>
            <p class="text-sm font-medium text-amber-800">{{ $stats['pending'] }} pending request(s) require your review.</p>
            <p class="text-xs text-amber-700 mt-0.5">Review them to move them into the review queue.</p>
        </div>
    </div>
@endif

<div class="bg-white rounded-xl border border-gray-200">
    <div class="p-4 border-b border-gray-100 flex gap-3 flex-wrap">
        <form method="GET" class="flex-1 min-w-[200px]">
            <select name="status" onchange="this.form.submit()" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500">
                <option value="all" {{ request('status','all') === 'all' ? 'selected' : '' }}>All status</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending ({{ $stats['pending'] }})</option>
                <option value="in_review" {{ request('status') === 'in_review' ? 'selected' : '' }}>In Review ({{ $stats['in_review'] }})</option>
                <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved ({{ $stats['resolved'] }})</option>
                <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed ({{ $stats['closed'] }})</option>
            </select>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left">
                <tr>
                    <th class="px-5 py-3 font-medium text-gray-500">Parent</th>
                    <th class="px-5 py-3 font-medium text-gray-500">Type</th>
                    <th class="px-5 py-3 font-medium text-gray-500">Subject</th>
                    <th class="px-5 py-3 font-medium text-gray-500">Hospital</th>
                    <th class="px-5 py-3 font-medium text-gray-500">Status</th>
                    <th class="px-5 py-3 font-medium text-gray-500">Submitted</th>
                    <th class="px-5 py-3 font-medium text-gray-500">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($requests as $req)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3">
                            <p class="font-medium text-gray-900">{{ $req->user->name ?? 'N/A' }}</p>
                            <p class="text-xs text-gray-400">{{ $req->user->email ?? '' }}</p>
                        </td>
                        <td class="px-5 py-3">
                            <span class="text-xs text-gray-500">{{ ucfirst(str_replace('_', ' ', $req->type)) }}</span>
                        </td>
                        <td class="px-5 py-3">
                            <p class="font-medium text-gray-900 max-w-[200px] truncate">{{ $req->subject }}</p>
                            <p class="text-xs text-gray-400 line-clamp-1">{{ $req->details }}</p>
                        </td>
                        <td class="px-5 py-3 text-gray-600">
                            @if($req->hospital)
                                {{ $req->hospital->name }}
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            @php
                                $badgeClass = match($req->status) {
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'in_review' => 'bg-blue-100 text-blue-800',
                                    'resolved' => 'bg-green-100 text-green-800',
                                    'closed' => 'bg-gray-100 text-gray-800',
                                    default => 'bg-gray-100 text-gray-800',
                                };
                            @endphp
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badgeClass }}">{{ ucfirst(str_replace('_', ' ', $req->status)) }}</span>
                        </td>
                        <td class="px-5 py-3">
                            <p class="font-medium text-gray-700">{{ $req->created_at->format('M d, Y') }}</p>
                            <p class="text-xs text-gray-400">{{ $req->created_at->format('g:i A') }}</p>
                        </td>
                        <td class="px-5 py-3">
                            <a href="{{ route('admin.requests.show', $req) }}" class="text-purple-600 hover:underline text-xs font-medium">View Details</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-10 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#9CA3AF" stroke-width="1.5"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6"/></svg>
                                <p class="text-sm text-gray-500">No requests found.</p>
                                @if(request('status') !== null && request('status') !== 'all')
                                    <a href="{{ route('admin.requests.index') }}" class="text-xs text-purple-600 hover:underline">Clear filter</a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($requests->hasPages())
        <div class="p-4 border-t border-gray-100">
            {{ $requests->links() }}
        </div>
    @endif
</div>
@endsection
