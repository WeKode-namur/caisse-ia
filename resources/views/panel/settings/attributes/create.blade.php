<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <!-- En-tête -->
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-800">Créer un nouvel attribut</h2>
                            <p class="text-sm text-gray-600 mt-1">Définissez les propriétés de votre attribut</p>
                        </div>
                        <a href="{{ route('settings.attributes.index') }}"
                           class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Retour
                        </a>
                    </div>
                </div>

                <!-- Formulaire -->
                <form action="{{ route('settings.attributes.store') }}" method="POST" class="p-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nom de l'attribut -->
                        <div class="col-span-1">
                            <label for="name" class="block text-sm font-medium text-gray-700">Nom de l'attribut
                                *</label>
                            <input type="text"
                                   name="name"
                                   id="name"
                                   value="{{ old('name') }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('name') border-red-300 @enderror"
                                   placeholder="Ex: Couleur, Taille, Marque...">
                            @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Type d'attribut -->
                        <div class="col-span-1">
                            <label for="type" class="block text-sm font-medium text-gray-700">Type d'attribut <span
                                    class="text-red-500">*</span></label>
                            <select name="type"
                                    id="type"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('type') border-red-300 @enderror">
                                <option value="">Sélectionnez un type</option>
                                <option value="number" {{ old('type') == 'number' ? 'selected' : '' }}>Nombre</option>
                                <option value="select" {{ old('type') == 'select' ? 'selected' : '' }}>Sélection
                                </option>
                                <option value="color" {{ old('type') == 'color' ? 'selected' : '' }}>Couleur</option>
                            </select>
                            @error('type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Unité -->
                        <div class="col-span-1">
                            <label for="unit" class="block text-sm font-medium text-gray-700">Unité</label>
                            <input type="text"
                                   name="unit"
                                   id="unit"
                                   value="{{ old('unit') }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('unit') border-red-300 @enderror"
                                   placeholder="Ex: GB, L, cm, kg...">
                            @error('unit')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Info-bulle explicative -->
                        <div class="col-span-2">
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                  d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                  clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h4 class="text-sm font-medium text-blue-800">Comment créer un attribut ?</h4>
                                        <div class="mt-1 text-sm text-blue-700">
                                            <ul class="list-disc list-inside space-y-1">
                                                <li><strong>Nombre :</strong> Pour les valeurs numériques (ex: taille,
                                                    poids, capacité)
                                                </li>
                                                <li><strong>Sélection :</strong> Pour une liste de choix (ex: marque,
                                                    matériau, style)
                                                </li>
                                                <li><strong>Couleur :</strong> Pour les attributs de couleur avec
                                                    sélecteur visuel
                                                </li>
                                                <li><strong>Unité :</strong> Facultatif, pour préciser l'unité de mesure
                                                    (ex: GB pour stockage, L pour volume)
                                                </li>
                                            </ul>
                                            <p class="mt-2 font-medium">Après création, vous pourrez ajouter les valeurs
                                                possibles pour cet attribut.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Boutons d'action -->
                    <div class="mt-6 flex items-center justify-end space-x-3">
                        <a href="{{ route('settings.attributes.index') }}"
                           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                            Annuler
                        </a>
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M5 13l4 4L19 7"></path>
                            </svg>
                            Créer l'attribut
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
