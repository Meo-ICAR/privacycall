@props(['icon', 'title', 'value', 'subtitle' => null, 'color' => 'blue'])

<div class="bg-white shadow rounded-lg p-6">
    <div class="flex items-center">
        <div class="flex-shrink-0">
            <i class="fas {{ $icon }} text-{{ $color }}-600 text-3xl"></i>
        </div>
        <div class="ml-4">
            <div class="text-sm text-gray-500">{{ $title }}</div>
            <div class="text-2xl font-bold text-gray-900">{{ $value }}</div>
            @if($subtitle)
                <div class="text-xs text-gray-500 mt-1">{!! $subtitle !!}</div>
            @endif
        </div>
    </div>
</div>
