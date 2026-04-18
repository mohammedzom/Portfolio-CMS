import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                mono: ['Fira Code', ...defaultTheme.fontFamily.mono],
            },
            colors: {
                // Subtle Cyberpunk Palette: Zinc/Emerald
                dark: {
                    950: '#09090b', // Zinc 950
                    900: '#18181b', // Zinc 900
                    800: '#27272a', // Zinc 800
                    700: '#3f3f46', // Zinc 700
                    600: '#52525b', // Zinc 600
                    500: '#71717a', // Zinc 500
                    400: '#a1a1aa', // Zinc 400
                    300: '#d4d4d8', // Zinc 300
                    200: '#e4e4e7', // Zinc 200
                    100: '#f4f4f5', // Zinc 100
                },
                neon: {
                    500: '#10b981', // Emerald 500
                    400: '#34d399', // Emerald 400
                    600: '#059669', // Emerald 600
                },
            },
            backgroundImage: {
                'cyber-grid': "radial-gradient(circle at 2px 2px, rgba(16, 185, 129, 0.05) 1px, transparent 0)",
            },
            boxShadow: {
                'neon-sm': '0 0 10px rgba(16, 185, 129, 0.1)',
                'neon-md': '0 0 20px rgba(16, 185, 129, 0.2)',
                'neon-lg': '0 0 30px rgba(16, 185, 129, 0.3)',
            },
        },
    },

    plugins: [forms],
};
