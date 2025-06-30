<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="flex justify-between items-center px-6 py-4 border-b border-gray-200">
                    <div>
                        <h1 class="text-lg font-semibold text-gray-900">Modifier le client</h1>
                        <div class="text-gray-600 dark:text-gray-400 text-sm font-medium">{{ $customer->full_name }}</div>
                    </div>
                    <a href="{{ route('clients.customers.show', $customer) }}"
                       class="bg-gray-600 hover:bg-gray-800 text-white font-bold py-2 px-4 rounded hover:scale-105 duration-500">
                        <i class="fas fa-arrow-left me-1"></i>
                        Retour
                    </a>
                </div>

                <form action="{{ route('clients.customers.update', $customer) }}" method="POST" class="p-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Informations personnelles -->
                        <div class="space-y-4">
                            <h4 class="text-md font-medium text-gray-900">Informations personnelles</h4>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="first_name" class="block text-sm font-medium text-gray-700">Prénom *</label>
                                    <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $customer->first_name) }}" required
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    @error('first_name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="last_name" class="block text-sm font-medium text-gray-700">Nom *</label>
                                    <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $customer->last_name) }}" required
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    @error('last_name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <label for="gender" class="block text-sm font-medium text-gray-700">Genre</label>
                                <select name="gender" id="gender" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Sélectionner...</option>
                                    @foreach(\App\Models\Customer::GENDERS as $value => $label)
                                        <option value="{{ $value }}" {{ old('gender', $customer->gender) == $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('gender')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="birth_date" class="block text-sm font-medium text-gray-700">Date de naissance</label>
                                <input type="date" name="birth_date" id="birth_date" value="{{ old('birth_date', $customer->birth_date ? $customer->birth_date->format('Y-m-d') : '') }}"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('birth_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Informations de contact -->
                        <div class="space-y-4">
                            <h4 class="text-md font-medium text-gray-900">Informations de contact</h4>

                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">Email *</label>
                                <input type="email" name="email" id="email" value="{{ old('email', $customer->email) }}" required
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700">Téléphone</label>
                                <input type="tel" name="phone" id="phone" value="{{ old('phone', $customer->phone) }}"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('phone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Options -->
                    <div class="mt-6 space-y-4">
                        <h4 class="text-md font-medium text-gray-900">Options</h4>

                        <div class="flex items-center">
                            <input type="checkbox" name="marketing_consent" id="marketing_consent" value="1" {{ old('marketing_consent', $customer->marketing_consent) ? 'checked' : '' }}
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="marketing_consent" class="ml-2 block text-sm text-gray-900">
                                Consentement marketing (RGPD)
                            </label>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="mt-6">
                        <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                        <textarea name="notes" id="notes" rows="3"
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Informations supplémentaires...">{{ old('notes', $customer->notes) }}</textarea>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Boutons -->
                    <div class="mt-6 flex justify-end space-x-3">
                        <a href="{{ route('clients.customers.show', $customer) }}"
                           class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded hover:scale-105 duration-500">
                            Annuler
                        </a>
                        <button type="submit"
                                class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded hover:scale-105 duration-500">
                            Mettre à jour
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
