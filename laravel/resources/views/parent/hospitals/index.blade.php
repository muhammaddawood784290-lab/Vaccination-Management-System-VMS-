@extends('parent.layouts.dashboard')
@section('page-title', 'Find Hospitals')
@section('content')
<div class="space-y-6">
    <div>
        <h2 class="text-lg font-semibold text-gray-900">Hospitals</h2>
        <p class="text-sm text-gray-500">Browse active vaccination hospitals near you</p>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($hospitals as $hospital)
            <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md transition">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center text-purple-600 font-bold text-sm">{{ substr($hospital->name, 0, 1) }}</div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">{{ $hospital->name }}</h3>
                        <p class="text-xs text-gray-400">{{ $hospital->city }}</p>
                    </div>
                </div>
                <div class="space-y-1 mb-4">
                    @if($hospital->address)<p class="text-xs text-gray-500">{{ $hospital->address }}</p>@endif
                    @if($hospital->phone)<p class="text-xs text-gray-500">📞 {{ $hospital->phone }}</p>@endif
                </div>
                <a href="{{ route('parent.hospitals.show', $hospital) }}" class="block text-center px-3 py-2 bg-emerald-50 text-emerald-700 rounded-lg text-xs font-medium hover:bg-emerald-100 transition">View Details</a>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-xl border border-gray-200 p-12 text-center">
                <p class="text-sm text-gray-400">No active hospitals available.</p>
            </div>
        @endforelse
    </div>
    {{ $hospitals->links() }}
</div>
@endsection
