{{--
    Primary navigation for the Blade surface.

    Renders from App\Support\Navigation, the same tree the Inertia shell and
    the command palette use, so the information architecture is defined once.
    This replaced ~826 lines of hardcoded markup whose links, permissions and
    grouping had drifted out of step with the routes behind them.

    Behavioural contract kept for layouts/partials/javascripts.blade.php:
      .side-bar                 root element
      .small-view-side-active   mobile drawer state, toggled by .small-view-button
      .side-bar-collapse        desktop rail toggle (see js at the bottom)
--}}

@php
    $nav = \App\Support\Navigation::forCurrentUser();
    $settingsNav = \App\Support\Navigation::settingsForCurrentUser();

    // Longest matching path wins, so /sells/create beats /sells.
    $here = '/' . trim(request()->getPathInfo(), '/');
    $activeUrl = null;

    foreach ($nav as $group) {
        foreach ($group['items'] as $item) {
            $path = '/' . trim(parse_url($item['url'], PHP_URL_PATH) ?? '', '/');
            if (($here === $path || str_starts_with($here, $path . '/'))
                && (! $activeUrl || strlen($path) > strlen($activeUrl))) {
                $activeUrl = $path;
            }
        }
    }

    $isActive = function (array $item) use ($activeUrl) {
        return $activeUrl !== null
            && '/' . trim(parse_url($item['url'], PHP_URL_PATH) ?? '', '/') === $activeUrl;
    };
@endphp

<aside class="side-bar tw-hidden lg:tw-flex tw-flex-col tw-shrink-0 tw-w-64 tw-h-screen tw-bg-nav-bg tw-text-nav-fg tw-z-30 no-print">

    <a href="{{ route('home') }}"
        class="tw-flex tw-items-center tw-gap-2.5 tw-h-14 tw-shrink-0 tw-px-4 tw-border-b tw-border-nav-border">
        @php $logo = session('business.logo'); @endphp
        @if (!empty($logo) && is_file(public_path('uploads/business_logos/' . $logo)))
            <img src="{{ asset('uploads/business_logos/' . $logo) }}" alt=""
                class="tw-h-8 tw-w-8 tw-shrink-0 tw-rounded tw-object-contain">
        @else
            <span class="tw-grid tw-h-8 tw-w-8 tw-shrink-0 tw-place-items-center tw-rounded tw-bg-accent-500 tw-text-sm tw-font-bold tw-text-brand-950">
                {{ strtoupper(substr(session('business.name'), 0, 1)) }}
            </span>
        @endif
        <span class="sidebar-label tw-truncate tw-text-sm tw-font-semibold tw-text-nav-fg-active">
            {{ session('business.name') }}
        </span>
    </a>

    <div class="sidebar-scroll tw-flex-1 tw-overflow-y-auto tw-overflow-x-hidden tw-py-2">
        <ul class="tw-px-2 tw-space-y-0.5">
            @foreach ($nav as $i => $group)
                <li>
                    @if (!empty($group['url']))
                        {{-- A group with its own URL is a link, not a container. --}}
                        <a href="{{ $group['url'] }}" title="{{ $group['label'] }}"
                            class="tw-flex tw-items-center tw-gap-3 tw-rounded-md tw-px-2.5 tw-py-2 tw-text-sm tw-font-medium hover:tw-bg-white/5 hover:tw-text-nav-fg-active {{ $here === '/' . trim(parse_url($group['url'], PHP_URL_PATH) ?? '', '/') ? 'tw-bg-nav-active-bg tw-text-nav-fg-active' : '' }}">
                            @include('layouts.partials.sidebar_icon', ['icon' => $group['icon']])
                            <span class="sidebar-label tw-truncate">{{ $group['label'] }}</span>
                        </a>
                    @else
                        @php $groupActive = collect($group['items'])->contains($isActive); @endphp

                        <button type="button" title="{{ $group['label'] }}"
                            class="sidebar-group-toggle tw-flex tw-w-full tw-items-center tw-gap-3 tw-rounded-md tw-px-2.5 tw-py-2 tw-text-sm tw-font-medium hover:tw-bg-white/5 hover:tw-text-nav-fg-active {{ $groupActive ? 'tw-text-nav-fg-active' : '' }}"
                            data-group="{{ $i }}" aria-expanded="{{ $groupActive ? 'true' : 'false' }}">
                            @include('layouts.partials.sidebar_icon', ['icon' => $group['icon']])
                            <span class="sidebar-label tw-truncate">{{ $group['label'] }}</span>
                            <svg class="sidebar-label sidebar-chevron tw-ml-auto tw-shrink-0 tw-transition-transform {{ $groupActive ? 'tw-rotate-180' : '' }}"
                                width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="m6 9 6 6 6-6" />
                            </svg>
                        </button>

                        <ul class="sidebar-group tw-ml-4 tw-mt-0.5 tw-mb-1 tw-pl-3 tw-border-l tw-border-nav-border {{ $groupActive ? '' : 'tw-hidden' }}"
                            data-group="{{ $i }}">
                            @foreach ($group['items'] as $item)
                                <li>
                                    <a href="{{ $item['url'] }}"
                                        class="tw-block tw-truncate tw-rounded-md tw-px-2.5 tw-py-1.5 tw-text-[13px] hover:tw-bg-white/5 hover:tw-text-nav-fg-active {{ $isActive($item) ? 'tw-bg-nav-active-bg tw-font-medium tw-text-nav-fg-active' : 'tw-text-nav-fg' }}"
                                        @if ($isActive($item)) aria-current="page" @endif>
                                        {{ $item['label'] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>

    @if (count($settingsNav))
        <div class="tw-shrink-0 tw-border-t tw-border-nav-border tw-p-2">
            <button type="button"
                class="sidebar-group-toggle tw-flex tw-w-full tw-items-center tw-gap-3 tw-rounded-md tw-px-2.5 tw-py-2 tw-text-sm tw-font-medium hover:tw-bg-white/5 hover:tw-text-nav-fg-active"
                data-group="settings" aria-expanded="false">
                @include('layouts.partials.sidebar_icon', ['icon' => 'settings'])
                <span class="sidebar-label tw-truncate">@lang('business.settings')</span>
            </button>

            <ul class="sidebar-group tw-hidden tw-ml-4 tw-mt-0.5 tw-pl-3 tw-border-l tw-border-nav-border"
                data-group="settings">
                @foreach ($settingsNav as $item)
                    <li>
                        <a href="{{ $item['url'] }}"
                            class="tw-block tw-truncate tw-rounded-md tw-px-2.5 tw-py-1.5 tw-text-[13px] tw-text-nav-fg hover:tw-bg-white/5 hover:tw-text-nav-fg-active">
                            {{ $item['label'] }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
</aside>

<style>
    .side-bar .sidebar-scroll::-webkit-scrollbar { width: 4px; }
    .side-bar .sidebar-scroll::-webkit-scrollbar-track { background: transparent; }
    .side-bar .sidebar-scroll::-webkit-scrollbar-thumb {
        background-color: rgb(var(--nav-fg) / 0.25);
        border-radius: 20px;
    }

    /* Rail mode: icons only. Replaces the old behaviour, which hid the whole
       sidebar and left no way back to it without knowing the shortcut. */
    .side-bar.is-rail { width: 68px; }
    .side-bar.is-rail .sidebar-label { display: none; }
    .side-bar.is-rail .sidebar-group { display: none !important; }
    .side-bar.is-rail a,
    .side-bar.is-rail .sidebar-group-toggle { justify-content: center; }
</style>

<script>
    (function () {
        var sidebar = document.querySelector('.side-bar');
        if (!sidebar) return;

        // Only one group open at a time, so the list never outgrows the viewport.
        sidebar.addEventListener('click', function (e) {
            var toggle = e.target.closest('.sidebar-group-toggle');
            if (!toggle) return;

            var group = toggle.dataset.group;
            var panel = sidebar.querySelector('.sidebar-group[data-group="' + group + '"]');
            if (!panel) return;

            var opening = panel.classList.contains('tw-hidden');

            sidebar.querySelectorAll('.sidebar-group').forEach(function (el) {
                el.classList.add('tw-hidden');
            });
            sidebar.querySelectorAll('.sidebar-group-toggle').forEach(function (el) {
                el.setAttribute('aria-expanded', 'false');
                var chevron = el.querySelector('.sidebar-chevron');
                if (chevron) chevron.classList.remove('tw-rotate-180');
            });

            if (opening) {
                panel.classList.remove('tw-hidden');
                toggle.setAttribute('aria-expanded', 'true');
                var chevron = toggle.querySelector('.sidebar-chevron');
                if (chevron) chevron.classList.add('tw-rotate-180');
            }
        });

        try {
            if (localStorage.getItem('upos_sidebar_collapse') === 'true') {
                sidebar.classList.add('is-rail');
            }
        } catch (err) {
            // Blocked storage: full-width sidebar is a fine default.
        }
    })();
</script>
