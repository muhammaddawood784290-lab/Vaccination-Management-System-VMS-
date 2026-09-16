@props(["label", "value", "change" => null, "changeType" => "neutral", "iconBg" => "#EBF5FF", "icon"])
<div {{ $attributes->merge(["class" => "bg-white rounded-xl border border-gray-200 p-5"]) }}>
    <div class="flex items-center justify-between mb-3">
        <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background-color:{{ $iconBg }}">{!! $icon !!}</div>
    </div>
    <p class="text-2xl font-bold text-gray-900">{{ $value }}</p>
    <p class="text-sm text-gray-500 mt-0.5">{{ $label }}</p>
    @if($change)
        <p class="text-xs mt-1 {{ $changeType === "up" ? "text-emerald-600" : ($changeType === "down" ? "text-red-500" : "text-gray-400") }}">{{ $change }}</p>
    @endif
</div>