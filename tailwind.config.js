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
        'bg-purple-100', 'bg-purple-600', 'bg-purple-900', // Pruple
        'text-purple-400', 'text-purple-600', 'text-purple-800',
        'bg-violet-100', 'bg-violet-600', 'bg-violet-900', // Violet
        'text-violet-400', 'text-violet-600', 'text-violet-800',
        'bg-fuchsia-100', 'bg-fuchsia-600', 'bg-fuchsia-900', // Fuchsia
        'text-fuchsia-400', 'text-fuchsia-600', 'text-fuchsia-800',
        'bg-red-100', 'bg-red-600', 'bg-red-900', // Rouge
        'text-red-100', 'text-red-600', 'text-red-900',
        'bg-green-100', 'bg-green-600', 'bg-green-900', // Vert
        'text-green-100', 'text-green-600', 'text-green-900',
        'bg-amber-100', 'bg-amber-600', 'bg-amber-900', // Ambre
        'text-amber-100', 'text-amber-600', 'text-amber-900',
        // Ajoute toutes les variantes que tu utilises
        'dark:bg-purple-900/30', 'dark:text-purple-400',
        'dark:bg-violet-900/30', 'dark:text-violet-400',
        'dark:bg-fuchsia-900/30', 'dark:text-fuchsia-400',
        'dark:bg-red-900/30', 'dark:text-red-400',
        'dark:bg-green-900/30', 'dark:text-green-400',
        'dark:bg-amber-900/30', 'dark:text-amber-400',
        // Size
        'max-w-xs', 'max-w-sm', 'max-w-md', 'max-w-lg', 'max-w-xl',
        'max-w-2xl', 'max-w-3xl','max-w-4xl', 'max-w-5xl', 'max-w-6xl', 'max-w-7xl',
    ],
    darkMode: 'class',
    theme: {
        extend: {
            boxShadow: {
                // Votre ombre inset personnalisée
                'inset-custom': 'inset 0 0 10px #999',
                'inset-custom-dark': 'inset 0 0 10px #333',
            },
            fontFamily: {
                sans: ['Figtree', 'ui-sans-serif', 'system-ui'],
            },
        },
    },
    plugins: [
        require('@tailwindcss/typography'),
    ],
}
