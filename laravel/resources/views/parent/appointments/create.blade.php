@extends('parent.layouts.dashboard')
@section('page-title', 'Book Appointment')
@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-6">Book Vaccination Appointment</h2>
        <form method="POST" action="{{ route('parent.appointments.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Select Child *</label>
                <select name="child_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('child_id') border-red-500 @enderror">
                    <option value="">Choose a child</option>
                    @foreach($children as $child)
                        <option value="{{ $child->id }}" {{ old('child_id') == $child->id ? 'selected' : '' }}>{{ $child->name }}</option>
                    @endforeach
                </select>
                @error('child_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Select Hospital *</label>
                <select name="hospital_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('hospital_id') border-red-500 @enderror">
                    <option value="">Choose a hospital</option>
                    @foreach($hospitals as $hospital)
                        <option value="{{ $hospital->id }}" {{ old('hospital_id') == $hospital->id ? 'selected' : '' }}>{{ $hospital->name }} — {{ $hospital->city }}</option>
                    @endforeach
                </select>
                @error('hospital_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Select Vaccine *</label>
                <select name="vaccine_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('vaccine_id') border-red-500 @enderror">
                    <option value="">Choose a vaccine</option>
                    @foreach($vaccines as $vaccine)
                        <option value="{{ $vaccine->id }}" {{ old('vaccine_id') == $vaccine->id ? 'selected' : '' }}>{{ $vaccine->name }} ({{ $vaccine->type }})</option>
                    @endforeach
                </select>
                @error('vaccine_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Dose *</label>
                <input type="text" name="dose" value="{{ old('dose', '1st Dose') }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('dose') border-red-500 @enderror" placeholder="e.g. 1st Dose, Booster">
                @error('dose') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Appointment Date *</label>
                    <input type="date" name="appointment_date" value="{{ old('appointment_date') }}" required min="{{ now()->addDay()->format('Y-m-d') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('appointment_date') border-red-500 @enderror">
                    @error('appointment_date') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Preferred Time *</label>
                    <input type="time" name="appointment_time" value="{{ old('appointment_time', '09:00') }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('appointment_time') border-red-500 @enderror">
                    @error('appointment_time') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                <textarea name="notes" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">{{ old('notes') }}</textarea>
            </div>
            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-medium hover:bg-emerald-700 transition">Submit Request</button>
                <a href="{{ route('parent.appointments.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
