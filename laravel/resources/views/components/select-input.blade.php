<div class="mb-4">
    <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 mb-1">{{ $label }}</label>
    <select id="{{ $name }}" name="{{ $name }}" {{ $required ?? false ? 'required' : '' }} class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
        <option value="">{{ $placeholder ?? 'Select...' }}</option>
        @foreach($options as $value => $lbl)
            <option value="{{ $value }}" {{ old($name) == $value ? 'selected' : '' }}>{{ $lbl }}</option>
        @endforeach
    </select>
    @include('components.input-error', ['field' => $name])
</div>
