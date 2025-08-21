import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';
import plugin from 'tailwindcss/plugin';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    darkMode: 'class',

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    midnight: '#0A0F2C',
                    cyan: '#00F0FF',
                    magenta: '#FF00A8',
                    electric: '#39FF14',
                    offwhite: '#F2F2F2',
                    graph: '#1A1A1A',
                },
                dark: {
                    50: '#f8fafc',
                    100: '#f1f5f9',
                    200: '#e2e8f0',
                    300: '#cbd5e1',
                    400: '#94a3b8',
                    500: '#64748b',
                    600: '#475569',
                    700: '#334155',
                    800: '#1e293b',
                    900: '#0f172a',
                    950: '#020617',
                }
            },
            backgroundImage: {
                'gradient-brand': 'linear-gradient(135deg, #0A67FF 0%, #00F0FF 40%, #7C3AED 100%)',
                'gradient-brand-dark': 'linear-gradient(135deg, #0A0F2C 0%, #1B1F4A 35%, #3A0CA3 70%, #0cf 100%)',
                'gradient-cyber': 'conic-gradient(from 180deg at 50% 50%, #00F0FF, #FF00A8, #00F0FF)'
            },
            boxShadow: {
                glow: '0 0 12px rgba(0, 240, 255, 0.6), 0 0 32px rgba(255, 0, 168, 0.35)'
            }
        },
    },

    plugins: [
        forms,
        typography,
        plugin(function({ addUtilities, theme }) {
            const newUtilities = {
                '.text-gradient-brand': {
                    backgroundImage: theme('backgroundImage.gradient-brand'),
                    WebkitBackgroundClip: 'text',
                    backgroundClip: 'text',
                    color: 'transparent',
                },
                '.neon-glow': {
                    boxShadow: theme('boxShadow.glow'),
                },
                '.btn-brand': {
                    background: 'linear-gradient(90deg, #00F0FF, #FF00A8)',
                    color: '#fff',
                    fontWeight: '600',
                    padding: '0.75rem 1.5rem',
                    borderRadius: '0.75rem',
                },
            };
            addUtilities(newUtilities);
        }),
    ],
};
