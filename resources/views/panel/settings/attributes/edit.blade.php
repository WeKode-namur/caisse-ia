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
                            <label for="type" class="block text-sm font-medium text-gray-700">Type d'attribut *</label>
                            <select name="type"
                                    id="type"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('type') border-red-300 @enderror">
                                <option value="">Sélectionnez un type</option>
                                <option value="text" {{ old('type', $attribute->type) == 'text' ? 'selected' : '' }}>
                                    Texte
                                </option>
                                <option
                                    value="number" {{ old('type', $attribute->type) == 'number' ? 'selected' : '' }}>
                                    Nombre
                                </option>
                                <option
                                    value="select" {{ old('type', $attribute->type) == 'select' ? 'selected' : '' }}>
                                    Sélection
                                </option>
                                <option
                                    value="boolean" {{ old('type', $attribute->type) == 'boolean' ? 'selected' : '' }}>
                                    Oui/Non
                                </option>
                                <option value="date" {{ old('type', $attribute->type) == 'date' ? 'selected' : '' }}>
                                    Date
                                </option>
                            </select>
                            @error('type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="col-span-2">
                            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea name="description"
                                      id="description"
                                      rows="3"
                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('description') border-red-300 @enderror"
                                      placeholder="Description optionnelle de l'attribut...">{{ old('description', $attribute->description) }}</textarea>
                            @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Options -->
                        <div class="col-span-2">
                            <div class="flex items-center space-x-6">
                                <div class="flex items-center">
                                    <input type="checkbox"
                                           name="is_required"
                                           id="is_required"
                                           value="1"
                                           {{ old('is_required', $attribute->is_required) ? 'checked' : '' }}
                                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    <label for="is_required" class="ml-2 block text-sm text-gray-900">
                                        Attribut requis
                                    </label>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox"
                                           name="is_searchable"
                                           id="is_searchable"
                                           value="1"
                                           {{ old('is_searchable', $attribute->is_searchable) ? 'checked' : '' }}
                                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    <label for="is_searchable" class="ml-2 block text-sm text-gray-900">
                                        Recherchable
                                    </label>
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
                            Mettre à jour
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
