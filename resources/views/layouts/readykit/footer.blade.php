<footer>
    <script>
        window.settings = @json($settings ?? []);
    </script>

    @stack('before-scripts')

    <script>
        window.localStorage.setItem('app-language', '{{ app()->getLocale() ?? "en" }}');
        window.localStorage.setItem('base_url', '{{ request()->root() }}');
    </script>
    
    <script src="https://js.stripe.com/v3/"></script>
    <script src="{{ asset('readykit/js/manifest.js') }}"></script>
    <script src="{{ asset('readykit/js/vendor.js') }}"></script>
    <script src="{{ asset('readykit/js/core.js') }}"></script>
    <script src="{{ asset('readykit/vendor/summernote/summernote-bs4.js') }}"></script>

    @stack('after-scripts')
</footer>
