@if($addresses->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach($addresses as $address)
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-white/90 dark:bg-gray-800/75 hover:shadow-md transition-shadow">
                <div class="flex justify-between items-start mb-2">
                    <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100">
                        {{ $addressTypes[$address->type] }}
                        @if($address->is_primary)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 ml-2">
                                Principale
                            </span>
                        @endif
                    </h4>
                    <div class="flex space-x-2">
                        <button onclick="editAddress({{ $address->id }})"
                                class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-200 text-sm transition-colors font-medium">
                            Modifier
                        </button>
                        @if(!$address->is_primary)
                            <button onclick="setPrimaryAddress({{ $address->id }})"
                                    class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-200 text-sm transition-colors font-medium">
                                Définir comme principale
                            </button>
                        @endif
                        <button onclick="deleteAddress({{ $address->id }})"
                                class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-200 text-sm transition-colors font-medium">
                            Supprimer
                        </button>
                    </div>
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-300">
                    <p>{{ $address->street }} {{ $address->number }}</p>
                    <p>{{ $address->postal_code }} {{ $address->city }}</p>
                    <p>{{ $address->country }}</p>
                </div>
            </div>
        @endforeach
    </div>
@else
    <p class="text-gray-500 dark:text-gray-400 text-center py-4">Aucune adresse enregistrée</p>
@endif
