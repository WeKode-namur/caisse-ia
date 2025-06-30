<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="flex justify-between items-center px-6 py-4 border-b border-gray-200">
                    <div>
                        <h1 class="text-lg font-semibold text-gray-900">
                            Modifier l'entreprise
                        </h1>
                        <div class="text-gray-600 dark:text-gray-400 text-sm font-medium">{{ ($company->company_type ? $company->company_type . ' ' : '' ) . $company->name }}</div>
                    </div>
                    <a href="{{ route('clients.companies.show', $company) }}"
                       class="bg-gray-600 hover:bg-gray-800 text-white font-bold py-2 px-4 rounded hover:scale-105 duration-500">
                        <i class="fas fa-arrow-left me-1"></i>
                        Retour
                    </a>
                </div>

                <form method="POST" action="{{ route('clients.companies.update', $company) }}" class="p-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nom de l'entreprise -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Nom de l'entreprise *</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $company->name) }}" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                            @error('name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Nom légal -->
                        <div>
                            <label for="legal_name" class="block text-sm font-medium text-gray-700">Nom légal</label>
                            <input type="text" name="legal_name" id="legal_name" value="{{ old('legal_name', $company->legal_name) }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                            @error('legal_name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Numéro d'entreprise -->
                        <div>
                            <label for="company_number_be" class="block text-sm font-medium text-gray-700">Numéro d'entreprise BE</label>
                            <input type="text" name="company_number_be" id="company_number_be" value="{{ old('company_number_be', $company->company_number_be) }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                            @error('company_number_be')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Numéro de TVA -->
                        <div>
                            <label for="vat_number" class="block text-sm font-medium text-gray-700">Numéro de TVA</label>
                            <input type="text" name="vat_number" id="vat_number" value="{{ old('vat_number', $company->vat_number) }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                            @error('vat_number')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Type d'entreprise -->
                        <div>
                            <label for="company_type" class="block text-sm font-medium text-gray-700">Type d'entreprise</label>
                            <select name="company_type" id="company_type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                                <option value="">Sélectionner</option>
                                @foreach(\App\Models\Company::COMPANY_TYPES as $value => $label)
                                    <option value="{{ $value }}" {{ old('company_type', $company->company_type) === $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('company_type')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Représentant légal -->
                        <div>
                            <label for="legal_representative" class="block text-sm font-medium text-gray-700">Représentant légal</label>
                            <input type="text" name="legal_representative" id="legal_representative" value="{{ old('legal_representative', $company->legal_representative) }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                            @error('legal_representative')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" name="email" id="email" value="{{ old('email', $company->email) }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                            @error('email')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Téléphone -->
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700">Téléphone</label>
                            <input type="tel" name="phone" id="phone" value="{{ old('phone', $company->phone) }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                            @error('phone')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Conditions de paiement -->
                        <div>
                            <label for="payment_terms" class="block text-sm font-medium text-gray-700">Conditions de paiement (jours)</label>
                            <input type="number" name="payment_terms" id="payment_terms" value="{{ old('payment_terms', $company->payment_terms) }}" min="0" max="365"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                            @error('payment_terms')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Limite de crédit -->
                        <div>
                            <label for="credit_limit" class="block text-sm font-medium text-gray-700">Limite de crédit (€)</label>
                            <input type="number" name="credit_limit" id="credit_limit" value="{{ old('credit_limit', $company->credit_limit) }}" min="0" step="0.01"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                            @error('credit_limit')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Notes -->
                        <div class="md:col-span-2">
                            <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                            <textarea name="notes" id="notes" rows="3"
                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">{{ old('notes', $company->notes) }}</textarea>
                            @error('notes')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>

                    <!-- Boutons d'action -->
                    <div class="mt-8 flex justify-end space-x-3">
                        <a href="{{ route('clients.companies.show', $company) }}"
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
