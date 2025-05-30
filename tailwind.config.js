import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import plugin from 'tailwindcss/plugin'

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            animation: {
                'fade-in': 'fadeIn 0.2s ease-out',
                'flash': 'flash 1.5s infinite ease-in-out',
                'fade-bounce': 'fadeBounce 1.2s infinite ease-in-out',
                'slide-right': 'slideRight 0.3s ease-in-out',
            },
            keyframes: {
                fadeIn: {
                    '0%': { opacity: '0', transform: 'translateY(10px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                flash: {
                    '0%, 100%': { opacity: '1', transform: 'scale(1)' },
                    '50%': { opacity: '0.3', transform: 'scale(1.1)' },
                },
                fadeBounce: {
                    '0%, 100%': { opacity: '1' },
                    '50%': { opacity: '0' },
                },
                slideRight: {
                    '0%': {
                        transform: 'translateX(100%)',
                        opacity: '0',
                    },
                    '100%': {
                        transform: 'translateX(0)',
                        opacity: '1',
                    },
                },
            },
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [
        plugin(function ({ addUtilities }) {
            addUtilities({
                '.icon-success': {
                    color: '#4caf50',
                },
                '.icon-error': {
                    color: '#f44336',
                },
                '.icon-danger': {
                    color: '#ff9800',
                },
                '.icon-info': {
                    color: '#2196f3',
                },
            })
        }),
        forms],
};
