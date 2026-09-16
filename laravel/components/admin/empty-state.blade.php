@props(["title" => "No data found", "description" => "", "icon" => null])
<div class="text-center py-12 px-4">
    @if($icon)
        <div class="mx-auto w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center mb-4">{!! $icon !!}</div>
    @endif
    <h3 class="text-sm font-semibold text-gray-900 mb-1">{{ $title }}</h3>
    @if($description)
        <p class="text-sm text-gray-500">{{ $description }}</p>
    @endif
    {{ $slot }}
</div>