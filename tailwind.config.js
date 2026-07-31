import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
    ],
    theme: {
        extend: {
            colors: {
                primary: 'var(--color-primary)',
                accent: {
                    DEFAULT: 'var(--color-accent)',
                    hover: 'var(--color-accent-hover)',
                },
                cta: {
                    DEFAULT: 'var(--color-cta)',
                    hover: 'var(--color-cta-hover)',
                },
                peach: 'var(--color-peach)',
                mint: 'var(--color-mint)',
                muted: 'var(--color-text-muted)',
            },
            fontFamily: {
                sans: ['Raleway', ...defaultTheme.fontFamily.sans],
                display: ['Pridi', ...defaultTheme.fontFamily.serif],
                accent: ['Grenze', ...defaultTheme.fontFamily.serif],
            },
            maxWidth: {
                refugio: 'var(--container-max)',
            },
            height: {
                header: 'var(--header-height)',
            },
            transitionTimingFunction: {
                'out-expo': 'cubic-bezier(0.19, 1, 0.22, 1)',
                'out-cubic': 'cubic-bezier(0.215, 0.61, 0.355, 1)',
            },
            screens: {
                tablet: '768px',
                desktop: '1024px',
                wide: '1440px',
            },
        },
    },
    plugins: [],
};
