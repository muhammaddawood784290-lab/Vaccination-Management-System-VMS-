@props(["variant" => "default", "dot" => false, "pill" => false])
@php
$colors = [
    "success" => "bg-emerald-50 text-emerald-700 border-emerald-200",
    "warning" => "bg-amber-50 text-amber-700 border-amber-200",
    "error" => "bg-red-50 text-red-700 border-red-200",
    "info" => "bg-blue-50 text-blue-700 border-blue-200",
    "primary" => "bg-blue-50 text-blue-700 border-blue-200",
    "default" => "bg-gray-50 text-gray-600 border-gray-200",
];
$dotColors = [
    "success" => "bg-emerald-500", "warning" => "bg-amber-500", "error" => "bg-red-500",
    "info" => "bg-blue-500", "primary" => "bg-blue-500", "default" => "bg-gray-400",
];
@endphp
<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 text-xs font-medium rounded-full border {{ $colors[$variant] ?? $colors["default"] }}">
    @if($dot)
        <span class="w-1.5 h-1.5 rounded-full {{ $dotColors[$variant] ?? $dotColors["default"] }}"></span>
    @endif
    {{ $slot }}
</span>