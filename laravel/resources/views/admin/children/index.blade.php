@extends('admin.layouts.dashboard')
@section('page-title', 'Children')
@section('content')
<div class="mb-5">
    <h1 class="text-2xl font-semibold text-gray-900">Children</h1>
    <p class="text-sm text-gray-500">{{ $children->total() }} children registered in VMS</p>
</div>
<div class="bg-white rounded-xl border border-gray-200">
    <div class="p-4 border-b border-gray-100 flex items-center gap-3">
        <form method="GET" class="flex-1 max-w-sm">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or parent..."
                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent">
        </form>
        <form method="GET">
            <select name="gender" onchange="this.form.submit()" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                <option value="all" {{ request('gender', 'all') === 'all' ? 'selected' : '' }}>All gender</option>
                <option value="male" {{ request('gender') === 'male' ? 'selected' : '' }}>Male</option>
                <option value="female" {{ request('gender') === 'female' ? 'selected' : '' }}>Female</option>
            </select>
        </form>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left">
                <tr>
                    <th class="px-5 py-3 font-medium text-gray-500">Child</th>
                    <th class="px-5 py-3 font-medium text-gray-500">Gender</th>
                    <th class="px-5 py-3 font-medium text-gray-500">Blood Group</th>
                    <th class="px-5 py-3 font-medium text-gray-500">Parent</th>
                    <th class="px-5 py-3 font-medium text-gray-500">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($children as $child)
                    <tr class="hover:bg-gray-50 cursor-pointer" onclick="window.location='{{ route('admin.children.show', $child) }}'">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center text-purple-600 text-xs font-bold">{{ substr($child->name, 0, 1) }}</div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $child->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $child->date_of_birth->format('M d, Y') }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3 capitalize">{{ $child->gender }}</td>
                        <td class="px-5 py-3"><span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 text-xs font-medium rounded-full border bg-blue-50 text-blue-700 border-blue-200">{{ $child->blood_group ?? 'N/A' }}</span></td>
                        <td class="px-5 py-3 text-gray-600">{{ $child->user->name ?? 'N/A' }}</td>
                        <td class="px-5 py-3">@php
                            $badgeClass = match($child->status) {
                                'active' => 'bg-green-100 text-green-800',
                                default => 'bg-gray-100 text-gray-800',
                            };
                        @endphp<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badgeClass }}">{{ ucfirst($child->status) }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-5 py-8 text-center text-gray-400">No children found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4">{{ $children->links() }}</div>
</div>
@endsection
