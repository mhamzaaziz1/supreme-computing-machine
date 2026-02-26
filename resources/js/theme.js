// Theme handling logic
const themeController = {
    init: function () {
        // Check for saved theme preference or system preference
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }

        // Add listener for theme toggle buttons
        document.addEventListener('click', function (e) {
            const toggle = e.target.closest('[data-theme-toggle]');
            if (toggle) {
                themeController.toggle();
            }
        });
    },

    toggle: function () {
        if (document.documentElement.classList.contains('dark')) {
            document.documentElement.classList.remove('dark');
            localStorage.theme = 'light';
        } else {
            document.documentElement.classList.add('dark');
            localStorage.theme = 'dark';
        }

        // Dispatch event for other components to listen to
        window.dispatchEvent(new CustomEvent('theme-changed', {
            detail: { theme: localStorage.theme }
        }));
    }
};

// Initialize theme on load
themeController.init();

// Export for usage in other modules if needed (though it auto-inits)
window.themeController = themeController;
