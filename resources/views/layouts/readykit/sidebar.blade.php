<app-sidebar :data="{{ json_encode($sidebarData ?? []) }}"
             :logo="{{ json_encode(config('settings.application.company_logo', asset('readykit/images/logo.png'))) }}"
             :logo-icon="{{ json_encode(config('settings.application.company_icon', asset('readykit/images/icon.png'))) }}">
</app-sidebar>
