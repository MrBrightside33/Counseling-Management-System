@props([
    'counselor',
    'size' => 'lg',
])

@php
    $sizes = [
        'md' => 'h-10 w-10 text-sm',
        'lg' => 'h-16 w-16 text-lg',
        'xl' => 'h-24 w-24 text-2xl',
    ];
    $sizeClass = $sizes[$size] ?? $sizes['lg'];
@endphp

@if ($counselor->avatar)
    <img
        src="{{ $counselor->avatarUrl() }}"
        alt="{{ $counselor->name }}"
        {{ $attributes->merge(['class' => "shrink-0 rounded-full object-cover ring-2 ring-gray-100 {$sizeClass}"]) }}
    >
@else
    <div {{ $attributes->merge(['class' => "flex shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-[#140DED] to-purple-600 font-bold text-white ring-2 ring-gray-100 {$sizeClass}"]) }}>
        {{ $counselor->initials() }}
    </div>
@endif
