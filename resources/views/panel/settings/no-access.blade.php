<x-app-layout>
    <div
        class="min-h-screen flex items-center justify-center bg-gradient-to-r from-violet-50 to-blue-50 dark:from-blue-950 dark:to-violet-950">
        <div class="max-w-md w-full space-y-8">
            <div class="bg-white dark:bg-gray-800 shadow-xl rounded-lg p-8 text-center">
                <!-- Icône d'accès refusé -->
                <div
                    class="mx-auto h-20 w-20 bg-red-100 dark:bg-red-900 rounded-full flex items-center justify-center mb-6">
                    <i class="fas fa-ban text-3xl text-red-600 dark:text-red-400"></i>
                </div>

                <!-- Titre -->
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                    Accès refusé
                </h2>

                <!-- Message -->
                <p class="text-gray-600 dark:text-gray-400 mb-6">
                    Vous n'avez pas les permissions nécessaires pour accéder aux paramètres du système.
                </p>

                <!-- Niveau d'admin requis -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6">
                    <div class="flex items-center justify-center space-x-2">
                        <i class="fas fa-shield-alt text-gray-500"></i>
                        <span class="text-sm text-gray-600 dark:text-gray-400">
                            Niveau d'administrateur requis : <span class="font-semibold text-gray-900 dark:text-white">80</span>
                        </span>
                    </div>
                    <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                        Votre niveau actuel : <span class="font-semibold">{{ Auth::user()->is_admin }}</span>
                    </div>
                </div>

                <!-- Actions -->
                <div class="space-y-3">
                    <a href="{{ route('dashboard') }}"
                       class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        <i class="fas fa-home mr-2"></i>
                        Retour au dashboard
                    </a>

                    <a href="{{ route('profile.show') }}"
                       class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors">
                        <i class="fas fa-user-cog mr-2"></i>
                        Mon profil
                    </a>
                </div>

                <!-- Contact support -->
                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-600">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">
                        Besoin d'accès aux paramètres ?
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Contactez votre administrateur système ou le support technique.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
