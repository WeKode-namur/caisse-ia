<div x-data="{ noteType: 'internal' }" x-show="showNoteModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
    <!-- Contenu modal note/commentaire -->
    <div class="bg-black bg-opacity-50 flex items-center justify-center p-4 h-full w-full backdrop-blur-sm">
        <div class="bg-white rounded xl:w-2/5 md:w-2/3 w-full max-h-[90vh] flex flex-col">
            <div class="border-b flex gap-2 items-center py-2 px-3">
                <h2 class="text-xl">Ajouter une note</h2>
                <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">Ticket</span>
                <button type="button" class="ms-auto bg-gray-500 text-white hover:opacity-75 rounded py-1 px-2 text-sm hover:shadow hover:scale-105 duration-300" title="Templates">
                    <i class="fas fa-file-text"></i>
                </button>
                <button @click="closeAllModals()" class="bg-red-500 text-white hover:opacity-75 rounded py-1 px-2 text-sm hover:shadow hover:scale-105 duration-300">
                    <i class="fas fa-x"></i>
                </button>
            </div>
            <div class="p-4 flex-1 overflow-hidden flex flex-col">
                <!-- Onglets Type de note -->
                <div class="flex bg-gray-100 rounded p-1 mb-4">
                    <button
                        @click="noteType = 'internal'"
                        :class="noteType === 'internal' ? 'bg-white shadow-sm text-gray-900' : 'text-gray-600 hover:text-gray-900'"
                        class="flex-1 py-2 text-sm font-medium rounded transition-all duration-200">
                        <i class="fas fa-eye-slash mr-1"></i>
                        Interne
                    </button>
                    <button
                        @click="noteType = 'customer'"
                        :class="noteType === 'customer' ? 'bg-white shadow-sm text-gray-900' : 'text-gray-600 hover:text-gray-900'"
                        class="flex-1 py-2 text-sm font-medium rounded transition-all duration-200">
                        <i class="fas fa-eye mr-1"></i>
                        Client
                    </button>
                </div>

                <!-- Note interne -->
                <template x-if="noteType === 'internal'">
                    <div class="flex-1 flex flex-col">
                        <!-- Templates rapides pour notes internes -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-magic mr-1"></i>
                                Templates fréquents
                            </label>
                            <div class="grid grid-cols-2 gap-2">
                                <button type="button" onclick="fillNote('Client difficile - Faire attention aux échanges')" class="text-left border rounded p-2 hover:bg-red-50 border-red-200 transition-colors">
                                    <div class="flex items-center">
                                        <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                                        <div>
                                            <div class="text-xs font-medium text-red-700">Client difficile</div>
                                        </div>
                                    </div>
                                </button>
                                <button type="button" onclick="fillNote('VIP - Service premium requis')" class="text-left border rounded p-2 hover:bg-yellow-50 border-yellow-200 transition-colors">
                                    <div class="flex items-center">
                                        <i class="fas fa-crown text-yellow-500 mr-2"></i>
                                        <div>
                                            <div class="text-xs font-medium text-yellow-700">Client VIP</div>
                                        </div>
                                    </div>
                                </button>
                                <button type="button" onclick="fillNote('Commande spéciale - Vérifier stock avant livraison')" class="text-left border rounded p-2 hover:bg-blue-50 border-blue-200 transition-colors">
                                    <div class="flex items-center">
                                        <i class="fas fa-star text-blue-500 mr-2"></i>
                                        <div>
                                            <div class="text-xs font-medium text-blue-700">Commande spéciale</div>
                                        </div>
                                    </div>
                                </button>
                                <button type="button" onclick="fillNote('Paiement différé accordé - Rappeler dans 7 jours')" class="text-left border rounded p-2 hover:bg-orange-50 border-orange-200 transition-colors">
                                    <div class="flex items-center">
                                        <i class="fas fa-clock text-orange-500 mr-2"></i>
                                        <div>
                                            <div class="text-xs font-medium text-orange-700">Paiement différé</div>
                                        </div>
                                    </div>
                                </button>
                            </div>
                        </div>

                        <!-- Zone de saisie note interne -->
                        <div class="flex-1 flex flex-col">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-comment-dots mr-1"></i>
                                Note interne (invisible au client)
                            </label>
                            <div class="border rounded text-base flex-1 flex flex-col">
                                <textarea name="internal_note" placeholder="Note visible uniquement par l'équipe..." class="border-0 px-3 py-3 flex-1 outline-0 focus:text-black resize-none" rows="8"></textarea>
                                <div class="border-t px-3 py-2 bg-gray-50 text-xs text-gray-500 flex justify-between">
                                    <span>💡 Astuce: Détaillez les informations importantes pour l'équipe</span>
                                    <span id="internal-char-count">0/500</span>
                                </div>
                            </div>
                        </div>

                        <!-- Priorité -->
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-flag mr-1"></i>
                                Priorité
                            </label>
                            <div class="flex space-x-2">
                                <button type="button" class="flex-1 border-2 border-green-200 hover:border-green-400 rounded-lg p-2 text-center transition-all">
                                    <i class="fas fa-flag text-green-500 mb-1"></i>
                                    <div class="text-xs font-medium">Normale</div>
                                </button>
                                <button type="button" class="flex-1 border-2 border-orange-200 hover:border-orange-400 rounded-lg p-2 text-center transition-all">
                                    <i class="fas fa-flag text-orange-500 mb-1"></i>
                                    <div class="text-xs font-medium">Importante</div>
                                </button>
                                <button type="button" class="flex-1 border-2 border-red-200 hover:border-red-400 rounded-lg p-2 text-center transition-all">
                                    <i class="fas fa-flag text-red-500 mb-1"></i>
                                    <div class="text-xs font-medium">Urgente</div>
                                </button>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Note client -->
                <template x-if="noteType === 'customer'">
                    <div class="flex-1 flex flex-col">
                        <!-- Templates pour notes client -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-magic mr-1"></i>
                                Messages prédéfinis
                            </label>
                            <div class="space-y-2">
                                <button type="button" onclick="fillNote('Merci pour votre achat ! N\'hésitez pas à revenir nous voir.')" class="w-full text-left border rounded p-2 hover:bg-green-50 border-green-200 transition-colors">
                                    <div class="flex items-center">
                                        <i class="fas fa-heart text-green-500 mr-2"></i>
                                        <div class="text-sm">Remerciement standard</div>
                                    </div>
                                </button>
                                <button type="button" onclick="fillNote('Garantie de 2 ans incluse. Conservez ce ticket pour tout échange.')" class="w-full text-left border rounded p-2 hover:bg-blue-50 border-blue-200 transition-colors">
                                    <div class="flex items-center">
                                        <i class="fas fa-shield-alt text-blue-500 mr-2"></i>
                                        <div class="text-sm">Information garantie</div>
                                    </div>
                                </button>
                                <button type="button" onclick="fillNote('Livraison prévue sous 3-5 jours ouvrés. SMS de confirmation envoyé.')" class="w-full text-left border rounded p-2 hover:bg-purple-50 border-purple-200 transition-colors">
                                    <div class="flex items-center">
                                        <i class="fas fa-truck text-purple-500 mr-2"></i>
                                        <div class="text-sm">Information livraison</div>
                                    </div>
                                </button>
                                <button type="button" onclick="fillNote('Échange possible sous 14 jours avec ticket et étiquettes.')" class="w-full text-left border rounded p-2 hover:bg-orange-50 border-orange-200 transition-colors">
                                    <div class="flex items-center">
                                        <i class="fas fa-exchange-alt text-orange-500 mr-2"></i>
                                        <div class="text-sm">Politique d'échange</div>
                                    </div>
                                </button>
                            </div>
                        </div>

                        <!-- Zone de saisie note client -->
                        <div class="flex-1 flex flex-col">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-comment mr-1"></i>
                                Message client (visible sur le ticket)
                            </label>
                            <div class="border rounded text-base flex-1 flex flex-col">
                                <textarea name="customer_note" placeholder="Message qui apparaîtra sur le ticket du client..." class="border-0 px-3 py-3 flex-1 outline-0 focus:text-black resize-none" rows="6"></textarea>
                                <div class="border-t px-3 py-2 bg-blue-50 text-xs text-blue-700 flex justify-between">
                                    <span>📄 Ce message apparaîtra sur le ticket imprimé</span>
                                    <span id="customer-char-count">0/200</span>
                                </div>
                            </div>
                        </div>

                        <!-- Options d'affichage -->
                        <div class="mt-4 space-y-2">
                            <label class="flex items-center">
                                <input type="checkbox" class="mr-2 rounded">
                                <span class="text-sm">Afficher en gras sur le ticket</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" class="mr-2 rounded">
                                <span class="text-sm">Envoyer par SMS si numéro disponible</span>
                            </label>
                        </div>
                    </div>
                </template>

                <!-- Aperçu -->
                <div class="mt-4 bg-gray-50 border rounded-lg p-3">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-eye text-gray-500 mr-2"></i>
                        <span class="text-sm font-medium text-gray-700">Aperçu</span>
                    </div>
                    <div class="text-sm text-gray-600 italic" id="note-preview">
                        La note apparaîtra ici...
                    </div>
                </div>

                <!-- Footer avec actions -->
                <div class="border-t pt-3 mt-3">
                    <template x-if="noteType === 'internal'">
                        <div class="space-y-2">
                            <button class="w-full bg-blue-500 text-white py-2 rounded hover:bg-blue-600 transition-colors flex items-center justify-center">
                                <i class="fas fa-save mr-2"></i>
                                Ajouter la note interne
                            </button>
                        </div>
                    </template>
                    <template x-if="noteType === 'customer'">
                        <div class="space-y-2">
                            <button class="w-full bg-green-500 text-white py-2 rounded hover:bg-green-600 transition-colors flex items-center justify-center">
                                <i class="fas fa-check mr-2"></i>
                                Ajouter au ticket client
                            </button>
                            <p class="text-xs text-gray-500 text-center">
                                Le message sera visible sur le ticket imprimé
                            </p>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function fillNote(text) {
        const activeTextarea = document.querySelector('textarea[name="internal_note"], textarea[name="customer_note"]');
        if (activeTextarea && activeTextarea.offsetParent !== null) {
            activeTextarea.value = text;
            updatePreview(text);
            updateCharCount(activeTextarea);
        }
    }

    function updatePreview(text) {
        const preview = document.getElementById('note-preview');
        if (preview) {
            preview.textContent = text || 'La note apparaîtra ici...';
        }
    }

    function updateCharCount(textarea) {
        const maxLength = textarea.name === 'internal_note' ? 500 : 200;
        const countElement = document.getElementById(textarea.name === 'internal_note' ? 'internal-char-count' : 'customer-char-count');
        if (countElement) {
            countElement.textContent = `${textarea.value.length}/${maxLength}`;
        }
    }

    // Événements pour mise à jour en temps réel
    document.addEventListener('input', function(e) {
        if (e.target.matches('textarea[name="internal_note"], textarea[name="customer_note"]')) {
            updatePreview(e.target.value);
            updateCharCount(e.target);
        }
    });
</script>

<style>
    [x-cloak] { display: none !important; }
</style>
