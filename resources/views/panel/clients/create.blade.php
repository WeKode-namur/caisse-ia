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

                @if ($errors->any())
                    <div class="mb-4">
                        <ul class="text-red-600 text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('clients.store') }}" class="p-6">
                    @csrf

                    <div x-data="{ type: '{{ old('client_type', 'customer') }}' }">
                        <div class="grid grid-cols-2 gap-6 mb-8">
                            <label
                                :class="type === 'customer' ? 'border-blue-500 ring-2 ring-blue-200' : 'border-gray-300'"
                                class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none hover:border-blue-500 transition-all duration-200"
                            >
                                <input type="radio" name="client_type" value="customer" class="sr-only"
                                    x-model="type">
                                <span class="flex flex-1">
                                    <span class="flex flex-col">
                                        <span class="block text-sm font-medium text-gray-900">Client Particulier</span>
                                        <span class="mt-1 flex items-center text-sm text-gray-500">
                                            <i class="far fa-user text-lg mr-3 text-blue-600"></i>
                                            Usage privé
                                        </span>
                                    </span>
                                </span>
                                <template x-if="type === 'customer'">
                                    <i class="fas fa-circle-check h-5 w-5 text-blue-600"></i>
                                </template>
                                <template x-if="type !== 'customer'">
                                    <i class="far fa-circle h-5 w-5 text-blue-600"></i>
                                </template>
                            </label>

                            <label
                                :class="type === 'company' ? 'border-green-500 ring-2 ring-green-200' : 'border-gray-300'"
                                class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none hover:border-green-500 transition-all duration-200"
                            >
                                <input type="radio" name="client_type" value="company" class="sr-only"
                                    x-model="type">
                                <span class="flex flex-1">
                                    <span class="flex flex-col">
                                        <span class="block text-sm font-medium text-gray-900">Entreprise</span>
                                        <span class="mt-1 flex items-center text-sm text-gray-500">
                                            <i class="far fa-building text-lg mr-3 text-green-600"></i>
                                            Indépendant ou société
                                        </span>
                                    </span>
                                </span>
                                <template x-if="type === 'company'">
                                    <i class="fas fa-circle-check h-5 w-5 text-green-600"></i>
                                </template>
                                <template x-if="type !== 'company'">
                                    <i class="far fa-circle h-5 w-5 text-green-600"></i>
                                </template>
                            </label>
                        </div>
                        @error('client_type')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror

                        <!-- Champs pour Client Particulier -->
                        <template x-if="type === 'customer'">
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
                                    <input type="text" name="first_name" id="first_name" :required="type === 'customer'" value="{{ old('first_name') }}"
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Prénom">
                                    @error('first_name')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Nom -->
                                <div>
                                    <label for="last_name" class="block text-sm font-medium text-gray-700">Nom *</label>
                                    <input type="text" name="last_name" id="last_name" :required="type === 'customer'" value="{{ old('last_name') }}"
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Nom">
                                    @error('last_name')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Email -->
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700">Email *</label>
                                    <input type="email" name="email" id="email" :required="type === 'customer'" value="{{ old('email') }}"
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Email">
                                    @error('email')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Téléphone -->
                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700">Téléphone</label>
                                    <input type="tel" name="phone" id="phone" value="{{ old('phone') }}"
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
                                    <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                                    <textarea name="notes" id="notes" rows="3"
                                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('notes') }}</textarea>
                                </div>

                                <!-- Statut actif -->
                                <div class="md:col-span-2">
                                    <div class="flex items-center">
                                        <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                        <label for="is_active" class="ml-2 block text-sm text-gray-900">
                                            Client actif
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <!-- Champs pour Entreprise -->
                        <template x-if="type === 'company'">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Nom de l'entreprise -->
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700">Nom de l'entreprise *</label>
                                    <input type="text" name="name" id="name" :required="type === 'company'" value="{{ old('name') }}"
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500" placeholder="Nom de l'entreprise">
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
                                        @foreach(\App\Models\Company::COMPANY_TYPES as $key => $label)
                                            <option value="{{ $key }}" {{ old('company_type') === $key ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
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
                                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                    <input type="email" name="email" id="email" :required="type === 'company'" value="{{ old('email') }}"
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500" placeholder="Email entreprise">
                                    @error('email')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Téléphone -->
                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700">Téléphone</label>
                                    <input type="tel" name="phone" id="phone" value="{{ old('phone') }}"
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
                                    <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                                    <textarea name="notes" id="notes" rows="3"
                                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">{{ old('notes') }}</textarea>
                                </div>

                                <!-- Statut actif -->
                                <div class="md:col-span-2">
                                    <div class="flex items-center">
                                        <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                                               class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                                        <label for="is_active" class="ml-2 block text-sm text-gray-900">
                                            Entreprise active
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </template>
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
</x-app-layout>
