<x-modal name="address-modal" icon="fas fa-house" title="Ajouter une adresse" size="4xl" :showFooter="false">
    <form id="addressForm" class="space-y-6">
        @csrf
        <input type="hidden" name="client_type" id="client_type">
        <input type="hidden" name="client_id" id="client_id">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Type d'adresse -->
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700">Type d'adresse *</label>
                <select name="type" id="type" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Sélectionner...</option>
                    @foreach(\App\Models\CustomerAddress::ADDRESS_TYPES as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Rue -->
            <div>
                <label for="street" class="block text-sm font-medium text-gray-700">Rue *</label>
                <input type="text" name="street" id="street" required
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Numéro -->
            <div>
                <label for="number" class="block text-sm font-medium text-gray-700">Numéro *</label>
                <input type="text" name="number" id="number" required
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Code postal -->
            <div>
                <label for="postal_code" class="block text-sm font-medium text-gray-700">Code postal *</label>
                <input type="text" name="postal_code" id="postal_code" required
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Ville -->
            <div>
                <label for="city" class="block text-sm font-medium text-gray-700">Ville *</label>
                <input type="text" name="city" id="city" required
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Pays -->
            <div>
                <label for="country" class="block text-sm font-medium text-gray-700">Pays *</label>
                <input type="text" name="country" id="country" required value="Belgique"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>

        <!-- Adresse principale -->
        <div class="flex items-center">
            <input type="checkbox" name="is_primary" id="is_primary" value="1"
                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
            <label for="is_primary" class="ml-2 block text-sm text-gray-900">
                Définir comme adresse principale
            </label>
        </div>

        <!-- Boutons -->
        <div class="flex justify-end space-x-3 pt-4">
            <button type="button" onclick="closeAddressModal()"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">
                Annuler
            </button>
            <button type="submit"
                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 transition-colors">
                Ajouter l'adresse
            </button>
        </div>
    </form>
</x-modal>
