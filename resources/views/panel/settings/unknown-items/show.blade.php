<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <!-- En-tête -->
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Détails de l'article inconnu</h3>
                            <p class="mt-1 text-sm text-gray-500">Informations détaillées sur l'article non
                                identifié</p>
                        </div>
                        <a href="{{ route('settings.unknown-items.index') }}"
                           class="inline-flex items-center px-4 py-2 bg-gray-100 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Retour à la liste
                        </a>
                    </div>
                </div>

                <!-- Contenu principal -->
                <div class="p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Informations de l'article -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h4 class="text-lg font-medium text-gray-900 mb-4">Informations de l'article</h4>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Description</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $unknownItem->description ?? 'Aucune description' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Nom</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $unknownItem->nom ?? 'Aucun nom' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Prix</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ number_format($unknownItem->prix, 2) }}
                                        €</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">TVA</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ number_format($unknownItem->tva, 2) }}
                                        €</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Statut</label>
                                    <div class="mt-1">
                                        @if(!$unknownItem->est_regularise)
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                <i class="fas fa-clock mr-1"></i>
                                                En attente
                                            </span>
                                        @elseif($unknownItem->note_interne && str_starts_with($unknownItem->note_interne, 'Non identifiable'))
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <i class="fas fa-times mr-1"></i>
                                                Non identifiable
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-check mr-1"></i>
                                                Régularisé
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                @if($unknownItem->note_interne)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Notes</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ $unknownItem->note_interne }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Informations de la transaction -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h4 class="text-lg font-medium text-gray-900 mb-4">Informations de la transaction</h4>
                            @if($unknownItem->transactionItem && $unknownItem->transactionItem->transaction)
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Numéro de
                                            transaction</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ $unknownItem->transactionItem->transaction->id }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Date de création</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ $unknownItem->transactionItem->transaction->created_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Montant total</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ number_format($unknownItem->transactionItem->transaction->total_amount ?? 0, 2) }}
                                            €</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Statut</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ ucfirst($unknownItem->transactionItem->transaction->status ?? 'N/A') }}</p>
                                    </div>
                                </div>
                            @else
                                <p class="text-gray-500 text-sm">Aucune transaction associée</p>
                            @endif
                        </div>
                    </div>

                    <!-- Actions -->
                    @if(!$unknownItem->est_regularise)
                        <div class="mt-6 bg-white border border-gray-200 rounded-lg p-6">
                            <h4 class="text-lg font-medium text-gray-900 mb-4">Actions</h4>
                            <div class="flex space-x-4">
                                <button onclick="showRegularizeModal({{ $unknownItem->id }})"
                                        class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Régulariser
                                </button>
                                <button onclick="showNonIdentifiableModal({{ $unknownItem->id }})"
                                        class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    Marquer non identifiable
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de régularisation -->
    <div id="regularize-modal"
         class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div
            class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-2/3 xl:w-1/2 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <!-- En-tête du modal -->
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-lg font-medium text-gray-900">Régulariser l'article inconnu</h3>
                        </div>
                    </div>
                    <button onclick="closeRegularizeModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Contenu du modal -->
                <form id="regularize-form" class="space-y-4">
                    @csrf
                    <input type="hidden" id="regularize-item-id" value="{{ $unknownItem->id }}">
                    <input type="hidden" id="selected-variant-id" name="variant_id" required>

                    <!-- Champ de recherche -->
                    <div>
                        <label for="search-input" class="block text-sm font-medium text-gray-700 mb-1">Rechercher un
                            article *</label>
                        <div class="relative">
                            <input type="text" id="search-input"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 pl-10 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                   placeholder="Ex: Jordan xs, Chaussure Nike 42..."
                                   autocomplete="off">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Tapez le nom de l'article avec la valeur d'attribut (ex:
                            Jordan xs)</p>
                    </div>

                    <!-- Résultats de recherche -->
                    <div id="search-results" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Correspondances trouvées</label>
                        <div id="results-container"
                             class="space-y-2 max-h-64 overflow-y-auto border border-gray-200 rounded-lg p-3">
                            <!-- Les résultats seront injectés ici -->
                        </div>
                    </div>

                    <!-- Variant sélectionné -->
                    <div id="selected-variant-info" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Variant sélectionné</label>
                        <div id="selected-variant-details" class="bg-green-50 border border-green-200 rounded-lg p-3">
                            <!-- Les détails du variant sélectionné seront affichés ici -->
                        </div>
                    </div>

                    <div>
                        <label for="regularize-notes" class="block text-sm font-medium text-gray-700 mb-1">Notes
                            (optionnel)</label>
                        <textarea id="regularize-notes" name="note_interne" rows="3"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                  placeholder="Notes sur la régularisation"></textarea>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4">
                        <button type="button" onclick="closeRegularizeModal()"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                            Annuler
                        </button>
                        <button type="submit" id="submit-btn" disabled
                                class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M5 13l4 4L19 7"></path>
                            </svg>
                            Régulariser
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal non identifiable -->
    <div id="non-identifiable-modal"
         class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <!-- En-tête du modal -->
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-lg font-medium text-gray-900">Marquer comme non identifiable</h3>
                        </div>
                    </div>
                    <button onclick="closeNonIdentifiableModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Contenu du modal -->
                <form id="non-identifiable-form" class="space-y-4">
                    @csrf
                    <input type="hidden" id="non-identifiable-item-id" value="{{ $unknownItem->id }}">
                    <div>
                        <label for="non-identifiable-reason" class="block text-sm font-medium text-gray-700 mb-1">Raison
                            *</label>
                        <textarea id="non-identifiable-reason" rows="3" required
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                  placeholder="Raison pour laquelle cet article ne peut pas être identifié"></textarea>
                    </div>
                    <div class="flex justify-end space-x-3 pt-4">
                        <button type="button" onclick="closeNonIdentifiableModal()"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                            Annuler
                        </button>
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Marquer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            let searchTimeout;
            let selectedVariant = null;

            // Modal de régularisation
            function showRegularizeModal(itemId) {
                document.getElementById('regularize-modal').classList.remove('hidden');
                resetSearchForm();
            }

            function closeRegularizeModal() {
                document.getElementById('regularise-modal').classList.add('hidden');
                resetSearchForm();
            }

            function resetSearchForm() {
                document.getElementById('search-input').value = '';
                document.getElementById('search-results').classList.add('hidden');
                document.getElementById('selected-variant-info').classList.add('hidden');
                document.getElementById('regularize-notes').value = '';
                document.getElementById('submit-btn').disabled = true;
                selectedVariant = null;
            }

            // Recherche intelligente
            document.getElementById('search-input').addEventListener('input', function (e) {
                const searchTerm = e.target.value.trim();

                clearTimeout(searchTimeout);

                if (searchTerm.length < 2) {
                    document.getElementById('search-results').classList.add('hidden');
                    return;
                }

                searchTimeout = setTimeout(() => {
                    searchArticles(searchTerm);
                }, 300);
            });

            function searchArticles(searchTerm) {
                fetch('{{ route("settings.unknown-items.search") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        search: searchTerm
                    }),
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            displaySearchResults(data.variants, searchTerm);
                        }
                    })
                    .catch(error => {
                        console.error('Erreur lors de la recherche:', error);
                    });
            }

            function displaySearchResults(variants, searchTerm) {
                const container = document.getElementById('results-container');
                const resultsDiv = document.getElementById('search-results');

                if (variants.length === 0) {
                    container.innerHTML = '<p class="text-gray-500 text-sm">Aucune correspondance trouvée</p>';
                    resultsDiv.classList.remove('hidden');
                    return;
                }

                container.innerHTML = variants.map(variant => {
                    const attributes = variant.attribute_values ? variant.attribute_values.map(attr =>
                        `<span class="inline-block bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded mr-1 mb-1">
                        ${attr.attribute.name}: ${attr.value}
                    </span>`
                    ).join('') : '';

                    return `
                    <div class="variant-result border border-gray-200 rounded-lg p-3 hover:bg-gray-50 cursor-pointer transition-colors"
                         onclick="selectVariant('${variant.id}', ${JSON.stringify(variant).replace(/"/g, '&quot;')})">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-900">${variant.article.name}</h4>
                                <p class="text-sm text-gray-600">${variant.article.category ? variant.article.category.name : 'Aucune catégorie'}</p>
                                <div class="mt-2 space-y-1">
                                    <p class="text-xs text-gray-500">
                                        <span class="font-medium">Référence:</span> ${variant.reference || 'N/A'}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        <span class="font-medium">Code-barres:</span> ${variant.barcode || 'N/A'}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        <span class="font-medium">Prix:</span> ${parseFloat(variant.effective_sell_price || 0).toFixed(2)} €
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        <span class="font-medium">Stock:</span> ${variant.total_stock || 0}
                                    </p>
                                </div>
                                ${attributes ? `<div class="mt-2">${attributes}</div>` : ''}
                            </div>
                            <div class="ml-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    Score: ${variant.relevance_score}
                                </span>
                            </div>
                        </div>
                    </div>
                `;
                }).join('');

                resultsDiv.classList.remove('hidden');
            }

            function selectVariant(variantId, variantData) {
                selectedVariant = variantData;
                document.getElementById('selected-variant-id').value = variantId;

                // Masquer les résultats de recherche
                document.getElementById('search-results').classList.add('hidden');

                // Afficher les détails du variant sélectionné
                const detailsContainer = document.getElementById('selected-variant-details');
                const attributes = variantData.attribute_values ? variantData.attribute_values.map(attr =>
                    `<span class="inline-block bg-green-100 text-green-700 text-xs px-2 py-1 rounded mr-1 mb-1">
                    ${attr.attribute.name}: ${attr.value}
                </span>`
                ).join('') : '';

                detailsContainer.innerHTML = `
                <div class="space-y-2">
                    <h4 class="font-medium text-gray-900">${variantData.article.name}</h4>
                    <p class="text-sm text-gray-600">${variantData.article.category ? variantData.article.category.name : 'Aucune catégorie'}</p>
                    <div class="grid grid-cols-2 gap-2 text-sm">
                        <div><span class="font-medium">Référence:</span> ${variantData.reference || 'N/A'}</div>
                        <div><span class="font-medium">Code-barres:</span> ${variantData.barcode || 'N/A'}</div>
                        <div><span class="font-medium">Prix:</span> ${parseFloat(variantData.effective_sell_price || 0).toFixed(2)} €</div>
                        <div><span class="font-medium">Stock:</span> ${variantData.total_stock || 0}</div>
                    </div>
                    ${attributes ? `<div class="mt-2">${attributes}</div>` : ''}
                </div>
            `;

                document.getElementById('selected-variant-info').classList.remove('hidden');
                document.getElementById('submit-btn').disabled = false;
            }

            // Formulaires
            document.getElementById('regularize-form').addEventListener('submit', function (e) {
                e.preventDefault();
                const itemId = document.getElementById('regularize-item-id').value;
                const variantId = document.getElementById('selected-variant-id').value;
                const notes = document.getElementById('regularize-notes').value;

                if (!variantId) {
                    alert('Veuillez sélectionner un variant');
                    return;
                }

                fetch(`/settings/unknown-items/${itemId}/regularize`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        variant_id: variantId,
                        note_interne: notes
                    }),
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            closeRegularizeModal();
                            location.reload();
                        } else {
                            alert('Erreur: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        alert('Erreur lors de la régularisation');
                    });
            });

            // Modal non identifiable
            function showNonIdentifiableModal(itemId) {
                document.getElementById('non-identifiable-modal').classList.remove('hidden');
            }

            function closeNonIdentifiableModal() {
                document.getElementById('non-identifiable-modal').classList.add('hidden');
                document.getElementById('non-identifiable-form').reset();
            }

            document.getElementById('non-identifiable-form').addEventListener('submit', function (e) {
                e.preventDefault();
                const itemId = document.getElementById('non-identifiable-item-id').value;
                const reason = document.getElementById('non-identifiable-reason').value;

                fetch(`/settings/unknown-items/${itemId}/mark-non-identifiable`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        note_interne: reason
                    }),
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            closeNonIdentifiableModal();
                            location.reload();
                        } else {
                            alert('Erreur: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        alert('Erreur lors du marquage');
                    });
            });

            // Fermer les modals en cliquant à l'extérieur
            document.getElementById('regularize-modal').addEventListener('click', function (e) {
                if (e.target === this) {
                    closeRegularizeModal();
                }
            });

            document.getElementById('non-identifiable-modal').addEventListener('click', function (e) {
                if (e.target === this) {
                    closeNonIdentifiableModal();
                }
            });
        </script>
    @endpush
</x-app-layout>
