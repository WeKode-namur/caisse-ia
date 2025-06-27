<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Nouveau Client') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Créer un nouveau client</h3>
                </div>

                <form method="POST" action="{{ route('clients.store') }}" class="p-6">
                    @csrf

                    <!-- Sélection du type de client -->
                    <div class="mb-8">
                        <label class="block text-sm font-medium text-gray-700 mb-4">Type de client</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <label class="relative flex cursor-pointer rounded-lg border border-gray-300 bg-white p-4 shadow-sm focus:outline-none hover:border-blue-500">
                                <input type="radio" name="client_type" value="customer" class="sr-only" {{ old('client_type') === 'customer' ? 'checked' : '' }}>
                                <span class="flex flex-1">
                                    <span class="flex flex-col">
                                        <span class="block text-sm font-medium text-gray-900">Client Particulier</span>
                                        <span class="mt-1 flex items-center text-sm text-gray-500">
                                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                            Personne physique
                                        </span>
                                    </span>
                                </span>
                                <svg class="h-5 w-5 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </label>

                            <label class="relative flex cursor-pointer rounded-lg border border-gray-300 bg-white p-4 shadow-sm focus:outline-none hover:border-green-500">
                                <input type="radio" name="client_type" value="company" class="sr-only" {{ old('client_type') === 'company' ? 'checked' : '' }}>
                                <span class="flex flex-1">
                                    <span class="flex flex-col">
                                        <span class="block text-sm font-medium text-gray-900">Entreprise</span>
                                        <span class="mt-1 flex items-center text-sm text-gray-500">
                                            <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                            </svg>
                                            Personne morale
                                        </span>
                                    </span>
                                </span>
                                <svg class="h-5 w-5 text-green-600" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </label>
                        </div>
                        @error('client_type')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Formulaire Client Particulier -->
                    <div id="customer-form" class="hidden">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Genre -->
                            <div>
                                <label for="gender" class="block text-sm font-medium text-gray-700">Genre</label>
                                <select name="gender" id="gender" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Sélectionner</option>
                                    <option value="M" {{ old('gender') === 'M' ? 'selected' : '' }}>Masculin</option>
                                    <option value="F" {{ old('gender') === 'F' ? 'selected' : '' }}>Féminin</option>
                                    <option value="O" {{ old('gender') === 'O' ? 'selected' : '' }}>Autre</option>
                                </select>
                            </div>

                            <!-- Date de naissance -->
                            <div>
                                <label for="birth_date" class="block text-sm font-medium text-gray-700">Date de naissance</label>
                                <input type="date" name="birth_date" id="birth_date" value="{{ old('birth_date') }}" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <!-- Prénom -->
                            <div>
                                <label for="first_name" class="block text-sm font-medium text-gray-700">Prénom *</label>
                                <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}" required
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('first_name')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Nom -->
                            <div>
                                <label for="last_name" class="block text-sm font-medium text-gray-700">Nom *</label>
                                <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}" required
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('last_name')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="customer_email" class="block text-sm font-medium text-gray-700">Email *</label>
                                <input type="email" name="email" id="customer_email" value="{{ old('email') }}" required
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('email')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Téléphone -->
                            <div>
                                <label for="customer_phone" class="block text-sm font-medium text-gray-700">Téléphone</label>
                                <input type="tel" name="phone" id="customer_phone" value="{{ old('phone') }}"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('phone')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Consentement marketing -->
                            <div class="md:col-span-2">
                                <div class="flex items-center">
                                    <input type="checkbox" name="marketing_consent" id="marketing_consent" value="1" {{ old('marketing_consent') ? 'checked' : '' }}
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <label for="marketing_consent" class="ml-2 block text-sm text-gray-900">
                                        Consentement pour les communications marketing
                                    </label>
                                </div>
                            </div>

                            <!-- Notes -->
                            <div class="md:col-span-2">
                                <label for="customer_notes" class="block text-sm font-medium text-gray-700">Notes</label>
                                <textarea name="notes" id="customer_notes" rows="3" 
                                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('notes') }}</textarea>
                            </div>

                            <!-- Statut actif -->
                            <div class="md:col-span-2">
                                <div class="flex items-center">
                                    <input type="checkbox" name="is_active" id="customer_is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <label for="customer_is_active" class="ml-2 block text-sm text-gray-900">
                                        Client actif
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Formulaire Entreprise -->
                    <div id="company-form" class="hidden">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nom de l'entreprise -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">Nom de l'entreprise *</label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                                @error('name')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Nom légal -->
                            <div>
                                <label for="legal_name" class="block text-sm font-medium text-gray-700">Nom légal</label>
                                <input type="text" name="legal_name" id="legal_name" value="{{ old('legal_name') }}"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                                @error('legal_name')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Numéro d'entreprise -->
                            <div>
                                <label for="company_number_be" class="block text-sm font-medium text-gray-700">Numéro d'entreprise BE</label>
                                <input type="text" name="company_number_be" id="company_number_be" value="{{ old('company_number_be') }}"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                                @error('company_number_be')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Numéro de TVA -->
                            <div>
                                <label for="vat_number" class="block text-sm font-medium text-gray-700">Numéro de TVA</label>
                                <input type="text" name="vat_number" id="vat_number" value="{{ old('vat_number') }}"
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
                                    <option value="SPRL" {{ old('company_type') === 'SPRL' ? 'selected' : '' }}>SPRL</option>
                                    <option value="SA" {{ old('company_type') === 'SA' ? 'selected' : '' }}>SA</option>
                                    <option value="SRL" {{ old('company_type') === 'SRL' ? 'selected' : '' }}>SRL</option>
                                    <option value="SNC" {{ old('company_type') === 'SNC' ? 'selected' : '' }}>SNC</option>
                                    <option value="SC" {{ old('company_type') === 'SC' ? 'selected' : '' }}>SC</option>
                                    <option value="ASBL" {{ old('company_type') === 'ASBL' ? 'selected' : '' }}>ASBL</option>
                                    <option value="AUTRE" {{ old('company_type') === 'AUTRE' ? 'selected' : '' }}>Autre</option>
                                </select>
                            </div>

                            <!-- Représentant légal -->
                            <div>
                                <label for="legal_representative" class="block text-sm font-medium text-gray-700">Représentant légal</label>
                                <input type="text" name="legal_representative" id="legal_representative" value="{{ old('legal_representative') }}"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                                @error('legal_representative')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="company_email" class="block text-sm font-medium text-gray-700">Email</label>
                                <input type="email" name="email" id="company_email" value="{{ old('email') }}"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                                @error('email')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Téléphone -->
                            <div>
                                <label for="company_phone" class="block text-sm font-medium text-gray-700">Téléphone</label>
                                <input type="tel" name="phone" id="company_phone" value="{{ old('phone') }}"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                                @error('phone')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Conditions de paiement -->
                            <div>
                                <label for="payment_terms" class="block text-sm font-medium text-gray-700">Conditions de paiement (jours)</label>
                                <input type="number" name="payment_terms" id="payment_terms" value="{{ old('payment_terms', 30) }}" min="0" max="365"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                                @error('payment_terms')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Limite de crédit -->
                            <div>
                                <label for="credit_limit" class="block text-sm font-medium text-gray-700">Limite de crédit (€)</label>
                                <input type="number" name="credit_limit" id="credit_limit" value="{{ old('credit_limit', 0) }}" min="0" step="0.01"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                                @error('credit_limit')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Notes -->
                            <div class="md:col-span-2">
                                <label for="company_notes" class="block text-sm font-medium text-gray-700">Notes</label>
                                <textarea name="notes" id="company_notes" rows="3" 
                                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">{{ old('notes') }}</textarea>
                            </div>

                            <!-- Statut actif -->
                            <div class="md:col-span-2">
                                <div class="flex items-center">
                                    <input type="checkbox" name="is_active" id="company_is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                                           class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                                    <label for="company_is_active" class="ml-2 block text-sm text-gray-900">
                                        Entreprise active
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Boutons d'action -->
                    <div class="flex justify-end gap-4 mt-8 pt-6 border-t border-gray-200">
                        <a href="{{ route('clients.index') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Annuler
                        </a>
                        <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Créer le client
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const customerRadio = document.querySelector('input[value="customer"]');
            const companyRadio = document.querySelector('input[value="company"]');
            const customerForm = document.getElementById('customer-form');
            const companyForm = document.getElementById('company-form');

            function showForm() {
                if (customerRadio.checked) {
                    customerForm.classList.remove('hidden');
                    companyForm.classList.add('hidden');
                } else if (companyRadio.checked) {
                    companyForm.classList.remove('hidden');
                    customerForm.classList.add('hidden');
                } else {
                    customerForm.classList.add('hidden');
                    companyForm.classList.add('hidden');
                }
            }

            customerRadio.addEventListener('change', showForm);
            companyRadio.addEventListener('change', showForm);

            // Afficher le formulaire approprié au chargement si un type est déjà sélectionné
            showForm();
        });
    </script>
</x-app-layout> 