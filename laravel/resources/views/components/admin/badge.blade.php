@props(['variant' => 'default', 'dot' => false])

@php
$baseClasses = 'inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium';
$variants = [
    'success' => 'bg-green-100 text-green-800',
    'info' => 'bg-blue-100 text-blue-800',
    'warning' => 'bg-yellow-100 text-yellow-800',
    'error' => 'bg-red-100 text-red-800',
    'default' => 'bg-gray-100 text-gray-800',
];
$dotColors = [
    'success' => 'bg-green-500',
    'info' => 'bg-blue-500',
    'warning' => 'bg-yellow-500',
    'error' => 'bg-red-500',
    'default' => 'bg-gray-500',
];
@endphp

<span {{ $attributes->merge(['class' => $baseClasses . ' ' . ($variants[$variant] ?? $variants['default'])]) }}>
    @if($dot)
        <span class="w-1.5 h-1.5 rounded-full {{ $dotColors[$variant] ?? $dotColors['default'] }}"></span>
    @endif
    {{ $slot }}
</span>
