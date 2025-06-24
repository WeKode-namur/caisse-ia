<x-modal name="discount-modal" title="Appliquer une remise" size="2xl" icon="percent" iconColor="blue" :footer="false">
    <div class="flex-1 overflow-hidden flex flex-col" x-data="{ discountType: 'percentage' }">
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

                <!-- Footer avec actions -->
                <div class="border-t pt-3 mt-3 space-y-2">
                    <button class="w-full bg-blue-500 text-white py-2 rounded hover:bg-blue-600 transition-colors flex items-center justify-center">
                        <i class="fas fa-calculator mr-2"></i>
                        <span x-show="discountType === 'percentage'">Appliquer le pourcentage</span>
                        <span x-show="discountType === 'fixed'">Appliquer le montant</span>
                    </button>
                </div>
            </div>
</x-modal>
