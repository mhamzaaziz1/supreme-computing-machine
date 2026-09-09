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
        'home' => ['M3 10.5 12 3l9 7.5', 'M5 9.5V21h14V9.5', 'M10 21v-6h4v6'],
        'users' => ['M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z', 'M4 21v-1a6 6 0 0 1 6-6h4a6 6 0 0 1 6 6v1'],
        'truck' => ['M3 7h11v9H3V7Z', 'M14 10h4l3 3v3h-7v-6Z', 'M7.5 19a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Z', 'M17.5 19a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Z'],
        'geofence' => ['M12 21s7-6.1 7-11a7 7 0 1 0-14 0c0 4.9 7 11 7 11Z', 'M12 12.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z'],
        'purchase' => ['M4 15v4a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-4', 'M12 3v10', 'm8 9 4 4 4-4'],
        'transfer' => ['M4 8h13', 'm14 5 3 3-3 3', 'M20 16H7', 'm10 13-3 3 3 3'],
        'adjustment' => ['M12 3v18', 'M5 8l7-5 7 5', 'M5 16l7 5 7-5'],
        'expense' => ['M12 2v20', 'M17 6.5c0-2-2.2-3.5-5-3.5S7 4.5 7 6.5 9.2 10 12 10s5 1.5 5 3.5-2.2 3.5-5 3.5-5-1.5-5-3.5'],
        'account' => ['M12 3 21 8H3l9-5Z', 'M5 10v8', 'M9 10v8', 'M15 10v8', 'M19 10v8', 'M2 21h20'],
        'calendar' => ['M4 6h16v14H4V6Z', 'M8 3v5', 'M16 3v5', 'M4 11h16'],
        'kitchen' => ['M6 14h12v6H6v-6Z', 'M7 14a4 4 0 0 1-1-7.9 4 4 0 0 1 7.5-2A4 4 0 0 1 18 6.1 4 4 0 0 1 17 14'],
        'orders' => ['M9 3h6v4H9V3Z', 'M9 5H5v16h14V5h-4', 'M9 11h6', 'M9 15h6'],
        'bell' => ['M18 9a6 6 0 1 0-12 0c0 5-2 6-2 6h16s-2-1-2-6Z', 'M13.7 20a2 2 0 0 1-3.4 0'],
    ];

    $paths = $sidebarIcons[$icon ?? ''] ?? $sidebarIcons['today'];
@endphp

<svg class="tw-shrink-0" width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor"
    stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
    @foreach ($paths as $d)
        <path d="{{ $d }}" />
    @endforeach
</svg>
