@props(['type' => 'button', 'variant' => 'primary', 'class' => ''])

@php
    $baseClasses = 'tw-inline-flex tw-items-center tw-justify-center tw-px-4 tw-py-2 tw-text-sm tw-font-medium tw-rounded-lg tw-transition-colors tw-duration-200 focus:tw-outline-none focus:tw-ring-2 focus:tw-ring-offset-2';
    
    $variants = [
        'primary' => 'tw-bg-primary-600 tw-text-white hover:tw-bg-primary-700 focus:tw-ring-primary-500',
        'secondary' => 'tw-bg-white dark:tw-bg-dark-surface tw-text-gray-700 dark:tw-text-gray-200 tw-border tw-border-gray-300 dark:tw-border-dark-border hover:tw-bg-gray-50 dark:hover:tw-bg-dark-hover focus:tw-ring-primary-500',
        'danger' => 'tw-bg-red-600 tw-text-white hover:tw-bg-red-700 focus:tw-ring-red-500',
    ];
    
    $classes = $baseClasses . ' ' . ($variants[$variant] ?? $variants['primary']) . ' ' . $class;
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</button>
