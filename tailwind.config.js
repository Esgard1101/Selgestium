import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            colors: {
                primary: 'var(--color-primary)',
                'primary-dark': 'var(--color-primary-dark)',
                'primary-light': 'var(--color-primary-light)',
                accent: 'var(--color-accent)',
                'accent-light': 'var(--color-accent-light)',
                bg: 'var(--color-bg)',
                surface: 'var(--color-surface)',
                border: 'var(--color-border)',
                'text-primary': 'var(--color-text-primary)',
                'text-secondary': 'var(--color-text-secondary)',
                'text-muted': 'var(--color-text-muted)',
                success: 'var(--color-success)',
                'success-bg': 'var(--color-success-bg)',
                warning: 'var(--color-warning)',
                'warning-bg': 'var(--color-warning-bg)',
                danger: 'var(--color-danger)',
                'danger-bg': 'var(--color-danger-bg)',
                neutral: 'var(--color-neutral)',
                'neutral-bg': 'var(--color-neutral-bg)',
                info: 'var(--color-info)',
            },
            fontFamily: {
                sans: ['var(--font-sans)', ...defaultTheme.fontFamily.sans],
                mono: ['var(--font-mono)', ...defaultTheme.fontFamily.mono],
            },
            fontSize: {
                xs: 'var(--text-xs)',
                sm: 'var(--text-sm)',
                base: 'var(--text-base)',
                lg: 'var(--text-lg)',
                xl: 'var(--text-xl)',
                '2xl': 'var(--text-2xl)',
            },
        },
    },

    plugins: [forms, typography],
};
