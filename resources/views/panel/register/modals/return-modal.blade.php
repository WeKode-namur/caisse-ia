<div x-data="{ returnAction: 'search' }" x-show="showReturnModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
    <!-- Contenu modal retour -->
    <div class="bg-black bg-opacity-50 flex items-center justify-center p-4 h-full w-full backdrop-blur-sm">
        <div class="bg-white rounded xl:w-2/5 md:w-2/3 w-full max-h-[90vh] flex flex-col">
            <div class="border-b flex gap-2 items-center py-2 px-3">
                <h2 class="text-xl">Retour / Échange</h2>
                <button type="button" class="ms-auto bg-gray-500 text-white hover:opacity-75 rounded py-1 px-2 text-sm hover:shadow hover:scale-105 duration-300" title="Historique des retours">
                    <i class="fas fa-history"></i>
                </button>
                <button @click="closeAllModals()" class="bg-red-500 text-white hover:opacity-75 rounded py-1 px-2 text-sm hover:shadow hover:scale-105 duration-300">
                    <i class="fas fa-x"></i>
                </button>
            </div>
            <div class="p-4 flex-1 overflow-hidden flex flex-col">
                <!-- Onglets Rechercher / Retour sans ticket -->
                <div class="flex bg-gray-100 rounded p-1 mb-4">
                    <button
                        @click="returnAction = 'search'"
                        :class="returnAction === 'search' ? 'bg-white shadow-sm text-gray-900' : 'text-gray-600 hover:text-gray-900'"
                        class="flex-1 py-2 text-sm font-medium rounded transition-all duration-200">
                        <i class="fas fa-search mr-1"></i>
                        Avec ticket
                    </button>
                    <button
                        @click="returnAction = 'without'"
                        :class="returnAction === 'without' ? 'bg-white shadow-sm text-gray-900' : 'text-gray-600 hover:text-gray-900'"
                        class="flex-1 py-2 text-sm font-medium rounded transition-all duration-200">
                        <i class="fas fa-receipt mr-1"></i>
                        Sans ticket
                    </button>
                </div>

                <!-- Section Retour avec ticket -->
                <template x-if="returnAction === 'search'">
                    <div class="flex-1 flex flex-col">
                        <!-- Recherche de ticket/facture -->
                        <div class="space-y-3 mb-4">
                            <div class="border rounded flex items-center text-base">
                                <i class="fas fa-receipt px-3 text-gray-400"></i>
                                <input type="text" name="ticket_number" placeholder="N° de ticket ou facture..." class="border-0 px-3 py-2 flex-1 outline-0 focus:text-black">
                                <button class="rounded-r bg-blue-500 h-full px-3 py-2 text-white hover:opacity-75 duration-300">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Résultat de recherche (simulé) -->
                        <div class="flex-1 overflow-y-auto">
                            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                                <div class="flex items-start">
                                    <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                                    <div class="flex-1">
                                        <h4 class="font-medium text-green-800">Ticket trouvé</h4>
                                        <p class="text-sm text-green-700 mt-1">
                                            <strong>N° TK240615001</strong> - 15/06/2024 à 14:30<br>
                                            Total: 89.50€ - Payé par carte
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Articles du ticket -->
                            <h3 class="text-sm font-medium text-gray-700 mb-3">Articles achetés</h3>
                            <ul role="list" class="divide-y divide-slate-200 border rounded">
                                <li class="flex py-3 px-3 hover:bg-gray-50">
                                    <div class="h-10 w-10 bg-blue-500 rounded-lg flex items-center justify-center text-white mr-3">
                                        <i class="fas fa-shirt"></i>
                                    </div>
                                    <div class="ml-3 overflow-hidden flex-1">
                                        <div class="flex justify-between items-start">
                                            <div class="flex-1">
                                                <p class="text-sm font-medium text-slate-900">T-shirt Basic</p>
                                                <p class="text-sm text-slate-500">Taille M - Bleu</p>
                                            </div>
                                            <div class="text-right ml-4">
                                                <p class="text-sm font-bold">24.99€</p>
                                                <div class="flex items-center mt-1">
                                                    <input type="checkbox" class="mr-2 rounded">
                                                    <span class="text-xs text-gray-500">Retourner</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li class="flex py-3 px-3 hover:bg-gray-50">
                                    <div class="h-10 w-10 bg-orange-500 rounded-lg flex items-center justify-center text-white mr-3">
                                        <i class="fas fa-shoe-prints"></i>
                                    </div>
                                    <div class="ml-3 overflow-hidden flex-1">
                                        <div class="flex justify-between items-start">
                                            <div class="flex-1">
                                                <p class="text-sm font-medium text-slate-900">Baskets Sport</p>
                                                <p class="text-sm text-slate-500">Pointure 42 - Noir</p>
                                            </div>
                                            <div class="text-right ml-4">
                                                <p class="text-sm font-bold">64.50€</p>
                                                <div class="flex items-center mt-1">
                                                    <input type="checkbox" class="mr-2 rounded">
                                                    <span class="text-xs text-gray-500">Retourner</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </template>

                <!-- Section Retour sans ticket -->
                <template x-if="returnAction === 'without'">
                    <div class="flex-1 flex flex-col">
                        <!-- Informations client -->
                        <div class="space-y-3 mb-4">
                            <div class="border rounded flex items-center text-base">
                                <i class="fas fa-user px-3 text-gray-400"></i>
                                <input type="text" name="customer_name" placeholder="Nom du client..." class="border-0 px-3 py-2 flex-1 outline-0 focus:text-black">
                            </div>
                            <div class="border rounded flex items-center text-base">
                                <i class="fas fa-phone px-3 text-gray-400"></i>
                                <input type="tel" name="customer_phone" placeholder="Téléphone..." class="border-0 px-3 py-2 flex-1 outline-0 focus:text-black">
                            </div>
                        </div>

                        <!-- Description de l'article -->
                        <div class="space-y-3 mb-4">
                            <div class="border rounded flex items-center text-base">
                                <i class="fas fa-tag px-3 text-gray-400"></i>
                                <input type="text" name="item_description" placeholder="Description de l'article..." class="border-0 px-3 py-2 flex-1 outline-0 focus:text-black">
                            </div>
                            <div class="border rounded flex items-center text-base">
                                <i class="fas fa-euro-sign px-3 text-gray-400"></i>
                                <input type="number" name="estimated_price" placeholder="Prix estimé..." class="border-0 px-3 py-2 flex-1 outline-0 focus:text-black" step="0.01">
                                <span class="px-3 text-gray-500">€</span>
                            </div>
                        </div>

                        <!-- Motif du retour -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-comment-alt mr-1"></i>
                                Motif du retour
                            </label>
                            <div class="grid grid-cols-2 gap-2">
                                <button type="button" class="border-2 border-gray-200 hover:border-red-400 rounded-lg p-3 text-left transition-all group">
                                    <div class="flex items-center">
                                        <div class="h-6 w-6 bg-red-500 rounded-full flex items-center justify-center text-white mr-2">
                                            <i class="fas fa-exclamation-triangle text-xs"></i>
                                        </div>
                                        <span class="text-sm font-medium">Défaut</span>
                                    </div>
                                </button>
                                <button type="button" class="border-2 border-gray-200 hover:border-orange-400 rounded-lg p-3 text-left transition-all group">
                                    <div class="flex items-center">
                                        <div class="h-6 w-6 bg-orange-500 rounded-full flex items-center justify-center text-white mr-2">
                                            <i class="fas fa-resize text-xs"></i>
                                        </div>
                                        <span class="text-sm font-medium">Taille</span>
                                    </div>
                                </button>
                                <button type="button" class="border-2 border-gray-200 hover:border-blue-400 rounded-lg p-3 text-left transition-all group">
                                    <div class="flex items-center">
                                        <div class="h-6 w-6 bg-blue-500 rounded-full flex items-center justify-center text-white mr-2">
                                            <i class="fas fa-heart-broken text-xs"></i>
                                        </div>
                                        <span class="text-sm font-medium">Regret</span>
                                    </div>
                                </button>
                                <button type="button" class="border-2 border-gray-200 hover:border-purple-400 rounded-lg p-3 text-left transition-all group">
                                    <div class="flex items-center">
                                        <div class="h-6 w-6 bg-purple-500 rounded-full flex items-center justify-center text-white mr-2">
                                            <i class="fas fa-question text-xs"></i>
                                        </div>
                                        <span class="text-sm font-medium">Autre</span>
                                    </div>
                                </button>
                            </div>
                        </div>

                        <!-- Note complémentaire -->
                        <div class="border rounded text-base mb-4">
                            <div class="flex items-center border-b">
                                <i class="fas fa-sticky-note px-3 text-gray-400"></i>
                                <span class="px-3 py-2 text-sm text-gray-500">Note complémentaire</span>
                            </div>
                            <textarea name="return_note" placeholder="Détails supplémentaires sur le retour..." class="border-0 px-3 py-2 w-full outline-0 focus:text-black resize-none" rows="3"></textarea>
                        </div>

                        <!-- Avertissement -->
                        <div class="bg-amber-50 border border-amber-200 rounded-lg p-3">
                            <div class="flex">
                                <i class="fas fa-exclamation-triangle text-amber-500 mt-0.5 mr-2"></i>
                                <div class="text-sm">
                                    <p class="font-medium text-amber-800">Attention</p>
                                    <p class="text-amber-700 mt-1">
                                        Retour sans ticket nécessite validation manager.
                                        Vérifiez l'état de l'article et la cohérence des informations.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Footer avec actions -->
                <div class="border-t pt-3 mt-3">
                    <template x-if="returnAction === 'search'">
                        <div class="space-y-2">
                            <button class="w-full bg-blue-500 text-white py-2 rounded hover:bg-blue-600 transition-colors flex items-center justify-center">
                                <i class="fas fa-exchange-alt mr-2"></i>
                                Ajouter à la caisse
                            </button>
                        </div>
                    </template>
                    <template x-if="returnAction === 'without'">
                        <button class="w-full bg-orange-500 text-white py-2 rounded hover:bg-orange-600 transition-colors flex items-center justify-center">
                            <i class="fas fa-user-shield mr-2"></i>
                            Demander validation manager
                        </button>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>
