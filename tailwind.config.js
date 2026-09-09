/**
 * Tailwind config for the main (Blade) application.
 *
 * IMPORTANT: the whole Blade codebase is written with the `tw-` prefix and
 * daisyUI's `dw-` sub-prefix (e.g. `tw-flex`, `tw-dw-btn`). Tailwind v4 cannot
 * reproduce that naming — its prefix syntax is `tw:flex` — so this project is
 * pinned to Tailwind v3.4 + daisyUI v4. Do not "upgrade" without rewriting
 * every class in resources/views, Modules and app/.
 *
 * Output: public/css/tailwind/app.css  (see `npm run build:css`)
 * That file is loaded by resources/views/layouts/partials/css.blade.php.
 *
 * @type {import('tailwindcss').Config}
 */
export default {
    prefix: 'tw-',
    darkMode: 'class',
    content: [
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
        './Modules/**/Resources/views/**/*.blade.php',
        // Controllers and helpers emit markup (DataTables action buttons etc.)
        // with tw- classes, so they are part of the content surface.
        './app/**/*.php',
        './public/js/**/*.js',
    ],
    // The business "theme colour" setting builds class names at render time
    // (e.g. tw-from-{{ $color }}-800 in layouts/partials/header.blade.php).
    // Tailwind cannot see those, so every option has to be safelisted.
    // Keep this list in sync with BusinessController::$theme_colors.
    // The four patterns below are the only ones the templates build; keeping
    // the list this tight costs ~2kB instead of ~250kB.
    safelist: [
        // Safelist patterns are matched against the final class name, so
        // they have to carry the `tw-` prefix too.
        { pattern: /^tw-bg-(primary|purple|green|red|yellow|orange|sky)-800$/ },
        { pattern: /^tw-bg-(primary|purple|green|red|yellow|orange|sky)-700$/, variants: ['hover'] },
        { pattern: /^tw-from-(primary|purple|green|red|yellow|orange|sky)-800$/ },
        { pattern: /^tw-to-(primary|purple|green|red|yellow|orange|sky)-900$/ },
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
            },
            colors: {
                gray: {
                    50: '#f9fafb',
                    100: '#f3f4f6',
                    200: '#e5e7eb',
                    300: '#d1d5db',
                    400: '#9ca3af',
                    500: '#6b7280',
                    600: '#4b5563',
                    700: '#374151',
                    800: '#1f2937',
                    900: '#111827',
                    950: '#030712',
                },
                primary: {
                    50: '#eef2ff',
                    100: '#e0e7ff',
                    200: '#c7d2fe',
                    300: '#a5b4fc',
                    400: '#818cf8',
                    500: '#6366f1',
                    600: '#4f46e5',
                    700: '#4338ca',
                    800: '#3730a3',
                    900: '#312e81',
                    950: '#1e1b4b',
                },
                dark: {
                    bg: '#0f172a',
                    surface: '#1e293b',
                    border: '#334155',
                    hover: '#334155',
                    text: {
                        primary: '#f1f5f9',
                        secondary: '#94a3b8',
                        muted: '#64748b',
                    },
                },
            },
            borderRadius: {
                xl: '0.75rem',
                '2xl': '1rem',
            },
            boxShadow: {
                card: '0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1)',
                'card-hover': '0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1)',
            },
        },
    },
    plugins: [require('daisyui')],
    daisyui: {
        prefix: 'dw-',
        themes: ['light'],
        logs: false,
    },
};
