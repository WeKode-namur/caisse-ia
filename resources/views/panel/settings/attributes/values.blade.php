<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-900 overflow-hidden shadow-xl sm:rounded-lg">
                <!-- En-tête -->
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between ">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-300">Valeurs de l'attribut
                                "{{ $attribute->name }}"</h2>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Gérez les valeurs possibles pour
                                cet attribut</p>
                        </div>
                        <div class="flex space-x-3">
                            <button onclick="openModal('add-value'); resetAddModal();"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Ajouter une valeur
                            </button>
                            <a href="{{ route('settings.attributes.index') }}"
                               class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Retour
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Messages de succès/erreur -->
                @if(session('success'))
                    <div class="px-6 py-3 bg-green-100 border-l-4 border-green-500">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                          d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                          clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-green-700">{{ session('success') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="px-6 py-3 bg-red-100 border-l-4 border-red-500">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                          d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                          clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-red-700">{{ session('error') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Informations sur l'attribut -->
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Type:</span>
                            <span class="ml-2 inline-flex px-2 py-1 text-xs font-semibold rounded-full
                            @switch($attribute->type)
                                @case('number')
                                    bg-green-100 text-green-800
                                    @break
                                @case('select')
                                    bg-purple-100 text-purple-800
                                    @break
                                @case('color')
                                    bg-pink-100 text-pink-800
                                    @break
                                @default
                                    bg-gray-100 text-gray-800
                            @endswitch">
                            @switch($attribute->type)
                                    @case('number')
                                        Nombre
                                        @break
                                    @case('select')
                                        Sélection
                                        @break
                                    @case('color')
                                        Couleur
                                        @break
                                    @default
                                        {{ ucfirst($attribute->type) }}
                                @endswitch
                        </span>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Valeurs:</span>
                            <span class="ml-2 text-sm text-gray-900 dark:text-white">{{ $values->count() }}</span>
                        </div>
                        @if($attribute->unit)
                            <div>
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Unité:</span>
                                <span class="ml-2 text-sm text-gray-900 dark:text-white">{{ $attribute->unit }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Tableau des valeurs -->
                <div id="values-table">
                    {{-- Le tableau sera chargé ici en AJAX --}}
                </div>

                <!-- Section Archives (valeurs inactives) -->
                <div id="archives-section-container" class="px-6 py-4 border-t border-gray-200" style="display: none;">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900 flex items-center">
                            <i class="fa-solid fa-archive text-gray-500 mr-2"></i>
                            <span>Archives</span>
                            <span class="text-gray-500 ms-2 text-sm">
                                (<span id="archives-count" class="mr-1">0</span>valeur<span
                                    id="archives-count-plural">s</span>)
                            </span>
                        </h3>
                        <button onclick="toggleArchives()"
                                class="text-sm text-gray-600 hover:text-gray-900 flex items-center">
                            <i class="fa-solid fa-chevron-down mr-1" id="archives-icon"></i>
                            <span id="archives-toggle-text">Afficher</span>
                        </button>
                    </div>

                    <div id="archives-section" class="hidden">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div id="archives-table">
                                {{-- Le tableau des archives sera chargé ici en AJAX --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal pour ajouter une valeur -->
    <x-modal name="add-value" title="Ajouter une valeur" size="lg" icon="fas fa-plus-circle" iconColor="green">
        <form action="{{ route('settings.attributes.values.store', $attribute) }}" method="POST" data-ajax>
            @csrf
            <div class="space-y-4">
                <div class="relative">
                    <label for="value" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Valeur <span
                            class="text-red-500">*</span></label>
                    @if($attribute->type === 'number')
                        <input type="number"
                               name="value"
                               id="value"
                               required
                               class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                               placeholder="Entrez une valeur numérique...">
                    @else
                    <input type="text"
                           name="value"
                           id="value"
                           required
                           class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                           placeholder="Entrez la valeur...">
                    @endif
                    @if($attribute->type === 'color')
                        <div id="add-suggestions"
                             class="absolute z-50 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md shadow-lg max-h-48 overflow-y-auto hidden w-full"></div>
                    @endif
                </div>
                @if($attribute->type === 'color')
                    <div id="add-second-value-wrapper">
                        <label for="second_value" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Couleur</label>
                        <div class="mt-1 flex items-center space-x-3">
                            <div class="relative">
                                <!-- Input color invisible -->
                                <input type="color"
                                       name="second_value"
                                       id="second_value"
                                       class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                       value="#000000">
                                <!-- Div ronde personnalisée -->
                                <div id="add_color_preview"
                                     class="w-12 h-12 rounded-full border-3 border-gray-300 dark:border-gray-600 cursor-pointer shadow-md hover:shadow-lg transition-all duration-200 bg-white dark:bg-gray-800 flex items-center justify-center">
                                    <div class="w-8 h-8 rounded-full border-2 border-gray-200 dark:border-gray-700"
                                         style="background-color: #000000;"></div>
                                </div>
                            </div>
                            <div class="flex-1">
                                <input type="text"
                                       id="add_color_hex"
                                       class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm font-mono"
                                       placeholder="#000000"
                                       readonly>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <x-slot name="actions">
                <button type="button"
                        onclick="closeModal('add-value')"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-md hover:bg-gray-50 dark:hover:bg-gray-500 transition-colors">
                    Annuler
                </button>
                <button type="button"
                        onclick="submitAddForm()"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 transition-colors">
                    Ajouter
                </button>
            </x-slot>
        </form>
    </x-modal>

    <!-- Modal pour modifier une valeur -->
    <x-modal name="edit-value" title="Modifier la valeur" size="2xl" icon="fas fa-pen" iconColor="amber">
        <form id="editValueForm" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Formulaire -->
                <div class="lg:col-span-2 space-y-4">
                    <div class="relative">
                        <label for="edit_value" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Valeur
                            <span class="text-red-500">*</span></label>
                        @if($attribute->type === 'number')
                            <input type="number"
                                   name="value"
                                   id="edit_value"
                                   required
                                   class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                   placeholder="Entrez une valeur numérique...">
                        @else
                        <input type="text"
                               name="value"
                               id="edit_value"
                               required
                               class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                               placeholder="Entrez la valeur...">
                        @endif
                        @if($attribute->type === 'color')
                            <div id="edit-suggestions"
                                 class="absolute z-50 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md shadow-lg max-h-48 overflow-y-auto hidden w-full"></div>
                        @endif
                    </div>
                    @if($attribute->type === 'color')
                        <div id="edit-second-value-wrapper">
                            <label for="edit_second_value"
                                   class="block text-sm font-medium text-gray-700 dark:text-gray-300">Couleur</label>
                            <div class="mt-1 flex items-center space-x-3">
                                <div class="relative">
                                    <!-- Input color invisible -->
                                    <input type="color"
                                           name="second_value"
                                           id="edit_second_value"
                                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                           value="#000000">
                                    <!-- Div ronde personnalisée -->
                                    <div id="edit_color_preview"
                                         class="w-12 h-12 rounded-full border-3 border-gray-300 dark:border-gray-600 cursor-pointer shadow-md hover:shadow-lg transition-all duration-200 bg-white dark:bg-gray-800 flex items-center justify-center">
                                        <div class="w-8 h-8 rounded-full border-2 border-gray-200 dark:border-gray-700"
                                             style="background-color: #000000;"></div>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <input type="text"
                                           id="edit_color_hex"
                                           class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm font-mono"
                                           placeholder="#000000"
                                           readonly>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Info-bulle d'avertissement pour tous les types -->
                    <div
                        class="mt-3 p-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-amber-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                          d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                          clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h4 class="text-sm font-medium text-amber-800 dark:text-amber-200">Attention :
                                    Modification de valeur</h4>
                                <div class="mt-1 text-sm text-amber-700 dark:text-amber-300">
                                    <p class="mb-2">La modification de cette valeur aura des répercussions sur :</p>
                                    <ul class="list-disc list-inside space-y-1 text-xs">
                                        <li>Les articles existants utilisant cette valeur</li>
                                        <li>Les tickets et factures déjà générés</li>
                                        <li>L'historique des transactions</li>
                                    </ul>
                                    <p class="mt-2 font-medium">Il est vivement déconseillé de modifier cette valeur
                                        sauf en cas d'erreur orthographique.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Box d'informations -->
                <div class="lg:col-span-1">
                    <div
                        class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 border border-gray-200 dark:border-gray-600 h-full">
                        <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-3 flex items-center">
                            <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                            Informations
                        </h4>

                        <div class="space-y-3">
                            <div>
                                <span
                                    class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Position</span>
                                <p class="text-sm text-gray-900 dark:text-gray-100" id="value-position">-</p>
                            </div>

                            <div>
                                <span
                                    class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Variants liés</span>
                                <p class="text-sm text-gray-900 dark:text-gray-100" id="variants-count">-</p>
                            </div>

                            <div>
                                <span
                                    class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Articles liés</span>
                                <p class="text-sm text-gray-900 dark:text-gray-100" id="articles-count">-</p>
                            </div>

                            <div>
                                <span
                                    class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Créé le</span>
                                <p class="text-sm text-gray-900 dark:text-gray-100" id="created-at">-</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <x-slot name="actions">
                <button type="button"
                        onclick="closeModal('edit-value')"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-md hover:bg-gray-50 dark:hover:bg-gray-500 transition-colors">
                    Annuler
                </button>
                <button type="button"
                        onclick="submitEditForm()"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 transition-colors">
                    Mettre à jour
                </button>
            </x-slot>
        </form>
    </x-modal>

    <script>
        // Liste complète des couleurs CSS avec traductions FR → EN
        const cssColorTranslations = {
            // Couleurs de base
            'rouge': 'red',
            'bleu': 'blue',
            'vert': 'green',
            'jaune': 'yellow',
            'noir': 'black',
            'blanc': 'white',
            'gris': 'gray',
            'orange': 'orange',
            'violet': 'purple',
            'rose': 'pink',
            'marron': 'brown',
            'cyan': 'cyan',
            'magenta': 'magenta',

            // Couleurs étendues
            'rouge foncé': 'darkred',
            'bleu foncé': 'darkblue',
            'vert foncé': 'darkgreen',
            'gris foncé': 'darkgray',
            'rouge clair': 'lightcoral',
            'bleu clair': 'lightblue',
            'vert clair': 'lightgreen',
            'gris clair': 'lightgray',

            // Couleurs spéciales
            'or': 'gold',
            'argent': 'silver',
            'indigo': 'indigo',
            'turquoise': 'turquoise',
            'coral': 'coral',
            'saumon': 'salmon',
            'lavande': 'lavender',
            'menthe': 'mintcream',
            'pêche': 'peachpuff',
            'ivoire': 'ivory',
            'beige': 'beige',
            'chocolat': 'chocolate',
            'cramoisi': 'crimson',
            'fuchsia': 'fuchsia',
            'citron vert': 'lime',
            'olive': 'olive',
            'marine': 'navy',
            'sarcelle': 'teal',
            'aqua': 'aqua',
            'azur': 'azure',
            'bisque': 'bisque',
            'amande blanchit': 'blanchedalmond',
            'violet bleu': 'blueviolet',
            'bois de bruyère': 'burlywood',
            'bleu cadet': 'cadetblue',
            'chartreuse': 'chartreuse',
            'bleu bleuet': 'cornflowerblue',
            'soie de maïs': 'cornsilk',
            'cyan foncé': 'darkcyan',
            'jaune doré foncé': 'darkgoldenrod',
            'kaki foncé': 'darkkhaki',
            'magenta foncé': 'darkmagenta',
            'vert olive foncé': 'darkolivegreen',
            'orange foncé': 'darkorange',
            'orchidée foncée': 'darkorchid',
            'saumon foncé': 'darksalmon',
            'vert mer foncé': 'darkseagreen',
            'bleu ardoise foncé': 'darkslateblue',
            'gris ardoise foncé': 'darkslategray',
            'turquoise foncé': 'darkturquoise',
            'violet foncé': 'darkviolet',
            'rose profond': 'deeppink',
            'bleu ciel profond': 'deepskyblue',
            'bleu dodger': 'dodgerblue',
            'brique': 'firebrick',
            'blanc floral': 'floralwhite',
            'vert forêt': 'forestgreen',
            'gainsboro': 'gainsboro',
            'blanc fantôme': 'ghostwhite',
            'verge d\'or': 'goldenrod',
            'jaune vert': 'greenyellow',
            'rosée de miel': 'honeydew',
            'rose vif': 'hotpink',
            'rouge indien': 'indianred',
            'khaki': 'khaki',
            'lavande rosée': 'lavenderblush',
            'vert gazon': 'lawngreen',
            'chiffon citron': 'lemonchiffon',
            'cyan clair': 'lightcyan',
            'jaune doré clair': 'lightgoldenrodyellow',
            'rose clair': 'lightpink',
            'saumon clair': 'lightsalmon',
            'vert mer clair': 'lightseagreen',
            'bleu ciel clair': 'lightskyblue',
            'gris ardoise clair': 'lightslategray',
            'bleu acier clair': 'lightsteelblue',
            'jaune clair': 'lightyellow',
            'vert citron': 'limegreen',
            'lin': 'linen',
            'aigue-marine moyen': 'mediumaquamarine',
            'orchidée moyenne': 'mediumorchid',
            'violet moyen': 'mediumpurple',
            'vert mer moyen': 'mediumseagreen',
            'bleu ardoise moyen': 'mediumslateblue',
            'vert printemps moyen': 'mediumspringgreen',
            'turquoise moyen': 'mediumturquoise',
            'rouge violet moyen': 'mediumvioletred',
            'bleu minuit': 'midnightblue',
            'rose brumeux': 'mistyrose',
            'mocassin': 'moccasin',
            'blanc navajo': 'navajowhite',
            'dentelle ancienne': 'oldlace',
            'vert olive terne': 'olivedrab',
            'rouge orange': 'orangered',
            'orchidée': 'orchid',
            'verge d\'or pâle': 'palegoldenrod',
            'vert pâle': 'palegreen',
            'turquoise pâle': 'paleturquoise',
            'rouge violet pâle': 'palevioletred',
            'blanc papaye': 'papayawhip',
            'pêche': 'peachpuff',
            'pérou': 'peru',
            'prune': 'plum',
            'bleu poudre': 'powderblue',
            'marron rosé': 'rosybrown',
            'bleu royal': 'royalblue',
            'marron selle': 'saddlebrown',
            'marron sable': 'sandybrown',
            'vert mer': 'seagreen',
            'coquillage': 'seashell',
            'terre de sienne': 'sienna',
            'bleu ciel': 'skyblue',
            'bleu ardoise': 'slateblue',
            'gris ardoise': 'slategray',
            'neige': 'snow',
            'vert printemps': 'springgreen',
            'bleu acier': 'steelblue',
            'bronzé': 'tan',
            'chardon': 'thistle',
            'tomate': 'tomato',
            'blé': 'wheat',
            'fumée blanche': 'whitesmoke',
            'vert jaune': 'yellowgreen'
        };

        // Fonction pour capitaliser la première lettre
        function capitalizeFirstLetter(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        }

        // Fonction pour convertir une couleur CSS en HEX
        function cssColorToHex(cssColor) {
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            ctx.fillStyle = cssColor;
            ctx.fillRect(0, 0, 1, 1);
            const data = ctx.getImageData(0, 0, 1, 1).data;
            return '#' + ((1 << 24) + (data[0] << 16) + (data[1] << 8) + data[2]).toString(16).slice(1);
        }

        // Fonction pour créer l'autocomplétion
        function createColorAutocomplete(inputId, colorInputId, suggestionsId) {
            const valueInput = document.getElementById(inputId);
            const colorInput = document.getElementById(colorInputId);
            const suggestionsList = document.getElementById(suggestionsId);

            function showSuggestions(text) {
                if (!text || text.length < 2) {
                    suggestionsList.classList.add('hidden');
                    return;
                }

                const matches = Object.keys(cssColorTranslations).filter(color =>
                    color.toLowerCase().includes(text.toLowerCase())
                ).slice(0, 8); // Limiter à 8 suggestions

                if (matches.length === 0) {
                    suggestionsList.classList.add('hidden');
                    return;
                }

                suggestionsList.innerHTML = matches.map(color =>
                    `<div class="suggestion-item px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer text-sm" data-color="${color}">
                        <span class="inline-block w-4 h-4 rounded-full mr-2 align-middle" style="background: ${cssColorTranslations[color]}"></span>
                        ${capitalizeFirstLetter(color)} → ${cssColorTranslations[color]}
                    </div>`
                ).join('');

                suggestionsList.classList.remove('hidden');
            }

            function hideSuggestions() {
                suggestionsList.classList.add('hidden');
            }

            function selectSuggestion(color) {
                valueInput.value = capitalizeFirstLetter(color);
                const hexValue = cssColorToHex(cssColorTranslations[color]);
                colorInput.value = hexValue;

                // Mettre à jour la prévisualisation si elle existe
                const previewId = inputId === 'value' ? 'add_color_preview' : 'edit_color_preview';
                const previewElement = document.getElementById(previewId);
                if (previewElement) {
                    updateColorPreview(previewElement, hexValue);
                }

                // Mettre à jour le champ hex si il existe
                const hexInputId = inputId === 'value' ? 'add_color_hex' : 'edit_color_hex';
                const hexInput = document.getElementById(hexInputId);
                if (hexInput) {
                    hexInput.value = hexValue;
                }

                hideSuggestions();
            }

            // Événements
            valueInput.addEventListener('input', function () {
                showSuggestions(this.value);
            });

            valueInput.addEventListener('focus', function () {
                showSuggestions(this.value);
            });

            valueInput.addEventListener('blur', function () {
                setTimeout(hideSuggestions, 200);
            });

            suggestionsList.addEventListener('click', function (e) {
                if (e.target.classList.contains('suggestion-item')) {
                    selectSuggestion(e.target.dataset.color);
                }
            });

            // Gestion des touches clavier
            valueInput.addEventListener('keydown', function (e) {
                const visibleSuggestions = suggestionsList.querySelectorAll('.suggestion-item:not(.hidden)');
                const currentIndex = Array.from(visibleSuggestions).findIndex(item => item.classList.contains('selected'));

                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    if (currentIndex < visibleSuggestions.length - 1) {
                        visibleSuggestions.forEach(item => item.classList.remove('selected'));
                        visibleSuggestions[currentIndex + 1].classList.add('selected', 'bg-gray-100', 'dark:bg-gray-700');
                    }
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    if (currentIndex > 0) {
                        visibleSuggestions.forEach(item => item.classList.remove('selected'));
                        visibleSuggestions[currentIndex - 1].classList.add('selected', 'bg-gray-100', 'dark:bg-gray-700');
                    }
                } else if (e.key === 'Enter') {
                    e.preventDefault();
                    const selectedItem = suggestionsList.querySelector('.suggestion-item.selected');
                    if (selectedItem) {
                        selectSuggestion(selectedItem.dataset.color);
                    }
                } else if (e.key === 'Escape') {
                    hideSuggestions();
                }
            });
        }

        // Initialiser l'autocomplétion pour les deux formulaires
        document.addEventListener('DOMContentLoaded', function () {
            const attributeType = "{{ $attribute->type }}";

            if (attributeType === 'color') {
                createColorAutocomplete('value', 'second_value', 'add-suggestions');
                createColorAutocomplete('edit_value', 'edit_second_value', 'edit-suggestions');

                // Synchroniser les inputs color avec les champs hex
                initializeColorInputs();
            }
        });

        // Fonction pour initialiser la synchronisation des inputs color
        function initializeColorInputs() {
            // Pour le modal d'ajout
            const addColorInput = document.getElementById('second_value');
            const addHexInput = document.getElementById('add_color_hex');
            const addPreview = document.getElementById('add_color_preview');

            if (addColorInput && addHexInput && addPreview) {
                // Initialiser la valeur
                addHexInput.value = addColorInput.value;
                updateColorPreview(addPreview, addColorInput.value);

                // Écouter les changements
                addColorInput.addEventListener('input', function () {
                    addHexInput.value = this.value;
                    updateColorPreview(addPreview, this.value);
                });

                // Permettre la saisie manuelle dans le champ hex
                addHexInput.addEventListener('input', function () {
                    const hexValue = this.value;
                    if (/^#[0-9A-F]{6}$/i.test(hexValue)) {
                        addColorInput.value = hexValue;
                        updateColorPreview(addPreview, hexValue);
                    }
                });
            }

            // Pour le modal d'édition
            const editColorInput = document.getElementById('edit_second_value');
            const editHexInput = document.getElementById('edit_color_hex');
            const editPreview = document.getElementById('edit_color_preview');

            if (editColorInput && editHexInput && editPreview) {
                // Initialiser la valeur
                editHexInput.value = editColorInput.value;
                updateColorPreview(editPreview, editColorInput.value);

                // Écouter les changements
                editColorInput.addEventListener('input', function () {
                    editHexInput.value = this.value;
                    updateColorPreview(editPreview, this.value);
                });

                // Permettre la saisie manuelle dans le champ hex
                editHexInput.addEventListener('input', function () {
                    const hexValue = this.value;
                    if (/^#[0-9A-F]{6}$/i.test(hexValue)) {
                        editColorInput.value = hexValue;
                        updateColorPreview(editPreview, hexValue);
                    }
                });
            }
        }

        // Fonction pour mettre à jour la prévisualisation de couleur
        function updateColorPreview(previewElement, colorValue) {
            const colorCircle = previewElement.querySelector('div');
            if (colorCircle) {
                colorCircle.style.backgroundColor = colorValue;
            }
        }

        function editValue(valueId, value, secondValue, order) {
            // Remplir le formulaire
            document.getElementById('edit_value').value = value;
            const type = "{{ $attribute->type }}";
            if (type === 'color') {
                // Si c'est une couleur CSS, la convertir en HEX
                let hexValue;
                if (secondValue && !secondValue.startsWith('#')) {
                    hexValue = cssColorToHex(secondValue);
                } else {
                    hexValue = secondValue || '#000000';
                }
                const editSecondValue = document.getElementById('edit_second_value');
                const editColorHex = document.getElementById('edit_color_hex');
                const editColorPreview = document.getElementById('edit_color_preview');

                if (editSecondValue) editSecondValue.value = hexValue;
                if (editColorHex) editColorHex.value = hexValue;
                if (editColorPreview) updateColorPreview(editColorPreview, hexValue);
            }
            // Pour les autres types, on ne fait rien car il n'y a plus de champ secondaire

            // Mettre à jour l'action du formulaire
            const form = document.getElementById('editValueForm');
            form.action = '{{ route("settings.attributes.values.update", [$attribute, ":value"]) }}'.replace(':value', valueId);

            // Récupérer les informations de la valeur via AJAX
            fetch(`{{ route('settings.attributes.values.show', [$attribute, ':value']) }}`.replace(':value', valueId))
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const valueData = data.value;

                        // Mettre à jour les informations dans la box
                        document.getElementById('value-position').textContent = valueData.order || 'Non définie';
                        document.getElementById('variants-count').textContent = valueData.variants_count || '0';
                        document.getElementById('articles-count').textContent = valueData.articles_count || '0';
                        document.getElementById('created-at').textContent = valueData.created_at_formatted || '-';
                    }
                })
                .catch(error => {
                    console.error('Erreur lors de la récupération des informations:', error);
                    // Valeurs par défaut en cas d'erreur
                    document.getElementById('value-position').textContent = order || 'Non définie';
                    document.getElementById('variants-count').textContent = '-';
                    document.getElementById('articles-count').textContent = '-';
                    document.getElementById('created-at').textContent = '-';
                });

            // Ouvrir le modal
            openModal('edit-value');
        }
    </script>

    <!-- Modal de confirmation de désactivation pour les valeurs -->
    <x-modal name="deactivate-value" title="Confirmer la désactivation" icon="exclamation-triangle" iconColor="red"
             size="2xl">
        <div class="text-center">
            <p class="text-sm text-gray-500" id="deactivateValueMessage">
                Êtes-vous sûr de vouloir désactiver cette valeur ?
            </p>
        </div>

        <x-slot name="actions">
            <form id="deactivateValueForm" method="POST" class="inline" data-ajax>
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="px-4 py-2 bg-red-500 text-white text-base font-medium rounded-md mr-2 hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-300">
                    Désactiver
                </button>
            </form>
            <button onclick="closeModal('deactivate-value')"
                    class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300">
                Annuler
            </button>
        </x-slot>
    </x-modal>

    <script>
        function showDeactivateValueModal(valueId, valueName, articlesCount, variantsCount) {
            const form = document.getElementById('deactivateValueForm');
            const message = document.getElementById('deactivateValueMessage');

            // Mettre à jour le formulaire
            form.action = `{{ route('settings.attributes.values.destroy', [$attribute, ':value']) }}`.replace(':value', valueId);

            // Mettre à jour le message
            if (articlesCount > 0 || variantsCount > 0) {
                message.innerHTML = `Êtes-vous sûr de vouloir désactiver la valeur <strong>${valueName}</strong> ?<br><br>Vous avez actuellement <strong>${articlesCount} articles</strong> et <strong>${variantsCount} variants</strong> encore liés à cette valeur.<br><br><em>Note : Vous pourrez réactiver cette valeur depuis la section archives.</em>`;
            } else {
                message.innerHTML = `Êtes-vous sûr de vouloir désactiver la valeur <strong>${valueName}</strong> ?<br><br><em>Note : Vous pourrez réactiver cette valeur depuis la section archives.</em>`;
            }

            openModal('deactivate-value');
        }
    </script>

    <!-- Sortable.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        function loadValuesTable() {
            fetch("{{ route('settings.attributes.values.table', $attribute) }}")
                .then(res => res.text())
                .then(html => {
                    document.getElementById('values-table').innerHTML = html;
                    // Réactiver le drag & drop après chargement
                    enableSortable();
                });
        }

        function loadArchivesTable() {
            fetch("{{ route('settings.attributes.values.archivesTable', $attribute) }}")
                .then(res => res.text())
                .then(html => {
                    document.getElementById('archives-table').innerHTML = html;
                    updateArchivesCount();
                });
        }

        function updateArchivesCount() {
            const archivesTable = document.getElementById('archives-table');
            const rows = archivesTable.querySelectorAll('tbody tr');

            // Compter seulement les lignes qui ne sont pas des messages "Aucune valeur archivée"
            let count = 0;
            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                if (cells.length > 1) { // Une vraie ligne de données a plusieurs cellules
                    const firstCell = cells[0];
                    const hasValue = firstCell.textContent.trim() &&
                        !firstCell.textContent.includes('Aucune valeur archivée');
                    if (hasValue) {
                        count++;
                    }
                }
            });

            document.getElementById('archives-count').textContent = count;
            document.getElementById('archives-count-plural').textContent = count > 1 ? 's' : '';

            // Afficher/masquer la section archives selon le nombre
            const container = document.getElementById('archives-section-container');
            if (count > 0) {
                container.style.display = 'block';
            } else {
                container.style.display = 'none';
                // Si on masque, fermer aussi la section
                const archivesSection = document.getElementById('archives-section');
                if (!archivesSection.classList.contains('hidden')) {
                    toggleArchives();
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            loadValuesTable();
            loadArchivesTable();
        });

        function enableSortable() {
            const el = document.getElementById('sortable-values');
            if (!el) return;
            new Sortable(el, {
                handle: '.fa-grip-vertical',
                animation: 150,
                onEnd: function () {
                    const order = Array.from(el.querySelectorAll('tr')).map(tr => tr.dataset.id);
                    fetch("{{ route('settings.attributes.values.updateOrder', $attribute) }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({order})
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                showNotif('Nouvel ordre enregistré !');
                                loadValuesTable();
                            }
                        });
                }
            });
        }

        function showNotif(msg, isError = false) {
            let notif = document.createElement('div');
            notif.className = `fixed top-4 right-4 bg-${isError ? 'red' : 'green'}-500 text-white px-4 py-2 rounded shadow z-50`;
            notif.innerText = msg;
            document.body.appendChild(notif);
            setTimeout(() => notif.remove(), 2000);
        }

        // Fonction pour réinitialiser le modal d'ajout
        function resetAddModal() {
            const form = document.querySelector('form[action$="/values"]');
            if (form) {
                form.reset();
                // Réinitialiser le champ hex si c'est un attribut de couleur
                const attributeType = "{{ $attribute->type }}";
                if (attributeType === 'color') {
                    const colorInput = document.getElementById('second_value');
                    const hexInput = document.getElementById('add_color_hex');
                    const preview = document.getElementById('add_color_preview');
                    if (colorInput && hexInput && preview) {
                        colorInput.value = '#000000';
                        hexInput.value = '#000000';
                        updateColorPreview(preview, '#000000');
                    }
                }
            }
        }

        function submitAddForm() {
            const form = document.querySelector('form[action$="/values"]');
            if (!form) {
                console.error('Add form not found');
                return;
            }

            // Soumettre le formulaire en AJAX
            const formData = new FormData(form);

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotif(data.message, false);
                        closeModal('add-value');
                        form.reset();
                        loadValuesTable();
                    } else {
                        showNotif(data.message || 'Erreur lors de l\'ajout', true);
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    showNotif('Erreur lors de l\'ajout', true);
                });
        }

        function submitEditForm() {
            const form = document.getElementById('editValueForm');
            if (!form) {
                console.error('Edit form not found');
                return;
            }

            // Debug: afficher les données du formulaire
            const formData = new FormData(form);

            // Soumettre le formulaire en AJAX

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotif(data.message, false);
                        closeModal('edit-value');
                        loadValuesTable();
                    } else {
                        showNotif(data.message || 'Erreur lors de la modification', true);
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    showNotif('Erreur lors de la modification', true);
                });
        }

        function toggleArchives() {
            const archivesSection = document.getElementById('archives-section');
            const archivesIcon = document.getElementById('archives-icon');
            const archivesToggleText = document.getElementById('archives-toggle-text');

            if (archivesSection.classList.contains('hidden')) {
                archivesSection.classList.remove('hidden');
                archivesIcon.classList.remove('fa-chevron-down');
                archivesIcon.classList.add('fa-chevron-up');
                archivesToggleText.textContent = 'Masquer';
            } else {
                archivesSection.classList.add('hidden');
                archivesIcon.classList.remove('fa-chevron-up');
                archivesIcon.classList.add('fa-chevron-down');
                archivesToggleText.textContent = 'Afficher';
            }
        }

        // Fonction pour recharger les tableaux après une action
        function reloadTables() {
            loadValuesTable();
            loadArchivesTable();
        }

        // Intercepter les soumissions de formulaires pour recharger les tableaux
        document.addEventListener('submit', function (e) {

            // Empêcher le rechargement de page pour tous les formulaires AJAX
            if (e.target.hasAttribute('data-ajax')) {
                e.preventDefault();

                // Traiter le formulaire AJAX
                fetch(e.target.action, {
                    method: e.target.method,
                    body: new FormData(e.target),
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            showNotif(data.message);
                            reloadTables();

                            // Fermer les modals selon le type d'action
                            if (e.target.id === 'deactivateValueForm') {
                                closeModal('deactivate-value');
                            } else if (e.target.action.includes('/store')) {
                                closeModal('add-value');
                                // Réinitialiser le formulaire d'ajout
                                e.target.reset();
                            } else if (e.target.id === 'editValueForm') {
                                closeModal('edit-value');
                            }
                        } else {
                            // Afficher les erreurs de validation si présentes
                            if (data.errors) {
                                Object.keys(data.errors).forEach(field => {
                                    showNotif(data.errors[field][0], true);
                                });
                            } else if (data.message) {
                                showNotif(data.message, true);
                            }
                        }
                    })
                    .catch(error => {
                        if (error instanceof SyntaxError) {
                            fetch(e.target.action, {
                                method: e.target.method,
                                body: new FormData(e.target),
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            })
                                .then(res => res.text())
                                .then(txt => {
                                    showNotif('Réponse erroné !', true);
                                });
                        } else {
                            showNotif('Erreur lors de l\'opération', true);
                        }
                    });
            }
        });
    </script>
</x-app-layout>
