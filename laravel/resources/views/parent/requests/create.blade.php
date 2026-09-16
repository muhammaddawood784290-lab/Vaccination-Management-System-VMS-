@extends('parent.layouts.dashboard')
@section('page-title', 'Submit a Request')
@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Submit a Request</h1>
        <p class="text-sm text-gray-500 mt-1">Send a message or request to the administration team.</p>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <form method="POST" action="{{ route('parent.requests.store') }}">
            @csrf

            <div class="mb-5">
                <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Request Type <span class="text-red-500">*</span></label>
                <select id="type" name="type" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 {{ $errors->has('type') ? 'ring-2 ring-red-500' : '' }}">
                    <option value="">Select a request type</option>
                    <option value="vaccination_request" {{ old('type') == 'vaccination_request' ? 'selected' : '' }}>Vaccination Request</option>
                    <option value="general_inquiry" {{ old('type') == 'general_inquiry' ? 'selected' : '' }}>General Inquiry</option>
                    <option value="complaint" {{ old('type') == 'complaint' ? 'selected' : '' }}>Complaint</option>
                    <option value="feedback" {{ old('type') == 'feedback' ? 'selected' : '' }}>Feedback</option>
                </select>
                @error('type')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-5">
                <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">Subject <span class="text-red-500">*</span></label>
                <input id="subject" type="text" name="subject" value="{{ old('subject') }}" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 {{ $errors->has('subject') ? 'ring-2 ring-red-500' : '' }}" placeholder="e.g. Request for additional vaccination record">
                @error('subject')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-5">
                <label for="hospital_id" class="block text-sm font-medium text-gray-700 mb-2">Hospital</label>
                <select id="hospital_id" name="hospital_id" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white {{ $errors->has('hospital_id') ? 'ring-2 ring-red-500' : '' }}">
                    <option value="" {{ old('hospital_id') === null || old('hospital_id') === '' ? 'selected' : '' }}>No specific hospital</option>
                    @foreach($hospitals as $hospital)
                        <option value="{{ $hospital->id }}" {{ old('hospital_id') == $hospital->id ? 'selected' : '' }}>{{ $hospital->name }}</option>
                    @endforeach
                </select>
                @error('hospital_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="details" class="block text-sm font-medium text-gray-700 mb-2">Details <span class="text-red-500">*</span></label>
                <textarea id="details" name="details" rows="5" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 {{ $errors->has('details') ? 'ring-2 ring-red-500' : '' }}" placeholder="Please describe your request in detail...">{{ old('details') }}</textarea>
                @error('details')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-400">Be as specific as possible so the team can review your request quickly.</p>
            </div>

            <div class="flex items-center gap-3 justify-end">
                <a href="{{ route('parent.requests.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900">Cancel</a>
                <button type="submit" class="px-5 py-2 bg-emerald-600 text-white rounded-lg text-sm font-medium hover:bg-emerald-700 transition">Submit Request</button>
            </div>
        </form>
    </div>
</div>
@endsection
