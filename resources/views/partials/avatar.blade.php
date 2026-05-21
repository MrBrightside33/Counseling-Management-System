@props([
    'user',
    'size' => 'md',
])

@php
    $sizes = [
        'sm' => 'h-8 w-8 text-xs',
        'md' => 'h-10 w-10 text-sm',
        'lg' => 'h-24 w-24 text-2xl',
    ];
    $sizeClass = $sizes[$size] ?? $sizes['md'];
@endphp

@if ($user->avatar)
    <img
        src="{{ $user->avatarUrl() }}"
        alt="{{ $user->name }}"
        {{ $attributes->merge(['class' => "shrink-0 rounded-full object-cover {$sizeClass}"]) }}
    >
@else
    <div {{ $attributes->merge(['class' => "flex shrink-0 items-center justify-center rounded-full bg-[#140DED] font-medium text-white {$sizeClass}"]) }}>
        {{ $user->initials() }}
    </div>
@endif
