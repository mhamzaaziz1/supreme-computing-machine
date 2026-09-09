/**
 * Tailwind config for the Vite/Inertia (Vue) surface.
 *
 * The Vue pages use UNPREFIXED classes, unlike the Blade app, so this needs
 * its own config. Colours resolve to the CSS custom properties defined in
 * resources/css/tokens.css, which means light/dark and rebranding are a
 * token swap rather than a find-and-replace across components.
 *
 * @type {import('tailwindcss').Config}
 */

/** Lets `bg-brand-600/40` work against a `R G B` custom property. */
const token = (name) => `rgb(var(--${name}) / <alpha-value>)`;

const ramp = (name) =>
    Object.fromEntries(
        [50, 100, 200, 300, 400, 500, 600, 700, 800, 900, 950].map((step) => [
            step,
            token(`${name}-${step}`),
        ]),
    );

export default {
    content: [
        './resources/js/**/*.vue',
        './resources/js/**/*.js',
        './resources/views/app.blade.php',
    ],
    darkMode: 'class',
    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter var', 'Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                mono: ['ui-monospace', 'SFMono-Regular', 'Menlo', 'monospace'],
            },
            colors: {
                brand: ramp('brand'),
                accent: ramp('accent'),
                neutral: { 0: token('neutral-0'), ...ramp('neutral') },

                success: token('success'),
                warning: token('warning'),
                danger: token('danger'),
                info: token('info'),

                surface: {
                    page: token('surface-page'),
                    raised: token('surface-raised'),
                    sunken: token('surface-sunken'),
                    inverse: token('surface-inverse'),
                },
                edge: {
                    subtle: token('border-subtle'),
                    strong: token('border-strong'),
                },
                content: {
                    primary: token('text-primary'),
                    secondary: token('text-secondary'),
                    muted: token('text-muted'),
                    inverse: token('text-inverse'),
                },
                nav: {
                    bg: token('nav-bg'),
                    fg: token('nav-fg'),
                    'fg-active': token('nav-fg-active'),
                    'active-bg': token('nav-active-bg'),
                    border: token('nav-border'),
                },
            },
            // Tabular figures matter here: this app is mostly columns of money.
            fontVariantNumeric: {
                tabular: 'tabular-nums',
            },
            boxShadow: {
                raised: '0 1px 2px 0 rgb(2 6 23 / 0.06), 0 1px 3px 0 rgb(2 6 23 / 0.08)',
                overlay: '0 10px 32px -8px rgb(2 6 23 / 0.24), 0 4px 12px -4px rgb(2 6 23 / 0.12)',
            },
        },
    },
    plugins: [],
};
