// Dans resources/js/utils/loading.js
export const LoadingUtils = {
    async getLoadingHtml(message = 'Chargement...', size = 'medium') {
        try {
            const response = await fetch(`/api/loading-spinner?message=${encodeURIComponent(message)}&size=${size}`);
            return await response.text();
        } catch (error) {
            // Fallback simple en cas d'erreur
            return `
                <div class="flex items-center justify-center py-8 space-x-3">
                    <div class="animate-spin rounded-full w-8 h-8 border-b-2 border-blue-500"></div>
                    <span class="text-gray-600 dark:text-gray-400">${message}</span>
                </div>
            `;
        }
    },

    // Version synchrone avec cache
    getLoadingHtmlSync(message = 'Chargement...', size = 'medium') {
        // Cache basique
        const cacheKey = `loading-${size}`;
        if (!this._cache) this._cache = {};

        if (!this._cache[cacheKey]) {
            // Fetch et cache pour les prochaines utilisations
            this.getLoadingHtml('', size).then(html => {
                this._cache[cacheKey] = html.replace('Chargement...', '{{MESSAGE}}');
            });
        }

        return this._cache[cacheKey]?.replace('{{MESSAGE}}', message) || this._fallback(message, size);
    },

    _fallback(message, size) {
        const sizeClasses = { small: 'w-4 h-4', medium: 'w-8 h-8', large: 'w-12 h-12' };
        return `
            <div class="flex items-center justify-center py-8 space-x-3">
                <div class="animate-spin rounded-full ${sizeClasses[size]} border-b-2 border-blue-500"></div>
                <span class="text-gray-600 dark:text-gray-400">${message}</span>
            </div>
        `;
    }
};
