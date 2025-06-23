<x-modal name="payment-modal" title="Finaliser le Paiement" icon="cash-register" size="4xl">
    <div class="flex flex-col md:flex-row gap-6">

        <!-- Colonne de gauche : Saisie du paiement -->
        <div id="payment-inputs-container" class="w-full md:w-2/3 space-y-3">
            <!-- Les champs de saisie par méthode de paiement seront injectés ici par le JS -->
        </div>

        <!-- Colonne de droite : Récapitulatif -->
        <div class="w-full md:w-1/3 bg-gray-50 dark:bg-gray-900/50 p-4 rounded-lg">
            <h4 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-200 border-b pb-2 dark:border-gray-700">Récapitulatif</h4>

            <!-- Totaux -->
            <div id="payments-breakdown-list" class="space-y-2 text-md mb-4">
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">À Payer :</span>
                    <span class="font-semibold dark:text-white" id="recap-total">0.00 €</span>
                </div>
                <!-- La ventilation des paiements sera injectée ici -->
            </div>

            <!-- Ligne de séparation -->
            <div class="border-t border-gray-300 dark:border-gray-700 my-2"></div>

            <!-- Reste à payer / Monnaie à rendre -->
            <div id="recap-final" class="space-y-2 text-lg">
                 <div class="flex justify-between font-bold">
                    <span id="recap-remaining-label" class="text-gray-800 dark:text-white">Reste à payer :</span>
                    <span class="text-red-600" id="recap-remaining">0.00 €</span>
                </div>
            </div>
        </div>

    </div>

    <!-- Notes (placé en dehors du flex pour prendre toute la largeur) -->
    <div class="mt-4">
        <label for="payment_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notes</label>
        <textarea id="payment_notes" name="notes" rows="2" class="w-full border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
    </div>

    <x-slot name="actions">
        <button type="button" @click="open = false" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-md hover:bg-gray-50 dark:hover:bg-gray-500">
            Annuler
        </button>
        <button type="submit" form="payment-form" class="px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md hover:bg-green-700">
            <i class="fas fa-check mr-2"></i>
            Valider le paiement
        </button>
    </x-slot>
</x-modal>