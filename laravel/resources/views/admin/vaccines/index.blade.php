@extends('admin.layouts.dashboard')
@section('page-title', 'Vaccines')
@section('content')
<div class="flex items-start justify-between mb-5">
    <div><h1 class="text-2xl font-semibold text-gray-900">Vaccines</h1><p class="text-sm text-gray-500">Manage vaccine catalogue and availability</p></div>
    <a href="{{ route('admin.vaccines.create') }}" class="px-4 py-2 bg-purple-600 text-white text-sm font-semibold rounded-lg hover:bg-purple-700 transition">+ Add Vaccine</a>
</div>
<div class="grid grid-cols-3 gap-4 mb-5">
    <div class="bg-white rounded-xl border border-gray-200 p-5"></div><path d="M22 6H12a4 4 0 00-4 4v1a4 4 0 01-4 4H2"/></svg>' />
    <div class="bg-white rounded-xl border border-gray-200 p-5"></div></svg>' />
    <div class="bg-white rounded-xl border border-gray-200 p-5"></div><line x1="6" y1="6" x2="18" y2="18"/></svg>' />
</div>
<div class="bg-white rounded-xl border border-gray-200">
    <div class="p-4 border-b border-gray-100">
        <form method="GET"><input type="text" name="search" value="{{ request('search') }}" placeholder="Search vaccines..." class="max-w-sm px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent"></form>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left"><tr><th class="px-5 py-3 font-medium text-gray-500">Vaccine</th><th class="px-5 py-3 font-medium text-gray-500">Doses</th><th class="px-5 py-3 font-medium text-gray-500">Age Range</th><th class="px-5 py-3 font-medium text-gray-500">Type</th><th class="px-5 py-3 font-medium text-gray-500">Manufacturer</th><th class="px-5 py-3 font-medium text-gray-500">Status</th><th class="px-5 py-3 font-medium text-gray-500">Actions</th></tr></thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($vaccines as $vaccine)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3"><p class="font-medium text-gray-900">{{ $vaccine->name }}</p><p class="text-xs text-gray-400 font-mono">{{ $vaccine->code }}</p></td>
                        <td class="px-5 py-3 font-mono">{{ $vaccine->doses }}</td>
                        <td class="px-5 py-3">{{ $vaccine->age_range }}</td>
                        <td class="px-5 py-3"><span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 text-xs font-medium rounded-full border bg-blue-50 text-blue-700 border-blue-200">{{ $vaccine->type }}</span></td>
                        <td class="px-5 py-3 text-gray-600">{{ $vaccine->manufacturer }}</td>
                        <td class="px-5 py-3">@php
                            $badgeClass = match($vaccine->status) {
                                'active' => 'bg-green-100 text-green-800',
                                default => 'bg-gray-100 text-gray-800',
                            };
                        @endphp<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badgeClass }}">{{ ucfirst($vaccine->status) }}</span></td>
                        <td class="px-5 py-3">
                            <a href="{{ route('admin.vaccines.edit', $vaccine) }}" class="text-purple-600 hover:underline text-xs">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-5 py-8 text-center text-gray-400">No vaccines found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4">{{ $vaccines->links() }}</div>
</div>
@endsection
