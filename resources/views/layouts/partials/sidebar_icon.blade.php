{{--
    Navigation glyphs for the Blade sidebar. Mirrors the paths in
    resources/js/components/Icon.vue so both surfaces look identical while
    the migration is in progress; keep the two in step when adding icons.
--}}
@php
    $sidebarIcons = [
        'today' => ['M3 12h4l2 5 4-11 2 6h6'],
        'sell' => ['M3 6h2l2.4 10.4a2 2 0 0 0 2 1.6h7.7a2 2 0 0 0 2-1.5L21 8H6', 'M10 21a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z', 'M18 21a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z'],
        'route' => ['M6 19a2 2 0 1 0 0-4 2 2 0 0 0 0 4Z', 'M18 9a2 2 0 1 0 0-4 2 2 0 0 0 0 4Z', 'M8.5 17H14a3 3 0 0 0 0-6h-4a3 3 0 0 1 0-6h5.5'],
        'customers' => ['M16 19v-1a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v1', 'M9.5 10a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Z', 'M21 19v-1a4 4 0 0 0-3-3.9', 'M15 3.1a4 4 0 0 1 0 7.8'],
        'stock' => ['M21 8.5 12 3 3 8.5v7L12 21l9-5.5v-7Z', 'M3 8.5 12 14l9-5.5', 'M12 14v7'],
        'insight' => ['M4 20V10', 'M10 20V4', 'M16 20v-6', 'M22 20H2'],
        'store' => ['M3 9 4.5 4h15L21 9', 'M3 9h18v11H3V9Z', 'M9 20v-6h6v6', 'M3 9a3 3 0 0 0 6 0 3 3 0 0 0 6 0 3 3 0 0 0 6 0'],
        'settings' => ['M4 21v-7', 'M4 10V3', 'M12 21v-9', 'M12 8V3', 'M20 21v-5', 'M20 12V3', 'M1 14h6', 'M9 8h6', 'M17 16h6'],
    ];

    $paths = $sidebarIcons[$icon ?? ''] ?? $sidebarIcons['today'];
@endphp

<svg class="tw-shrink-0" width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor"
    stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
    @foreach ($paths as $d)
        <path d="{{ $d }}" />
    @endforeach
</svg>
