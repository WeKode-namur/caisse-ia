<div x-data="{ giftCardAction: 'use' }" x-show="showGiftCardModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
    <!-- Contenu modal cartes cadeaux -->
    <div class="bg-black bg-opacity-50 flex items-center justify-center p-4 h-full w-full backdrop-blur-sm">
        <div class="bg-white rounded xl:w-2/5 md:w-2/3 w-full max-h-[90vh] flex flex-col">
            <div class="border-b flex gap-2 items-center py-2 px-3">
                <h2 class="text-xl">Cartes cadeaux</h2>
                <button type="button" class="ms-auto bg-gray-500 text-white hover:opacity-75 rounded py-1 px-2 text-sm hover:shadow hover:scale-105 duration-300" title="Paramètres">
                    <i class="fas fa-gears"></i>
                </button>
                <button @click="closeAllModals()" class="bg-red-500 text-white hover:opacity-75 rounded py-1 px-2 text-sm hover:shadow hover:scale-105 duration-300">
                    <i class="fas fa-x"></i>
                </button>
            </div>
            <div class="p-4 flex-1 overflow-hidden flex flex-col">
                <!-- Onglets Utiliser / Créer -->
                <div class="flex bg-gray-100 rounded p-1 mb-4">
                    <button
                        @click="giftCardAction = 'use'"
                        :class="giftCardAction === 'use' ? 'bg-white shadow-sm text-gray-900' : 'text-gray-600 hover:text-gray-900'"
                        class="flex-1 py-2 text-sm font-medium rounded transition-all duration-200">
                        <i class="fas fa-credit-card mr-1"></i>
                        Utiliser
                    </button>
                    <button
                        @click="giftCardAction = 'create'"
                        :class="giftCardAction === 'create' ? 'bg-white shadow-sm text-gray-900' : 'text-gray-600 hover:text-gray-900'"
                        class="flex-1 py-2 text-sm font-medium rounded transition-all duration-200">
                        <i class="fas fa-plus mr-1"></i>
                        Créer
                    </button>
                </div>

                <!-- Section Utiliser une carte cadeau -->
                <template x-if="giftCardAction === 'use'">
                    <div class="flex-1 flex flex-col">
                        <!-- Scanner/Saisir code -->
                        <div class="border rounded flex items-center text-base mb-6">
                            <i class="fas fa-barcode px-3 text-gray-400"></i>
                            <input type="text" name="gift_card_code" placeholder="Scanner ou saisir le code..." class="border-0 px-3 py-2 flex-1 outline-0 focus:text-black uppercase" maxlength="16">
                            <button class="rounded-r bg-blue-500 h-full px-3 py-2 text-white hover:opacity-75 duration-300">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>

                        <!-- Zone d'information/résultat -->
                        <div class="flex-1 flex items-center justify-center">
                            <div class="text-center text-gray-500">
                                <div class="mb-4">
                                    <i class="fas fa-credit-card text-6xl text-gray-300"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-700 mb-2">Scanner une carte cadeau</h3>
                                <p class="text-sm text-gray-500 mb-4">Utilisez le scanner ou saisissez le code manuellement</p>

                                <!-- Instructions -->
                                <div class="bg-blue-50 rounded-lg p-4 text-left max-w-md">
                                    <h4 class="font-medium text-blue-800 mb-2">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Instructions
                                    </h4>
                                    <ul class="text-sm text-blue-700 space-y-1">
                                        <li>• Scannez le code-barres de la carte</li>
                                        <li>• Ou saisissez le code à 16 caractères</li>
                                        <li>• Le solde s'affichera automatiquement</li>
                                        <li>• Validez pour appliquer à la vente</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Section Créer une carte cadeau -->
                <template x-if="giftCardAction === 'create'">
                    <div class="flex-1 flex flex-col">
                        <!-- Montants prédéfinis -->
                        <div class="mb-4">
                            <h3 class="text-sm font-medium text-gray-700 mb-3">Montants suggérés</h3>
                            <div class="grid grid-cols-3 gap-2">
                                <button class="bg-gradient-to-r from-blue-500 to-blue-600 text-white py-4 rounded-lg font-medium transition-all hover:shadow-lg hover:scale-105">
                                    <div class="text-lg font-bold">25€</div>
                                    <div class="text-xs opacity-75">Découverte</div>
                                </button>
                                <button class="bg-gradient-to-r from-green-500 to-green-600 text-white py-4 rounded-lg font-medium transition-all hover:shadow-lg hover:scale-105">
                                    <div class="text-lg font-bold">50€</div>
                                    <div class="text-xs opacity-75">Populaire</div>
                                </button>
                                <button class="bg-gradient-to-r from-purple-500 to-purple-600 text-white py-4 rounded-lg font-medium transition-all hover:shadow-lg hover:scale-105">
                                    <div class="text-lg font-bold">100€</div>
                                    <div class="text-xs opacity-75">Premium</div>
                                </button>
                            </div>
                        </div>

                        <!-- Montant personnalisé -->
                        <div class="border rounded flex items-center text-base mb-4">
                            <i class="fas fa-euro-sign px-3 text-gray-400"></i>
                            <input type="number" name="custom_amount" placeholder="Montant personnalisé..." class="border-0 px-3 py-2 flex-1 outline-0 focus:text-black" min="5" max="1000" step="0.01">
                            <span class="px-3 text-gray-500">€</span>
                        </div>

                        <!-- Informations destinataire -->
                        <div class="space-y-3 mb-4">
                            <div class="border rounded flex items-center text-base">
                                <i class="fas fa-user px-3 text-gray-400"></i>
                                <input type="text" name="recipient_name" placeholder="Nom du destinataire (optionnel)..." class="border-0 px-3 py-2 flex-1 outline-0 focus:text-black">
                            </div>
                            <div class="border rounded flex items-center text-base">
                                <i class="fas fa-envelope px-3 text-gray-400"></i>
                                <input type="email" name="recipient_email" placeholder="Email du destinataire (optionnel)..." class="border-0 px-3 py-2 flex-1 outline-0 focus:text-black">
                            </div>
                        </div>

                        <!-- Message personnalisé -->
                        <div class="border rounded text-base mb-4">
                            <div class="flex items-center border-b">
                                <i class="fas fa-comment px-3 text-gray-400"></i>
                                <span class="px-3 py-2 text-sm text-gray-500">Message personnalisé</span>
                            </div>
                            <textarea name="custom_message" placeholder="Joyeux anniversaire ! Profitez de cette carte cadeau pour vous faire plaisir..." class="border-0 px-3 py-2 w-full outline-0 focus:text-black resize-none" rows="3"></textarea>
                        </div>

                        <!-- Types de cartes -->
                        <div class="flex-1">
                            <h3 class="text-sm font-medium text-gray-700 mb-3">Type de carte</h3>
                            <div class="grid grid-cols-2 gap-3">
                                <button class="border-2 border-blue-200 hover:border-blue-400 rounded-lg p-3 text-left transition-all group hover:shadow-md">
                                    <div class="flex items-center">
                                        <div class="h-8 w-8 bg-blue-500 rounded-full flex items-center justify-center text-white mr-3">
                                            <i class="fas fa-print text-sm"></i>
                                        </div>
                                        <div>
                                            <p class="font-medium">Physique</p>
                                            <p class="text-xs text-gray-500">À imprimer</p>
                                        </div>
                                    </div>
                                </button>
                                <button class="border-2 border-green-200 hover:border-green-400 rounded-lg p-3 text-left transition-all group hover:shadow-md">
                                    <div class="flex items-center">
                                        <div class="h-8 w-8 bg-green-500 rounded-full flex items-center justify-center text-white mr-3">
                                            <i class="fas fa-mobile text-sm"></i>
                                        </div>
                                        <div>
                                            <p class="font-medium">Numérique</p>
                                            <p class="text-xs text-gray-500">Par email/SMS</p>
                                        </div>
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Footer avec actions -->
                <div class="border-t pt-3 mt-3">
                    <template x-if="giftCardAction === 'use'">
                        <div class="space-y-2">
                            <button class="w-full bg-blue-500 text-white py-2 rounded hover:bg-blue-600 transition-colors flex items-center justify-center">
                                <i class="fas fa-search mr-2"></i>
                                Vérifier la carte
                            </button>
                            <p class="text-xs text-gray-500 text-center">
                                La carte sera automatiquement appliquée après vérification
                            </p>
                        </div>
                    </template>
                    <template x-if="giftCardAction === 'create'">
                        <div class="space-y-2">
                            <button class="w-full bg-green-500 text-white py-2 rounded hover:bg-green-600 transition-colors flex items-center justify-center">
                                <i class="fas fa-gift mr-2"></i>
                                Créer la carte cadeau
                            </button>
                            <button class="w-full bg-gray-500 text-white py-2 rounded hover:bg-gray-600 transition-colors flex items-center justify-center">
                                <i class="fas fa-eye mr-2"></i>
                                Aperçu de la carte
                            </button>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
