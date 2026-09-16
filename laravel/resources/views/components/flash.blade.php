@if (session('success'))
    <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg flex items-center gap-3">
        <p class="text-sm text-green-700">{{ session('success') }}</p>
    </div>
@endif

@if (session('error'))
    <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg flex items-center gap-3">
        <p class="text-sm text-red-700">{{ session('error') }}</p>
    </div>
@endif
