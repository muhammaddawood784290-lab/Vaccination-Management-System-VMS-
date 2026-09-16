@props(["type" => "info", "title" => null])
@php
$styles = [
    "info" => "bg-blue-50 border-blue-200 text-blue-700",
    "success" => "bg-emerald-50 border-emerald-200 text-emerald-700",
    "warning" => "bg-amber-50 border-amber-200 text-amber-700",
    "error" => "bg-red-50 border-red-200 text-red-700",
];
@endphp
<div class="rounded-lg border p-4 text-sm {{ $styles[$type] ?? $styles["info"] }}">
    @if($title)
        <p class="font-semibold mb-1">{{ $title }}</p>
    @endif
    {{ $slot }}
</div>