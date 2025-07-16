<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <!-- En-tête -->
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-800">Modifier l'attribut</h2>
                            <p class="text-sm text-gray-600 mt-1">Modifiez les propriétés de l'attribut
                                "{{ $attribute->name }}"</p>
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
                <form action="{{ route('settings.attributes.update', $attribute) }}" method="POST" class="p-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nom de l'attribut -->
                        <div class="col-span-1">
                            <label for="name" class="block text-sm font-medium text-gray-700">Nom de l'attribut
                                *</label>
                            <input type="text"
                                   name="name"
                                   id="name"
                                   value="{{ old('name', $attribute->name) }}"
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
                                <option
                                    value="number" {{ old('type', $attribute->type) == 'number' ? 'selected' : '' }}>
                                    Nombre
                                </option>
                                <option
                                    value="select" {{ old('type', $attribute->type) == 'select' ? 'selected' : '' }}>
                                    Sélection
                                </option>
                                <option value="color" {{ old('type', $attribute->type) == 'color' ? 'selected' : '' }}>
                                    Couleur
                                </option>
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
                                   value="{{ old('unit', $attribute->unit) }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('unit') border-red-300 @enderror"
                                   placeholder="Ex: GB, L, cm, kg...">
                            @error('unit')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Avertissement lors de la modification -->
                        <div class="col-span-2">
                            <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-amber-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                  d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                                  clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h4 class="text-sm font-medium text-amber-800">Attention : Modification
                                            d'attribut</h4>
                                        <div class="mt-1 text-sm text-amber-700">
                                            <p class="mb-2">La modification de cet attribut peut avoir des répercussions
                                                sur :</p>
                                            <ul class="list-disc list-inside space-y-1 text-xs">
                                                <li>Les articles existants utilisant cet attribut</li>
                                                <li>Les variants déjà créés</li>
                                                <li>L'affichage des valeurs dans l'interface</li>
                                            </ul>
                                            <p class="mt-2 font-medium">Il est recommandé de ne modifier que le nom ou
                                                l'unité si nécessaire.</p>
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
                                class="inline-flex items-center px-4 py-2 bg-amber-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-amber-600 focus:bg-amber-600 active:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M5 13l4 4L19 7"></path>
                            </svg>
                            Mettre à jour
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
