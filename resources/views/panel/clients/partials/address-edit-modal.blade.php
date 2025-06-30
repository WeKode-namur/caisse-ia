<x-modal name="address-edit-modal" title="Modifier l'adresse" size="lg" :showFooter="false">
    <form id="addressEditForm" class="space-y-6">
        @csrf
        <input type="hidden" id="edit_address_id">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Type d'adresse -->
            <div>
                <label for="edit_type" class="block text-sm font-medium text-gray-700">Type d'adresse *</label>
                <select name="type" id="edit_type" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Sélectionner...</option>
                    @foreach(\App\Models\CustomerAddress::ADDRESS_TYPES as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Rue -->
            <div>
                <label for="edit_street" class="block text-sm font-medium text-gray-700">Rue *</label>
                <input type="text" name="street" id="edit_street" required
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Numéro -->
            <div>
                <label for="edit_number" class="block text-sm font-medium text-gray-700">Numéro *</label>
                <input type="text" name="number" id="edit_number" required
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Code postal -->
            <div>
                <label for="edit_postal_code" class="block text-sm font-medium text-gray-700">Code postal *</label>
                <input type="text" name="postal_code" id="edit_postal_code" required
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Ville -->
            <div>
                <label for="edit_city" class="block text-sm font-medium text-gray-700">Ville *</label>
                <input type="text" name="city" id="edit_city" required
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Pays -->
            <div>
                <label for="edit_country" class="block text-sm font-medium text-gray-700">Pays *</label>
                <input type="text" name="country" id="edit_country" required
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>

        <!-- Adresse principale -->
        <div class="flex items-center">
            <input type="checkbox" name="is_primary" id="edit_is_primary" value="1"
                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
            <label for="edit_is_primary" class="ml-2 block text-sm text-gray-900">
                Définir comme adresse principale
            </label>
        </div>

        <!-- Boutons -->
        <div class="flex justify-end space-x-3 pt-4">
            <button type="button" onclick="closeAddressEditModal()" 
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">
                Annuler
            </button>
            <button type="submit" 
                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 transition-colors">
                Mettre à jour
            </button>
        </div>
    </form>
</x-modal> 