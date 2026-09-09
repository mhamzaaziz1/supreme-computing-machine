/**
 * Tailwind config for the Vite/Inertia (Vue) surface only.
 *
 * The Vue pages under resources/js/Pages use UNPREFIXED Tailwind classes,
 * so they need their own config. This is compiled by PostCSS via
 * resources/css/app.css and emitted into public/build by Vite.
 *
 * @type {import('tailwindcss').Config}
 */
export default {
    content: [
        './resources/js/**/*.vue',
        './resources/js/**/*.js',
        './resources/views/app.blade.php',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
            },
        },
    },
    plugins: [],
};
