import { Config } from 'tailwindcss'

export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './public/js/**/*.js',
    ],
    safelist: [
        // Classes pour les couleurs dynamiques
        'bg-purple-100', 'bg-purple-600', 'bg-purple-900',
        'text-purple-400', 'text-purple-600', 'text-purple-800',
        'bg-violet-100', 'bg-violet-600', 'bg-violet-900',
        'text-violet-400', 'text-violet-600', 'text-violet-800',
        'bg-fuchsia-100', 'bg-fuchsia-600', 'bg-fuchsia-900',
        'text-fuchsia-400', 'text-fuchsia-600', 'text-fuchsia-800',
        // Ajoute toutes les variantes que tu utilises
        'dark:bg-purple-900/30', 'dark:text-purple-400',
        'dark:bg-violet-900/30', 'dark:text-violet-400',
        'dark:bg-fuchsia-900/30', 'dark:text-fuchsia-400',
    ],
    darkMode: 'class',
    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', 'ui-sans-serif', 'system-ui'],
            },
        },
    },
    plugins: [],
}
