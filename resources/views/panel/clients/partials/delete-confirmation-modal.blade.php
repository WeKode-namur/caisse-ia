<x-modal name="delete-confirmation-modal" title="Confirmer la suppression" size="sm" :showFooter="false">
    <div class="text-center">
        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
            <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
        </div>
        
        <h3 class="text-lg font-medium text-gray-900 mb-2">Supprimer cette adresse ?</h3>
        <p class="text-sm text-gray-500 mb-6">
            Cette action est irréversible. L'adresse sera définitivement supprimée.
        </p>
        
        <div class="flex justify-center space-x-3">
            <button type="button" onclick="closeDeleteConfirmationModal()" 
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">
                Annuler
            </button>
            <button type="button" onclick="confirmDeleteAddress()" 
                    class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700 transition-colors">
                Supprimer
            </button>
        </div>
    </div>
</x-modal> 