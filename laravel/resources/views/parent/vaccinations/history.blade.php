@extends('parent.layouts.dashboard')
@section('page-title', 'Vaccination History')
@section('content')
<div class="space-y-6">
    <div>
        <h2 class="text-lg font-semibold text-gray-900">Vaccination History</h2>
        <p class="text-sm text-gray-500">Complete vaccination records for your children</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500">Child</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500">Vaccine</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500">Dose</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500">Hospital</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500">Date</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($records as $record)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-3 text-sm font-medium text-gray-900">{{ $record->child->name ?? 'N/A' }}</td>
                            <td class="px-5 py-3 text-sm text-gray-600">{{ $record->vaccine->name ?? 'N/A' }}</td>
                            <td class="px-5 py-3 text-sm text-gray-600">Dose {{ $record->dose_number }}</td>
                            <td class="px-5 py-3 text-sm text-gray-600">{{ $record->hospital->name ?? 'N/A' }}</td>
                            <td class="px-5 py-3 text-sm text-gray-600">{{ $record->administered_at?->format('M d, Y') ?? 'N/A' }}</td>
                            <td class="px-5 py-3"><a href="{{ route('parent.vaccinations.show', $record) }}" class="text-xs text-emerald-600 hover:underline">View</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-12 text-center text-sm text-gray-400">No vaccination records yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    {{ $records->links() }}
</div>
@endsection
