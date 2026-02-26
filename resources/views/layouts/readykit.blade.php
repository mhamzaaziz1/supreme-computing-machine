<html dir="{{ config('settings.application.layout', 'ltr') }}" lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="UTF-8"/>
        <meta name="viewport"
              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0"/>
        <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
        <title>@yield('title') - {{ config('app.name') }}</title>
        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" />

        @include('layouts.readykit.header')
    </head>
    <body>
        <div id="app">
            <div class="container-scroller">
                <!--Top Navbar-->
                @section('nav-bar')
                    @include('layouts.readykit.navbar')
                @show

                <!--Sidebar-->
                @section('side-bar')
                    @include('layouts.readykit.sidebar')
                @show

                <div class="container-fluid page-body-wrapper">
                    <div class="main-panel">
                            <!--Contents-->
                            @yield('contents')

                    </div>
                </div>
            </div>
        </div>

        @include('layouts.readykit.footer')
    </body>
</html>
