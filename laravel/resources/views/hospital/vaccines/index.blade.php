@extends('hospital.layouts.dashboard')
@section('page-title', 'Vaccine Inventory')
@section('content')
<div class="space-y-6">
    <div><h2 class="text-lg font-semibold text-gray-900">Vaccine Inventory</h2><p class="text-sm text-gray-500">Manage vaccine stock at your hospital</p></div>
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50"><tr>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500">Vaccine</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500">Type</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500">Available</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500">Capacity</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500">Batch</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500">Expiry</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500">Status</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500">Action</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($inventory as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-3 text-sm font-medium text-gray-900">{{ $item->vaccine->name ?? 'N/A' }}</td>
                            <td class="px-5 py-3 text-sm text-gray-600">{{ $item->vaccine->type ?? 'N/A' }}</td>
                            <td class="px-5 py-3 text-sm text-gray-600">{{ $item->available }}</td>
                            <td class="px-5 py-3 text-sm text-gray-600">{{ $item->capacity }}</td>
                            <td class="px-5 py-3 text-sm text-gray-600">{{ $item->batch_number ?? 'N/A' }}</td>
                            <td class="px-5 py-3 text-sm text-gray-600">{{ $item->expiry_date?->format('M d, Y') ?? 'N/A' }}</td>
                            <td class="px-5 py-3">
                                @if($item->available <= 2)<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Critical</span>
                                @elseif($item->available <= 5)<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Low</span>
                                @else<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Good</span>
                                @endif
                            </td>
                            <td class="px-5 py-3">
                                <button onclick="document.getElementById('edit-{{ $item->id }}').classList.toggle('hidden')" class="text-xs text-blue-600 hover:underline">Update</button>
                            </td>
                        </tr>
                        <tr id="edit-{{ $item->id }}" class="hidden">
                            <td colspan="8" class="px-5 py-4 bg-blue-50">
                                <form method="POST" action="{{ route('hospital.vaccines.update', $item) }}" class="flex items-end gap-4">
                                    @csrf @method('PUT')
                                    <div><label class="block text-xs font-medium text-gray-700 mb-1">Available</label><input type="number" name="available" value="{{ $item->available }}" min="0" required class="w-24 border border-gray-300 rounded-lg px-3 py-2 text-sm"></div>
                                    <div><label class="block text-xs font-medium text-gray-700 mb-1">Capacity</label><input type="number" name="capacity" value="{{ $item->capacity }}" min="0" required class="w-24 border border-gray-300 rounded-lg px-3 py-2 text-sm"></div>
                                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">Save</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-5 py-12 text-center text-sm text-gray-400">No vaccine inventory found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    {{ $inventory->links() }}
</div>
@endsection
