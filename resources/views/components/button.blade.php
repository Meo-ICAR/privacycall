@props([
    'color' => 'primary', // default color
])

@php
    $colorClasses = [
        'primary' => 'bg-blue-600 hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800',
        'secondary' => 'bg-gray-600 hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-800',
        'danger' => 'bg-red-600 hover:bg-red-700 focus:bg-red-700 active:bg-red-800',
        'success' => 'bg-green-600 hover:bg-green-700 focus:bg-green-700 active:bg-green-800',
        'black' => 'bg-gray-800 hover:bg-gray-900 focus:bg-gray-900 active:bg-black',
    ][$color] ?? $color;
@endphp

<button {{ $attributes->merge([
    'type' => 'submit',
    'class' => "inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50 transition ease-in-out duration-150 $colorClasses"
]) }}>
    {{ $slot }}
</button>
    {{ $slot }}
</button>
