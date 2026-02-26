@props(['class' => '', 'title' => null, 'tools' => null])

<div {{ $attributes->merge(['class' => 'tw-bg-white dark:tw-bg-dark-surface tw-rounded-xl tw-shadow-card dark:tw-shadow-none dark:tw-border dark:tw-border-dark-border ' . $class]) }}>
    @if($title || $tools)
    <div class="tw-border-b tw-border-gray-100 dark:tw-border-dark-border tw-px-6 tw-py-4 tw-flex tw-items-center tw-justify-between">
        @if($title)
        <h3 class="tw-font-semibold tw-text-gray-900 dark:tw-text-dark-text-primary tw-text-lg">
            {{ $title }}
        </h3>
        @endif
        
        @if($tools)
        <div class="tw-flex tw-items-center tw-gap-2">
            {{ $tools }}
        </div>
        @endif
    </div>
    @endif

    <div class="tw-p-6">
        {{ $slot }}
    </div>
</div>
