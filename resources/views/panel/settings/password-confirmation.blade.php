<x-app-layout>
    <div
        class="min-h-screen flex items-center justify-center bg-gradient-to-r from-violet-50 to-blue-50 dark:from-blue-950 dark:to-violet-950">
        <div class="max-w-md w-full space-y-8">
            <div class="bg-white dark:bg-gray-800 shadow-xl rounded-lg p-8">
                <!-- En-tête -->
                <div class="text-center mb-8">
                    <div
                        class="mx-auto h-16 w-16 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-lock text-2xl text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                        Accès aux paramètres
                    </h2>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        Veuillez confirmer votre mot de passe pour accéder aux paramètres du système
                    </p>
                </div>

                <!-- Formulaire -->
                <form method="POST" action="{{ route('settings.index') }}" class="space-y-6">
                    @csrf

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Mot de passe
                        </label>
                        <div class="relative">
                            <input id="password"
                                   name="password"
                                   type="password"
                                   required
                                   autocomplete="current-password"
                                   class="appearance-none relative block w-full px-3 py-3 border border-gray-300 dark:border-gray-600 placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-white bg-white dark:bg-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm transition-colors"
                                   placeholder="Entrez votre mot de passe">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <button type="button"
                                        onclick="togglePassword()"
                                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 focus:outline-none">
                                    <i id="password-icon" class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        @error('password')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">
                            {{ $message }}
                        </p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between">
                        <a href="{{ route('dashboard') }}"
                           class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                            <i class="fas fa-arrow-left mr-1"></i>
                            Retour au dashboard
                        </a>
                    </div>

                    <div>
                        <button type="submit"
                                class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                <i class="fas fa-unlock text-blue-500 group-hover:text-blue-400"></i>
                            </span>
                            Confirmer l'accès
                        </button>
                    </div>
                </form>

                <!-- Informations de sécurité -->
                <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-shield-alt text-blue-400"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">
                                Sécurité
                            </h3>
                            <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                                <p>Cette confirmation est requise pour accéder aux paramètres sensibles du système.
                                    Votre mot de passe ne sera pas stocké.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const passwordIcon = document.getElementById('password-icon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.classList.remove('fa-eye');
                passwordIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                passwordIcon.classList.remove('fa-eye-slash');
                passwordIcon.classList.add('fa-eye');
            }
        }

        // Focus automatique sur le champ mot de passe
        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('password').focus();
        });
    </script>
</x-app-layout>
