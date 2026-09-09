export default {
    plugins: {
        // The Vite/Inertia surface uses unprefixed classes and its own config.
        // The main Blade stylesheet is built separately by `npm run build:css`
        // with tailwind.config.js (prefix: 'tw-').
        tailwindcss: { config: './tailwind.inertia.config.js' },
        autoprefixer: {},
    },
};
