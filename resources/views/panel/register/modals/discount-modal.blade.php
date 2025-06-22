<div x-data="{ discountType: 'percentage' }" x-show="showDiscountModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
    <!-- Contenu modal promo/remise -->
    <div class="bg-black bg-opacity-50 flex items-center justify-center p-4 h-full w-full backdrop-blur-sm">
        <div class="bg-white rounded xl:w-2/5 md:w-2/3 w-full max-h-[90vh] flex flex-col">
            <div class="border-b flex gap-2 items-center py-2 px-3">
                <h2 class="text-xl">Appliquer une remise</h2>
                <button type="button" class="ms-auto bg-gray-500 text-white hover:opacity-75 rounded py-1 px-2 text-sm hover:shadow hover:scale-105 duration-300" title="Nouvelle remise">
                    <i class="fas fa-gears"></i>
                </button>
                <button @click="closeAllModals()" class="bg-red-500 text-white hover:opacity-75 rounded py-1 px-2 text-sm hover:shadow hover:scale-105 duration-300">
                    <i class="fas fa-x"></i>
                </button>
            </div>
            <div class="p-4 flex-1 overflow-hidden flex flex-col">
                <!-- Onglets Pourcentage / Montant fixe -->
                <div class="flex bg-gray-100 rounded p-1 mb-4">
                    <button
                        @click="discountType = 'percentage'"
                        :class="discountType === 'percentage' ? 'bg-white shadow-sm text-gray-900' : 'text-gray-600 hover:text-gray-900'"
                        class="flex-1 py-2 text-sm font-medium rounded transition-all duration-200">
                        <i class="fas fa-percent mr-1"></i>
                        Pourcentage
                    </button>
                    <button
                        @click="discountType = 'fixed'"
                        :class="discountType === 'fixed' ? 'bg-white shadow-sm text-gray-900' : 'text-gray-600 hover:text-gray-900'"
                        class="flex-1 py-2 text-sm font-medium rounded transition-all duration-200">
                        <i class="fas fa-euro-sign mr-1"></i>
                        Montant fixe
                    </button>
                </div>

                <!-- Saisie rapide - Change selon le type -->
                <div class="grid grid-cols-4 gap-2 mb-4">
                    <template x-if="discountType === 'percentage'">
                        <div class="contents">
                            <button class="bg-blue-100 hover:bg-blue-200 text-blue-800 py-3 rounded font-medium transition-colors">5%</button>
                            <button class="bg-blue-100 hover:bg-blue-200 text-blue-800 py-3 rounded font-medium transition-colors">10%</button>
                            <button class="bg-blue-100 hover:bg-blue-200 text-blue-800 py-3 rounded font-medium transition-colors">15%</button>
                            <button class="bg-blue-100 hover:bg-blue-200 text-blue-800 py-3 rounded font-medium transition-colors">20%</button>
                        </div>
                    </template>
                    <template x-if="discountType === 'fixed'">
                        <div class="contents">
                            <button class="bg-green-100 hover:bg-green-200 text-green-800 py-3 rounded font-medium transition-colors">2€</button>
                            <button class="bg-green-100 hover:bg-green-200 text-green-800 py-3 rounded font-medium transition-colors">5€</button>
                            <button class="bg-green-100 hover:bg-green-200 text-green-800 py-3 rounded font-medium transition-colors">10€</button>
                            <button class="bg-green-100 hover:bg-green-200 text-green-800 py-3 rounded font-medium transition-colors">20€</button>
                        </div>
                    </template>
                </div>

                <!-- Saisie manuelle - Change selon le type -->
                <div class="border rounded flex items-center text-base mb-4">
                    <template x-if="discountType === 'percentage'">
                        <div class="contents">
                            <i class="fas fa-percent px-3 text-gray-400"></i>
                            <input type="number" name="discount_value" placeholder="Entrez le pourcentage..." class="border-0 px-3 py-2 flex-1 outline-0 focus:text-black" min="0" max="100">
                            <span class="px-3 text-gray-500">%</span>
                        </div>
                    </template>
                    <template x-if="discountType === 'fixed'">
                        <div class="contents">
                            <i class="fas fa-euro-sign px-3 text-gray-400"></i>
                            <input type="number" name="discount_value" placeholder="Entrez le montant..." class="border-0 px-3 py-2 flex-1 outline-0 focus:text-black" min="0" step="0.01">
                            <span class="px-3 text-gray-500">€</span>
                        </div>
                    </template>
                </div>

                <!-- Remises prédéfinies - Filtrées selon le type -->
                <div class="flex-1 overflow-y-auto">
                    <h3 class="text-sm font-medium text-gray-700 mb-3">
                        <span x-show="discountType === 'percentage'">Remises en pourcentage</span>
                        <span x-show="discountType === 'fixed'">Remises fixes</span>
                    </h3>
                    <ul role="list" class="divide-y divide-slate-200">
                        <!-- Remises en pourcentage -->
                        <template x-if="discountType === 'percentage'">
                            <div>
                                <li class="flex py-3 hover:bg-gray-50 cursor-pointer rounded px-2 transition-all group hover:shadow-sm">
                                    <div class="h-12 w-12 bg-blue-500 rounded-full flex items-center justify-center text-white">
                                        <i class="fas fa-heart"></i>
                                    </div>
                                    <div class="ml-3 overflow-hidden flex-1">
                                        <div class="flex justify-between items-start">
                                            <div class="flex-1">
                                                <p class="text-sm font-medium text-slate-900">Remise fidélité</p>
                                                <p class="text-sm text-slate-500">Remise sur article abîmé</p>
                                            </div>
                                            <div class="text-right ml-4">
                                                <p class="text-lg font-bold text-green-600">10%</p>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li class="flex py-3 hover:bg-gray-50 cursor-pointer rounded px-2 transition-all group hover:shadow-sm">
                                    <div class="h-12 w-12 bg-orange-500 rounded-full flex items-center justify-center text-white">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <div class="ml-3 overflow-hidden flex-1">
                                        <div class="flex justify-between items-start">
                                            <div class="flex-1">
                                                <p class="text-sm font-medium text-slate-900">Happy Hour</p>
                                                <p class="text-sm text-slate-500">Promo de 17h à 19h</p>
                                            </div>
                                            <div class="text-right ml-4">
                                                <p class="text-lg font-bold text-green-600">15%</p>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li class="flex py-3 hover:bg-gray-50 cursor-pointer rounded px-2 transition-all group hover:shadow-sm">
                                    <div class="h-12 w-12 bg-green-500 rounded-full flex items-center justify-center text-white">
                                        <i class="fas fa-graduation-cap"></i>
                                    </div>
                                    <div class="ml-3 overflow-hidden flex-1">
                                        <div class="flex justify-between items-start">
                                            <div class="flex-1">
                                                <p class="text-sm font-medium text-slate-900">Étudiant</p>
                                                <p class="text-sm text-slate-500">Tarif préférentiel étudiant</p>
                                            </div>
                                            <div class="text-right ml-4">
                                                <p class="text-lg font-bold text-green-600">20%</p>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </div>
                        </template>

                        <!-- Remises fixes -->
                        <template x-if="discountType === 'fixed'">
                            <div>
                                <li class="flex py-3 hover:bg-gray-50 cursor-pointer rounded px-2 transition-all group hover:shadow-sm">
                                    <div class="h-12 w-12 bg-red-500 rounded-full flex items-center justify-center text-white">
                                        <i class="fas fa-tags"></i>
                                    </div>
                                    <div class="ml-3 overflow-hidden flex-1">
                                        <div class="flex justify-between items-start">
                                            <div class="flex-1">
                                                <p class="text-sm font-medium text-slate-900">Soldes</p>
                                                <p class="text-sm text-slate-500">Remise fixe période soldes</p>
                                            </div>
                                            <div class="text-right ml-4">
                                                <p class="text-lg font-bold text-green-600">5€</p>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li class="flex py-3 hover:bg-gray-50 cursor-pointer rounded px-2 transition-all group hover:shadow-sm">
                                    <div class="h-12 w-12 bg-purple-500 rounded-full flex items-center justify-center text-white">
                                        <i class="fas fa-gift"></i>
                                    </div>
                                    <div class="ml-3 overflow-hidden flex-1">
                                        <div class="flex justify-between items-start">
                                            <div class="flex-1">
                                                <p class="text-sm font-medium text-slate-900">Bon d'achat</p>
                                                <p class="text-sm text-slate-500">Remise bon d'achat 10€</p>
                                            </div>
                                            <div class="text-right ml-4">
                                                <p class="text-lg font-bold text-green-600">10€</p>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li class="flex py-3 hover:bg-gray-50 cursor-pointer rounded px-2 transition-all group hover:shadow-sm">
                                    <div class="h-12 w-12 bg-indigo-500 rounded-full flex items-center justify-center text-white">
                                        <i class="fas fa-handshake"></i>
                                    </div>
                                    <div class="ml-3 overflow-hidden flex-1">
                                        <div class="flex justify-between items-start">
                                            <div class="flex-1">
                                                <p class="text-sm font-medium text-slate-900">Geste commercial</p>
                                                <p class="text-sm text-slate-500">Compensation service client</p>
                                            </div>
                                            <div class="text-right ml-4">
                                                <p class="text-lg font-bold text-green-600">15€</p>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </div>
                        </template>
                    </ul>
                </div>

                <!-- Footer avec actions -->
                <div class="border-t pt-3 mt-3 space-y-2">
                    <button class="w-full bg-blue-500 text-white py-2 rounded hover:bg-blue-600 transition-colors flex items-center justify-center">
                        <i class="fas fa-calculator mr-2"></i>
                        <span x-show="discountType === 'percentage'">Appliquer le pourcentage</span>
                        <span x-show="discountType === 'fixed'">Appliquer le montant</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
