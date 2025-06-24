<x-app-layout>
    <div class="lg:py-12">
        <div class="max-w-7xl mx-auto px-0 lg:px-8">
            <!-- Header -->
            <div class="bg-white/50 backdrop-blur dark:bg-gray-800/50 dark:text-gray-200 overflow-hidden shadow-xl lg:rounded-lg mb-6">
                <div class="flex items-center justify-between px-3 py-2 border-b border-gray-300 dark:border-gray-700">
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('inventory.index') }}" class="text-gray-600 px-1.5 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:scale-105 duration-500">
                            <i class="fas fa-arrow-left text-xl"></i>
                        </a>
                        <h1 class="text-gray-900 dark:text-gray-50 font-bold lg:text-2xl text-xl">{{ $article->name }}</h1>
                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                            Actif
                        </span>
                    </div>
                    <div class="flex items-center space-x-3">
{{--                        <a href="{{ route('inventory.create.step.one', $article->id) }}" class="bg-blue-500 dark:bg-blue-800 hover:opacity-75 hover:scale-105 duration-500 text-white px-3 py-1 rounded text-sm">--}}
{{--                            <i class="fas fa-edit"></i>--}}
{{--                        </a>--}}
{{--                        <button class="bg-red-500 dark:bg-red-800 hover:opacity-75 hover:scale-105 duration-500 text-white px-3 py-1 rounded text-sm">--}}
{{--                            <i class="fas fa-trash"></i>--}}
{{--                        </button>--}}
                    </div>
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
{{--                                <button class="text-blue-600 dark:text-blue-400 hover:text-blue-800 hover:scale-105 duration-500 dark:hover:text-blue-200 text-sm">--}}
{{--                                    <i class="fas fa-plus mr-1"></i>Ajouter un variant--}}
{{--                                </button>--}}
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

                    <!-- Historique des mouvements -->
                    <div class="bg-white/50 backdrop-blur dark:bg-gray-800/50 dark:text-gray-200 overflow-hidden shadow-xl lg:rounded-lg">
                        <div class="px-4 py-3 border-b border-gray-300 dark:border-gray-700 flex items-center justify-between">
                            <h2 class="font-semibold text-lg text-gray-900 dark:text-gray-100">Historique des mouvements</h2>
{{--                            <button class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200 hover:scale-105 duration-500">--}}
{{--                                <i class="far fa-file-lines mr-1"></i>--}}
{{--                                Voir tout--}}
{{--                            </button>--}}
                        </div>
                        <div class="p-6">
                            <!-- Container pour l'historique AJAX -->
                            <div id="movements-container">
                                <div class="flex items-center justify-center py-8">
                                    <i class="fas fa-spinner fa-spin text-2xl text-gray-400 mr-3"></i>
                                    <span class="text-gray-500 dark:text-gray-400">Chargement de l'historique...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Photo -->
{{--                    <div class="bg-white/50 backdrop-blur dark:bg-gray-800/50 dark:text-gray-200 overflow-hidden shadow-xl lg:rounded-lg">--}}
{{--                        <div class="px-4 py-3 border-b border-gray-300 dark:border-gray-700">--}}
{{--                            <h3 class="font-semibold text-gray-900 dark:text-gray-100">Photo</h3>--}}
{{--                        </div>--}}
{{--                        <div class="p-6">--}}
{{--                            <div class="w-full h-48 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">--}}
{{--                                <div class="text-center">--}}
{{--                                    <svg class="w-12 h-12 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">--}}
{{--                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>--}}
{{--                                    </svg>--}}
{{--                                    <p class="text-sm text-gray-500 dark:text-gray-400">Aucune photo</p>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                            <button class="w-full mt-3 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm hover:scale-105 duration-500 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600">--}}
{{--                                Ajouter une photo--}}
{{--                            </button>--}}
{{--                        </div>--}}
{{--                    </div>--}}

                    <!-- Actions rapides -->
                    <div class="bg-white/50 backdrop-blur dark:bg-gray-800/50 dark:text-gray-200 overflow-hidden shadow-xl lg:rounded-lg">
                        <div class="px-4 py-3 border-b border-gray-300 dark:border-gray-700">
                            <h3 class="font-semibold text-gray-900 dark:text-gray-100">Actions rapides</h3>
                        </div>
                        <div class="p-6 space-y-3">
{{--                            @if($variants->isNotEmpty())--}}
{{--                                <button class="w-full px-3 py-2 bg-purple-500 dark:bg-purple-800 hover:opacity-75 hover:scale-105 duration-500 text-white rounded-md text-sm">--}}
{{--                                    <i class="fas fa-layer-group mr-2"></i>Gérer les variants--}}
{{--                                </button>--}}
{{--                            @endif--}}
{{--                            <button class="w-full px-3 py-2 bg-green-500 dark:bg-green-800 hover:opacity-75 hover:scale-105 duration-500 text-white rounded-md text-sm">--}}
{{--                                <i class="fas fa-plus mr-2"></i>Ajuster le stock--}}
{{--                            </button>--}}
                            <button id="modal_print_etiquette"
                                    class="w-full px-3 py-2 bg-blue-500 dark:bg-blue-800 hover:opacity-75 hover:scale-105 duration-500 text-white rounded-md text-sm">
                                <i class="fas fa-barcode mr-2"></i>Imprimer étiquette
                            </button>
{{--                            <button class="w-full px-3 py-2 bg-orange-500 dark:bg-orange-800 hover:opacity-75 hover:scale-105 duration-500 text-white rounded-md text-sm">--}}
{{--                                <i class="fas fa-chart-bar mr-2"></i>Voir les statistiques--}}
{{--                            </button>--}}
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

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Charger les variants si l'article en a
                @if($variants->isNotEmpty())
                    loadVariantsTable();
                @endif

                // Charger l'historique des mouvements
                loadMovementsHistory();
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

            function loadMovementsHistory() {
                fetch(`/inventory/{{ $article->id }}/movements`)
                    .then(response => response.text())
                    .then(html => {
                        document.getElementById('movements-container').innerHTML = html;
                    })
                    .catch(error => {
                        console.error('Erreur lors du chargement de l\'historique:', error);
                        document.getElementById('movements-container').innerHTML =
                            '<div class="text-center py-8 text-red-500">Erreur lors du chargement de l\'historique</div>';
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
        </script>
    @endpush
</x-app-layout>
