<x-app-layout>
    <div class="lg:py-12">
        <div class="max-w-7xl mx-auto px-0 lg:px-8">
            <!-- Header -->
            <div class="bg-white/50 backdrop-blur dark:bg-gray-800/50 dark:text-gray-200 overflow-hidden shadow-xl lg:rounded-lg mb-6">
                <div class="flex items-center space-x-3 px-3 py-2 border-b border-gray-300 dark:border-gray-700">
                    <a href="{{ route('inventory.index') }}" class="text-gray-600 px-1.5 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:scale-105 duration-500"><i class="fas fa-arrow-left text-xl"></i></a>
                    <h1 class="text-gray-900 dark:text-gray-50 font-bold lg:text-2xl text-xl">{{ $article->name }}</h1>
                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">Actif</span>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Colonne principale -->
                <div class="lg:col-span-2 space-y-6">

                    <!-- Informations principales -->
                    <div class="bg-white/50 backdrop-blur dark:bg-gray-800/50 dark:text-gray-200 overflow-hidden shadow-xl lg:rounded-lg">
                        <div class="px-4 py-3 border-b border-gray-300 dark:border-gray-700">
                            <h2 class="font-semibold text-lg text-gray-900 dark:text-gray-100">Informations générales</h2>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-4">
                                    <div>
                                        <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Nom</label>
                                        <p class="text-gray-900 dark:text-gray-100 font-medium">{{ $article->name }}</p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Catégorie</label>
                                        <p class="text-gray-900 dark:text-gray-100">
                                            {{ $article->category?->name ?? 'Non définie' }}
                                            @if($article->type)
                                                <span class="text-gray-500 dark:text-gray-400"> > {{ $article->type->name }}</span>
                                            @endif
                                            @if($article->subtype)
                                                <span class="text-gray-500 dark:text-gray-400"> > {{ $article->subtype->name }}</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="space-y-4">
                                    <div>
                                        <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Date de création</label>
                                        <p class="text-gray-900 dark:text-gray-100">{{ $article->created_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Dernière modification</label>
                                        <p class="text-gray-900 dark:text-gray-100">{{ $article->updated_at->diffForHumans() }}</p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-600 dark:text-gray-400">TVA</label>
                                        <p class="text-gray-900 dark:text-gray-100">{{ $article->tva ?? 21 }}%</p>
                                    </div>
                                </div>
                            </div>

                            @if($article->description)
                                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-600">
                                    <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Description</label>
                                    <p class="text-gray-900 dark:text-gray-100 mt-2">{{ $article->description }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Prix et marge -->
                    <div class="bg-white/50 backdrop-blur dark:bg-gray-800/50 dark:text-gray-200 overflow-hidden shadow-xl lg:rounded-lg">
                        <div class="px-4 py-3 border-b border-gray-300 dark:border-gray-700">
                            <h2 class="font-semibold text-lg text-gray-900 dark:text-gray-100">
                                Prix et rentabilité
                                @if($variants->isNotEmpty())
                                    <span class="text-sm font-normal text-gray-500 dark:text-gray-400">(Article principal)</span>
                                @endif
                            </h2>
                        </div>
                        <div class="p-6">
                            @if($variants->isEmpty())
                                <!-- Prix normaux pour articles sans variants -->
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                                    <div class="text-center">
                                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($article->buy_price ?? 0, 2) }}€</div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">Prix d'achat (HTVA)</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ number_format($article->sell_price ?? 0, 2) }}€</div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">Prix de vente (TVAC)</div>
                                    </div>
                                    <div class="text-center">
                                        @php
                                            $marge = ($article->sell_price ?? 0) - ($article->buy_price ?? 0);
                                        @endphp
                                        <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ number_format($marge, 2) }}€</div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">Marge unitaire</div>
                                    </div>
                                    <div class="text-center">
                                        @php
                                            $margePourcent = $article->sell_price > 0 ? (($marge / $article->sell_price) * 100) : 0;
                                        @endphp
                                        <div class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ number_format($margePourcent, 1) }}%</div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">Marge en %</div>
                                    </div>
                                </div>
                            @else
                                <!-- Prix de base pour articles avec variants -->
                                <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-4">
                                    <div class="flex items-start space-x-3">
                                        <i class="fas fa-info-circle text-amber-600 mt-1"></i>
                                        <div>
                                            <h3 class="text-sm font-medium text-amber-800 dark:text-amber-200">Prix de référence</h3>
                                            <p class="text-xs text-amber-700 dark:text-amber-300 mt-1">
                                                Ces prix servent de base pour les variants. Chaque variant peut avoir ses propres prix.
                                            </p>
                                            <div class="mt-3 grid grid-cols-2 gap-4">
                                                <div>
                                                    <span class="text-xs text-amber-600 dark:text-amber-400">Prix d'achat de base:</span>
                                                    <span class="font-medium">{{ number_format($article->buy_price ?? 0, 2) }}€</span>
                                                </div>
                                                <div>
                                                    <span class="text-xs text-amber-600 dark:text-amber-400">Prix de vente de base:</span>
                                                    <span class="font-medium">{{ number_format($article->sell_price ?? 0, 2) }}€</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Variants ou Stock global -->
                    @if($variants->isNotEmpty())
                        <!-- Section Variants -->
                        <div class="bg-white/50 backdrop-blur dark:bg-gray-800/50 dark:text-gray-200 overflow-hidden shadow-xl lg:rounded-lg">
                            <div class="px-4 py-3 border-b border-gray-300 dark:border-gray-700 flex items-center justify-between">
                                <h2 class="font-semibold text-lg text-gray-900 dark:text-gray-100 flex items-center">
                                    <i class="fas fa-layer-group mr-2 text-blue-500"></i>
                                    Variants (<span id="variants-count">{{ $variants->count() }}</span>)
                                </h2>
                                <button class="bg-green-600 dark:bg-green-400 text-white rounded py-1.5 px-2 hover:opacity-75 hover:scale-105 duration-500">
                                    <i class="fas fa-plus"></i>
                                    <span class="sr-only">Ajouter un variant</span>
                                </button>
                            </div>
                            <div class="p-6">
                                <!-- Container pour le tableau AJAX -->
                                <div id="variants-table-container">
                                    <div class="flex items-center justify-center py-8">
                                        <i class="fas fa-spinner fa-spin text-2xl text-gray-400 mr-3"></i>
                                        <span class="text-gray-500 dark:text-gray-400">Chargement des variants...</span>
                                    </div>
                                </div>

                                <!-- Résumé des stocks -->
                                <div id="variants-summary" class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-600">
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                        <div class="text-center">
                                            <div class="text-xl font-bold text-blue-600 dark:text-blue-400" id="total-stock">
                                                {{ $stats->total_stock }}
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">Stock total</div>
                                        </div>
                                        <div class="text-center">
                                            <div class="text-xl font-bold text-green-600 dark:text-green-400" id="variants-in-stock">
                                                {{ $stats->variants_in_stock }}
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">Variants en stock</div>
                                        </div>
                                        <div class="text-center">
                                            <div class="text-xl font-bold text-orange-600 dark:text-orange-400" id="variants-low-stock">
                                                {{ $stats->variants_low_stock }}
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">Stock faible</div>
                                        </div>
                                        <div class="text-center">
                                            <div class="text-xl font-bold text-red-600 dark:text-red-400" id="variants-out-stock">
                                                {{ $stats->variants_out_of_stock }}
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">Ruptures</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Stock global (sans variants) -->
                        <div class="bg-white/50 backdrop-blur dark:bg-gray-800/50 dark:text-gray-200 overflow-hidden shadow-xl lg:rounded-lg">
                            <div class="px-4 py-3 border-b border-gray-300 dark:border-gray-700">
                                <h2 class="font-semibold text-lg text-gray-900 dark:text-gray-100">Stock</h2>
                            </div>
                            <div class="p-6">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div class="text-center">
                                        <div class="text-3xl font-bold
                                            {{ $stats->total_stock == 0 ? 'text-red-600 dark:text-red-400' :
                                               ($stats->total_stock <= 10 ? 'text-orange-600 dark:text-orange-400' : 'text-green-600 dark:text-green-400') }}">
                                            {{ $stats->total_stock }}
                                        </div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">Stock actuel</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-2xl font-bold text-gray-600 dark:text-gray-400">10</div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">Seuil d'alerte</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($stats->total_value, 2) }}€</div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">Valeur stock</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Actions rapides -->
                    <div class="bg-white/50 backdrop-blur dark:bg-gray-800/50 dark:text-gray-200 overflow-hidden shadow-xl lg:rounded-lg">
                        <div class="px-4 py-3 border-b border-gray-300 dark:border-gray-700">
                            <h3 class="font-semibold text-gray-900 dark:text-gray-100">Actions rapides</h3>
                        </div>
                        <div class="p-6 space-y-3">
                            <button id="btn-edit-article" class="w-full px-3 py-2 bg-amber-500 dark:bg-amber-800 hover:opacity-75 hover:scale-105 duration-500 text-white rounded-md text-sm">
                                <i class="fas fa-pen mr-2"></i>Modifier
                            </button>
                            <button id="btn-ajuster-stock" class="w-full px-3 py-2 bg-green-500 dark:bg-green-800 hover:opacity-75 hover:scale-105 duration-500 text-white rounded-md text-sm">
                                <i class="fas fa-plus mr-2"></i>Ajuster le stock
                            </button>
                            <button id="modal_print_etiquette"
                                    class="w-full px-3 py-2 bg-blue-500 dark:bg-blue-800 hover:opacity-75 hover:scale-105 duration-500 text-white rounded-md text-sm">
                                <i class="fas fa-barcode mr-2"></i>Imprimer étiquette
                            </button>
                            <a href="{{ route('inventory.movements.history', $article) }}"
                               class="w-full px-3 py-2 bg-violet-500 dark:bg-violet-800 hover:opacity-75 hover:scale-105 duration-500 text-white rounded-md text-sm flex items-center justify-center">
                                <i class="fas fa-clock mr-2"></i>Historique
                            </a>
                        </div>
                    </div>

                    <!-- Statistiques rapides -->
                    @if($variants->isNotEmpty())
                        <div class="bg-white/50 backdrop-blur dark:bg-gray-800/50 dark:text-gray-200 overflow-hidden shadow-xl lg:rounded-lg">
                            <div class="px-4 py-3 border-b border-gray-300 dark:border-gray-700">
                                <h3 class="font-semibold text-gray-900 dark:text-gray-100">Aperçu variants</h3>
                            </div>
                            <div class="p-6 space-y-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Total variants</span>
                                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ $stats->variants_count }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Stock combiné</span>
                                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ $stats->total_stock }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Variants actifs</span>
                                    <span class="font-medium text-green-600 dark:text-green-400">{{ $stats->variants_in_stock }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">En rupture</span>
                                    <span class="font-medium text-red-600 dark:text-red-400">{{ $stats->variants_out_of_stock }}</span>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="pb-6">
                        <!-- Alertes -->
                        @if($variants->isEmpty() && $stats->total_stock <= 10)
                            <div class="bg-white/50 backdrop-blur dark:bg-gray-800/50 dark:text-gray-200 overflow-hidden shadow-xl lg:rounded-lg">
                                <div class="p-4">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-{{ $stats->total_stock == 0 ? 'red' : 'orange' }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <h3 class="text-sm font-medium text-{{ $stats->total_stock == 0 ? 'red' : 'orange' }}-800 dark:text-{{ $stats->total_stock == 0 ? 'red' : 'orange' }}-200">
                                                {{ $stats->total_stock == 0 ? 'Rupture de stock' : 'Stock faible' }}
                                            </h3>
                                            <div class="mt-2 text-sm text-{{ $stats->total_stock == 0 ? 'red' : 'orange' }}-700 dark:text-{{ $stats->total_stock == 0 ? 'red' : 'orange' }}-300">
                                                @if($stats->total_stock == 0)
                                                    Cet article n'est plus en stock. Pensez à le réapprovisionner.
                                                @else
                                                    Le stock est en dessous du seuil d'alerte (10 unités).
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @elseif($variants->isNotEmpty() && ($stats->variants_out_of_stock > 0 || $stats->variants_low_stock > 0))
                            <div class="bg-white/50 backdrop-blur dark:bg-gray-800/50 dark:text-gray-200 overflow-hidden shadow-xl lg:rounded-lg">
                                <div class="p-4">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <h3 class="text-sm font-medium text-orange-800 dark:text-orange-200">
                                                Alertes variants
                                            </h3>
                                            <div class="mt-2 text-sm text-orange-700 dark:text-orange-300 space-y-1">
                                                @if($stats->variants_out_of_stock > 0)
                                                    <div>• {{ $stats->variants_out_of_stock }} variant(s) en rupture</div>
                                                @endif
                                                @if($stats->variants_low_stock > 0)
                                                    <div>• {{ $stats->variants_low_stock }} variant(s) en stock faible</div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('panel.inventory.partials.article.variant-detail-modal')

    {{-- Modal d'impression d'étiquettes --}}
    <x-modal name="print-labels" title="Impression d'étiquettes" icon="clipboard" :closable="true" :footer="false"
             size="2xl">
        <form id="print-labels-form">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                    <thead>
                    <tr>
                        <th colspan="2" class="text-start">Nom du variant</th>
                        <th class="text-start">Attributs</th>
                        <th>Stock</th>
                        <th class="text-center w-8">Quantité</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($article->variants->whereNotNull('barcode') as $variant)
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <td class="text-center">
                                <input type="checkbox" name="variants[]" value="{{ $variant->id }}"
                                       class="variant-checkbox hover:scale-110 duration-500 hover:shadow-lg">
                            </td>
                            <td class="font-medium text-gray-900 dark:text-gray-100 variant-name cursor-pointer">{{ $variant->name ?? $article->name }}</td>
                            <td class="text-gray-700 dark:text-gray-300">
                                @php
                                    $attrs = $variant->attributeValues->map(function($attr) {
                                        return $attr->attribute->name . ' : ' . $attr->value;
                                    })->take(4)->implode('<br>');
                                @endphp
                                <span>{!! $attrs !!}</span>
                            </td>
                            <td class="text-center">{{ $variant->total_stock ?? 0 }}</td>
                            <td class="text-center">
                                <div class="flex items-center justify-end space-x-1">
                                    <button type="button"
                                            class="qty-minus px-2 py-1 bg-gray-200 dark:bg-gray-600 rounded text-lg hover:scale-105 duration-500 hover:text-red-500 disabled:hover:text-gray-500 disabled:hover:scale-100"
                                            disabled>-
                                    </button>
                                    <input type="number" name="qty[{{ $variant->id }}]" value="0" min="0"
                                           max="{{ $variant->total_stock ?? 1 }}"
                                           class="w-16 px-2 py-1 border rounded text-center" disabled>
                                    <button type="button"
                                            class="qty-plus px-2 py-1 bg-gray-200 dark:bg-gray-600 rounded text-lg hover:scale-105 duration-500 hover:text-green-500 disabled:hover:text-gray-500 disabled:hover:scale-100">
                                        +
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="flex justify-end mt-6 space-x-3">
                <button type="button"
                        class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-800 dark:text-gray-200 rounded hover:bg-gray-400 dark:hover:bg-gray-500"
                        @click="open = false">Annuler
                </button>
                <button type="button" id="btn-print-labels"
                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Imprimer
                </button>
            </div>
        </form>
    </x-modal>

    <!-- Modal d'ajout de stock avec x-modal -->
    <x-modal name="ajust-stock" title="Ajouter du stock" icon="plus" :closable="true" :footer="false" size="md">
        <form id="form-ajust-stock">
            <div class="mb-4">
                <x-label for="ajust-variant-id" value="Variant" />
                <select name="variant_id" id="ajust-variant-id" class="form-select w-full" required>
                    @foreach($article->variants as $variant)
                        <option value="{{ $variant->id }}">
                            {{ $variant->reference ? $variant->reference : 'Variant #'.$variant->id }}
                        </option>
                    @endforeach
                </select>
                <div class="text-red-500 text-xs mt-1 hidden" id="error-variant_id"></div>
            </div>
            <div class="mb-4">
                <x-label for="ajust-quantity" value="Quantité à ajouter" />
                <x-input id="ajust-quantity" name="quantity" type="number" min="0.001" step="0.001" class="w-full" required />
                <div class="text-red-500 text-xs mt-1 hidden" id="error-quantity"></div>
            </div>
            <div class="mb-4">
                <x-label for="ajust-buy-price" value="Prix d'achat (€)" />
                <x-input id="ajust-buy-price" name="buy_price" type="number" min="0" step="0.01" class="w-full" required />
                <div class="text-red-500 text-xs mt-1 hidden" id="error-buy_price"></div>
            </div>
            @if(config('custom.referent_lot_optionnel'))
            <div class="mb-4">
                <x-label for="ajust-lot-reference" value="Référence de lot" />
                <x-input id="ajust-lot-reference" name="lot_reference" type="text" maxlength="100" class="w-full" />
                <div class="text-red-500 text-xs mt-1 hidden" id="error-lot_reference"></div>
            </div>
            @endif
            @if(config('custom.date_expiration_optionnel'))
            <div class="mb-4">
                <x-label for="ajust-expiry-date" value="Date d'expiration" />
                <x-input id="ajust-expiry-date" name="expiry_date" type="date" class="w-full" />
                <div class="text-red-500 text-xs mt-1 hidden" id="error-expiry_date"></div>
            </div>
            @endif
            <div class="flex justify-end space-x-2 mt-6">
                <button type="button" onclick="window.closeModal('ajust-stock')" class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-800 dark:text-gray-200 rounded hover:bg-gray-400 dark:hover:bg-gray-500">Annuler</button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Ajouter</button>
            </div>
        </form>
    </x-modal>

    {{-- Modal d'édition d'article --}}
    <x-modal name="edit-article" title="Modifier l'article" icon="pen" :closable="true" :footer="false" size="xl">
        <form id="form-edit-article">
            <input type="hidden" name="article_id" value="{{ $article->id }}">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <x-label for="edit-article-name" value="Nom" />
                    <x-input id="edit-article-name" name="name" type="text" class="w-full" required />
                    <div class="text-red-500 text-xs mt-1 hidden" id="error-edit-name"></div>
                </div>
                <div>
                    <x-label for="edit-article-tva" value="TVA (%)" />
                    <select id="edit-article-tva" name="tva" class="form-select w-full" required>
                        <option value="0">0%</option>
                        <option value="6">6%</option>
                        <option value="12">12%</option>
                        <option value="21">21%</option>
                    </select>
                    <div class="text-red-500 text-xs mt-1 hidden" id="error-edit-tva"></div>
                </div>
                <div>
                    <x-label for="edit-article-category" value="Catégorie" />
                    <select id="edit-article-category" name="category_id" class="form-select w-full" required></select>
                    <div class="text-red-500 text-xs mt-1 hidden" id="error-edit-category_id"></div>
                </div>
                <div>
                    <x-label for="edit-article-type" value="Type" />
                    <select id="edit-article-type" name="type_id" class="form-select w-full"></select>
                    <div class="text-red-500 text-xs mt-1 hidden" id="error-edit-type_id"></div>
                </div>
                <div  style="{{ config('custom.items.sousType') ? '' : 'position: absolute; left: -200%;' }}">
                    <x-label for="edit-article-subtype" value="Sous-type" />
                    <select id="edit-article-subtype" name="subtype_id" class="form-select w-full"></select>
                    <div class="text-red-500 text-xs mt-1 hidden" id="error-edit-subtype_id"></div>
                </div>
                <div class="md:col-span-2">
                    <x-label for="edit-article-description" value="Description" />
                    <textarea id="edit-article-description" name="description" class="w-full border rounded p-2" rows="3"></textarea>
                    <div class="text-red-500 text-xs mt-1 hidden" id="error-edit-description"></div>
                </div>
                <div>
                    <x-label for="edit-article-sell-price" value="Prix de vente (€)" />
                    <x-input id="edit-article-sell-price" name="sell_price" type="number" min="0" step="0.01" class="w-full" />
                    <div class="text-red-500 text-xs mt-1 hidden" id="error-edit-sell_price"></div>
                </div>
            </div>
            <div class="flex justify-end space-x-2 mt-6">
                <button type="button" onclick="window.closeModal('edit-article')" class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-800 dark:text-gray-200 rounded hover:bg-gray-400 dark:hover:bg-gray-500">Annuler</button>
                <button type="submit" class="px-4 py-2 bg-amber-600 text-white rounded hover:bg-amber-700">Valider</button>
            </div>
        </form>
    </x-modal>

    {{-- Modal de confirmation d'édition --}}
    <x-modal name="confirm-edit-article" title="Confirmer la modification" icon="exclamation-triangle" icon-color="red" :closable="true" :footer="false" size="md">
        <div class="mb-4 text-red-700 dark:text-red-300">
            <strong>Attention :</strong> Modifier ces informations peut impacter la gestion des stocks, la facturation et l'affichage en caisse. Veuillez vérifier les changements avant de confirmer.
        </div>
        <div id="edit-article-recap" class="mb-4 text-sm"></div>
        <div class="flex justify-end space-x-2 mt-6">
            <button type="button" onclick="window.closeModal('confirm-edit-article')" class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-800 dark:text-gray-200 rounded hover:bg-gray-400 dark:hover:bg-gray-500">Annuler</button>
            <button type="button" id="btn-confirm-edit-article" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Confirmer</button>
        </div>
    </x-modal>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Charger les variants si l'article en a
                @if($variants->isNotEmpty())
                    loadVariantsTable();
                @endif

            });

            function loadVariantsTable() {
                fetch(`/inventory/{{ $article->id }}/variants`)
                    .then(response => response.text())
                    .then(html => {
                        document.getElementById('variants-table-container').innerHTML = html;
                    })
                    .catch(error => {
                        console.error('Erreur lors du chargement des variants:', error);
                        document.getElementById('variants-table-container').innerHTML =
                            '<div class="text-center py-8 text-red-500">Erreur lors du chargement des variants</div>';
                    });
            }

            document.getElementById('modal_print_etiquette').addEventListener('click', function () {
                window.openModal('print-labels');
            });

            // Activer/désactiver l'input number et les boutons +/- selon la checkbox
            document.querySelectorAll('.variant-checkbox').forEach(function (checkbox) {
                checkbox.addEventListener('change', function () {
                    const row = this.closest('tr');
                    const qtyInput = row.querySelector('input[type=number]');
                    const minusBtn = row.querySelector('.qty-minus');
                    if (this.checked) {
                        qtyInput.value = 1;
                        qtyInput.disabled = false;
                        minusBtn.disabled = false;
                    } else {
                        qtyInput.value = 0;
                        qtyInput.disabled = true;
                        minusBtn.disabled = true;
                    }
                });
            });

            // Gestion des boutons +/-
            document.querySelectorAll('.qty-minus').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    const row = this.closest('tr');
                    const qtyInput = row.querySelector('input[type=number]');
                    const checkbox = row.querySelector('.variant-checkbox');
                    let val = parseInt(qtyInput.value) || 0;
                    if (val > 0) {
                        val--;
                        qtyInput.value = val;
                        if (val === 0) {
                            checkbox.checked = false;
                            qtyInput.disabled = true;
                            this.disabled = true;
                        }
                    }
                });
            });
            document.querySelectorAll('.qty-plus').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    const row = this.closest('tr');
                    const qtyInput = row.querySelector('input[type=number]');
                    const max = parseInt(qtyInput.max) || 99;
                    let val = parseInt(qtyInput.value) || 0;
                    const checkbox = row.querySelector('.variant-checkbox');
                    if (!checkbox.checked) {
                        checkbox.checked = true;
                        checkbox.dispatchEvent(new Event('change'));
                        qtyInput.value = 1;
                        qtyInput.disabled = false;
                        row.querySelector('.qty-minus').disabled = false;
                        row.querySelector('.qty-plus').disabled = false;
                        return;
                    }
                    if (val < max) {
                        val++;
                        qtyInput.value = val;
                        if (val > 0) {
                            checkbox.checked = true;
                            qtyInput.disabled = false;
                            row.querySelector('.qty-minus').disabled = false;
                            row.querySelector('.qty-plus').disabled = false;
                        }
                    }
                });
            });

            // Si on modifie manuellement l'input et met 0, décocher la case
            document.querySelectorAll('input[type=number][name^="qty["]').forEach(function (input) {
                input.addEventListener('input', function () {
                    const row = this.closest('tr');
                    const checkbox = row.querySelector('.variant-checkbox');
                    const minusBtn = row.querySelector('.qty-minus');
                    let val = parseInt(this.value) || 0;
                    if (val === 0) {
                        checkbox.checked = false;
                        this.disabled = true;
                        minusBtn.disabled = true;
                    } else {
                        checkbox.checked = true;
                        this.disabled = false;
                        minusBtn.disabled = false;
                    }
                });
            });

            // Cliquer sur le nom coche/décoche la case
            document.querySelectorAll('.variant-name').forEach(function (cell) {
                cell.addEventListener('click', function () {
                    const row = this.closest('tr');
                    const checkbox = row.querySelector('.variant-checkbox');
                    checkbox.checked = !checkbox.checked;
                    checkbox.dispatchEvent(new Event('change'));
                });
            });

            // Gestion du bouton Imprimer (popup d'impression)
            document.getElementById('btn-print-labels').addEventListener('click', function () {
                const form = document.getElementById('print-labels-form');
                const formData = new FormData(form);
                const params = new URLSearchParams();

                // On ne garde que les variants cochés et leur quantité
                form.querySelectorAll('.variant-checkbox:checked').forEach(cb => {
                    params.append('variants[]', cb.value);
                    const qty = form.querySelector(`input[name="qty[${cb.value}]"]`).value;
                    params.append(`qty[${cb.value}]`, qty);
                });

                if (params.toString() === '') {
                    alert('Sélectionnez au moins un variant.');
                    return;
                }

                // Ouvre la popup en POST
                const url = "{{ route('inventory.labels.print-preview', $article) }}";
                const popup = window.open('', '_blank', 'width=900,height=600');
                const html = `
                    <form id=\"popupForm\" action=\"${url}\" method=\"POST\">
                        <input type=\"hidden\" name=\"_token\" value=\"{{ csrf_token() }}\">
                        ${Array.from(params.entries()).map(([k, v]) => `<input type=\"hidden\" name=\"${k}\" value=\"${v}\">`).join('')}
                    </form>
                    <script>document.getElementById('popupForm').submit();<\/script>
                `;
                popup.document.write(html);
            });

            function clearAjustStockErrors() {
                ['variant_id','quantity','buy_price','lot_reference','expiry_date'].forEach(function(field) {
                    const el = document.getElementById('error-' + field);
                    if (el) {
                        el.classList.add('hidden');
                        el.textContent = '';
                    }
                });
            }
            document.getElementById('btn-ajuster-stock').addEventListener('click', function() {
                window.openModal('ajust-stock');
                setTimeout(() => {
                    const select = document.getElementById('ajust-variant-id');
                    if (select) {
                        setAjustBuyPrice(select.value);
                    }
                }, 100);
                clearAjustStockErrors();
            });
            document.getElementById('ajust-variant-id').addEventListener('change', function() {
                setAjustBuyPrice(this.value);
            });
            function setAjustBuyPrice(variantId) {
                let price = 0;
                @foreach($article->variants as $variant)
                    if (variantId == '{{ $variant->id }}') {
                        price = {{ $variant->stocks->last()?->buy_price ?? 0 }};
                    }
                @endforeach
                document.getElementById('ajust-buy-price').value = price;
            }
            document.getElementById('form-ajust-stock').addEventListener('submit', function(e) {
                e.preventDefault();
                clearAjustStockErrors();
                let hasError = false;
                // Vérification côté client
                const quantity = parseFloat(document.getElementById('ajust-quantity').value);
                const buyPrice = parseFloat(document.getElementById('ajust-buy-price').value);
                if (isNaN(quantity) || quantity <= 0) {
                    document.getElementById('error-quantity').textContent = 'Merci de mettre une valeur supérieure à 0';
                    document.getElementById('error-quantity').classList.remove('hidden');
                    hasError = true;
                }
                if (isNaN(buyPrice) || buyPrice < 0) {
                    document.getElementById('error-buy_price').textContent = 'Merci de mettre un prix d\'achat valide';
                    document.getElementById('error-buy_price').classList.remove('hidden');
                    hasError = true;
                }
                // Ajoute d'autres vérifications si besoin
                if (hasError) return;
                const form = e.target;
                const data = new FormData(form);
                fetch(`{{ route('inventory.stock.adjust', $article) }}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: data
                })
                .then(async r => {
                    let res;
                    try { res = await r.json(); } catch { res = {success: false, message: 'Erreur inconnue'}; }
                    if (res.success) {
                        window.closeModal('ajust-stock');
                        window.location.reload();
                    } else {
                        // Affichage des erreurs backend si possible
                        if (res.errors) {
                            Object.entries(res.errors).forEach(function([field, messages]) {
                                const el = document.getElementById('error-' + field);
                                if (el) {
                                    el.textContent = messages[0];
                                    el.classList.remove('hidden');
                                }
                            });
                        } else {
                            alert(res.message || 'Erreur lors de l\'ajout du stock');
                        }
                    }
                })
                .catch(() => alert('Erreur lors de l\'ajout du stock'));
            });

            let editArticleData = null;
            let editArticleOld = null;
            let editArticleNew = null;
            // Ouvre le modal d'édition et charge les données
            document.getElementById('btn-edit-article').addEventListener('click', function() {
                fetch(`/inventory/{{ $article->id }}/edit`)
                    .then(r => r.json())
                    .then(data => {
                        editArticleData = data;
                        // Remplir les champs
                        document.getElementById('edit-article-name').value = data.article.name;
                        document.getElementById('edit-article-tva').value = data.article.tva;
                        document.getElementById('edit-article-description').value = data.article.description || '';
                        document.getElementById('edit-article-sell-price').value = data.article.sell_price ?? '';
                        // Catégories
                        let catSelect = document.getElementById('edit-article-category');
                        catSelect.innerHTML = '';
                        data.categories.forEach(cat => {
                            let opt = document.createElement('option');
                            opt.value = cat.id;
                            opt.textContent = cat.name;
                            if (cat.id === data.article.category_id) opt.selected = true;
                            catSelect.appendChild(opt);
                        });
                        // Types
                        let typeSelect = document.getElementById('edit-article-type');
                        typeSelect.innerHTML = '<option value="">-- Aucun --</option>';
                        data.types.forEach(type => {
                            let opt = document.createElement('option');
                            opt.value = type.id;
                            opt.textContent = type.name;
                            if (type.id === data.article.type_id) opt.selected = true;
                            typeSelect.appendChild(opt);
                        });
                        // Sous-types
                        let subSelect = document.getElementById('edit-article-subtype');
                        subSelect.innerHTML = '<option value="">-- Aucun --</option>';
                        data.subtypes.forEach(sub => {
                            let opt = document.createElement('option');
                            opt.value = sub.id;
                            opt.textContent = sub.name;
                            if (sub.id === data.article.subtype_id) opt.selected = true;
                            subSelect.appendChild(opt);
                        });
                        // Sélectionner la TVA
                        Array.from(document.getElementById('edit-article-tva').options).forEach(opt => {
                            opt.selected = (parseInt(opt.value) === parseInt(data.article.tva));
                        });
                        window.openModal('edit-article');
                    });
            });
            // Changement de catégorie => charger types
            document.getElementById('edit-article-category').addEventListener('change', function() {
                let catId = this.value;
                fetch(`/api/catalog/categories/${catId}/types`)
                    .then(r => r.json())
                    .then(types => {
                        let typeSelect = document.getElementById('edit-article-type');
                        typeSelect.innerHTML = '<option value="">-- Aucun --</option>';
                        types.forEach(type => {
                            let opt = document.createElement('option');
                            opt.value = type.id;
                            opt.textContent = type.name;
                            typeSelect.appendChild(opt);
                        });
                        document.getElementById('edit-article-subtype').innerHTML = '<option value="">-- Aucun --</option>';
                    });
            });
            // Changement de type => charger sous-types
            document.getElementById('edit-article-type').addEventListener('change', function() {
                let typeId = this.value;
                if (!typeId) {
                    document.getElementById('edit-article-subtype').innerHTML = '<option value="">-- Aucun --</option>';
                    return;
                }
                fetch(`/api/catalog/types/${typeId}/subtypes`)
                    .then(r => r.json())
                    .then(subtypes => {
                        let subSelect = document.getElementById('edit-article-subtype');
                        subSelect.innerHTML = '<option value="">-- Aucun --</option>';
                        subtypes.forEach(sub => {
                            let opt = document.createElement('option');
                            opt.value = sub.id;
                            opt.textContent = sub.name;
                            subSelect.appendChild(opt);
                        });
                    });
            });
            // Soumission du formulaire d'édition => ouvrir le modal de confirmation
            document.getElementById('form-edit-article').addEventListener('submit', function(e) {
                e.preventDefault();
                // Nettoyer erreurs
                ['name','tva','category_id','type_id','subtype_id','description','sell_price'].forEach(f => {
                    let el = document.getElementById('error-edit-'+f);
                    if (el) { el.classList.add('hidden'); el.textContent = ''; }
                });
                let form = e.target;
                let formData = new FormData(form);
                let recap = '';
                let fields = ['name','tva','category_id','type_id','subtype_id','description','sell_price'];
                let labels = {
                    name: 'Nom', tva: 'TVA', category_id: 'Catégorie', type_id: 'Type', subtype_id: 'Sous-catégorie', description: 'Description', sell_price: 'Prix de vente'
                };
                let old = editArticleData.article;
                let newData = {};
                fields.forEach(f => {
                    newData[f] = formData.get(f);
                });
                editArticleOld = old;
                editArticleNew = newData;
                // Récapitulatif des changements
                recap += '<ul class="list-disc pl-5">';
                fields.forEach(f => {
                    let oldVal = old[f] ?? '';
                    let newVal = newData[f] ?? '';
                    if (f === 'category_id') {
                        let cat = editArticleData.categories.find(c => c.id == oldVal);
                        let catNew = editArticleData.categories.find(c => c.id == newVal);
                        oldVal = cat ? cat.name : '';
                        newVal = catNew ? catNew.name : '';
                    }
                    if (f === 'type_id') {
                        let type = editArticleData.types.find(t => t.id == oldVal);
                        let typeNew = editArticleData.types.find(t => t.id == newVal);
                        oldVal = type ? type.name : '';
                        newVal = typeNew ? typeNew.name : '';
                    }
                    if (f === 'subtype_id') {
                        let sub = editArticleData.subtypes.find(s => s.id == oldVal);
                        let subNew = editArticleData.subtypes.find(s => s.id == newVal);
                        oldVal = sub ? sub.name : '';
                        newVal = subNew ? subNew.name : '';
                    }
                    if ((oldVal+'') !== (newVal+'')) {
                        recap += `<li><b>${labels[f]}</b> : <span class='text-gray-600 line-through'>${oldVal}</span> <i class='fas fa-arrow-right mx-1'></i> <span class='text-green-700'>${newVal}</span></li>`;
                    }
                });
                recap += '</ul>';
                if (recap === '<ul class="list-disc pl-5"></ul>') {
                    recap = '<span class="text-gray-500">Aucun changement détecté.</span>';
                }
                document.getElementById('edit-article-recap').innerHTML = recap;
                window.openModal('confirm-edit-article');
            });
            // Confirmation finale => envoi AJAX
            document.getElementById('btn-confirm-edit-article').addEventListener('click', function() {
                let data = {...editArticleNew};
                fetch(`/inventory/{{ $article->id }}/edit`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                })
                .then(async r => {
                    let res;
                    try { res = await r.json(); } catch { res = {success: false, message: 'Erreur inconnue'}; }
                    if (res.success) {
                        window.closeModal('confirm-edit-article');
                        window.closeModal('edit-article');
                        window.location.reload();
                    } else {
                        if (res.errors) {
                            Object.entries(res.errors).forEach(function([field, messages]) {
                                const el = document.getElementById('error-edit-' + field);
                                if (el) {
                                    el.textContent = messages[0];
                                    el.classList.remove('hidden');
                                }
                            });
                        } else {
                            alert(res.message || 'Erreur lors de la modification');
                        }
                    }
                })
                .catch(() => alert('Erreur lors de la modification'));
            });
        </script>
    @endpush
</x-app-layout>
