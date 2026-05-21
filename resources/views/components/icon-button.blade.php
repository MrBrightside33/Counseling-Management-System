@props([
    'icon' => 'edit',
    'label',
    'variant' => 'default',
    'href' => null,
    'type' => 'button',
])

@php
    $base = 'inline-flex shrink-0 cursor-pointer items-center justify-center rounded-lg p-2 transition-colors focus:outline-none focus:ring-2 focus:ring-[#140DED]/30';
    $variants = [
        'default' => 'border border-gray-200 text-gray-700 hover:border-[#140DED]/30 hover:bg-[#140DED]/5 hover:text-[#140DED]',
        'danger' => 'border border-red-200 text-red-600 hover:bg-red-50',
        'primary' => 'border border-transparent bg-[#140DED] text-white hover:bg-[#1009c4] focus:ring-[#140DED]/40',
        'brand' => 'border border-[#140DED]/30 bg-[#140DED]/5 text-[#140DED] hover:bg-[#140DED]/10',
    ];
    $class = $base.' '.($variants[$variant] ?? $variants['default']);
@endphp

@if ($href)
    <a
        href="{{ $href }}"
        {{ $attributes->merge(['class' => $class, 'aria-label' => $label, 'title' => $label]) }}
    >
@else
    <button
        type="{{ $type }}"
        {{ $attributes->merge(['class' => $class, 'aria-label' => $label, 'title' => $label]) }}
    >
@endif
    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
        @switch($icon)
            @case('delete')
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                @break
            @case('export')
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5m0 0l5-5m-5 5V4"/>
                @break
            @case('history')
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                @break
            @case('notes')
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                @break
            @case('notes-add')
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                @break
            @case('save')
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                @break
            @default
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
        @endswitch
    </svg>
@if ($href)
    </a>
@else
    </button>
@endif
