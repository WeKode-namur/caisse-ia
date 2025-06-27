<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestion des Clients') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Première ligne : Filtres -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 mb-6">
                <form method="GET" action="{{ route('clients.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Recherche -->
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Recherche</label>
                        <input type="text" 
                               name="search" 
                               id="search" 
                               value="{{ $search }}"
                               placeholder="Nom, email, téléphone..."
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Statut -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                        <select name="status" id="status" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Tous</option>
                            <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Actifs</option>
                            <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Inactifs</option>
                        </select>
                    </div>

                    <!-- Type -->
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                        <select name="type" id="type" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="all" {{ $type === 'all' ? 'selected' : '' }}>Tous</option>
                            <option value="customer" {{ $type === 'customer' ? 'selected' : '' }}>Clients particuliers</option>
                            <option value="company" {{ $type === 'company' ? 'selected' : '' }}>Entreprises</option>
                        </select>
                    </div>

                    <!-- Tri -->
                    <div>
                        <label for="sort" class="block text-sm font-medium text-gray-700 mb-1">Tri</label>
                        <select name="sort" id="sort" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="name" {{ $sort === 'name' ? 'selected' : '' }}>Nom</option>
                            <option value="created_at" {{ $sort === 'created_at' ? 'selected' : '' }}>Date de création</option>
                            <option value="loyalty_points" {{ $sort === 'loyalty_points' ? 'selected' : '' }}>Points fidélité</option>
                        </select>
                    </div>

                    <!-- Boutons -->
                    <div class="md:col-span-4 flex gap-2">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            Filtrer
                        </button>
                        <a href="{{ route('clients.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                            Réinitialiser
                        </a>
                        <a href="{{ route('clients.create') }}" class="ml-auto px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                            Nouveau Client
                        </a>
                    </div>
                </form>
            </div>

            <!-- Deuxième ligne : Statistiques -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Clients Particuliers</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_customers'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Entreprises</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_companies'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Nouveaux cette semaine</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $stats['new_this_week'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Points Fidélité</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['total_loyalty_points']) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Troisième ligne : Liste des clients -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">
                        Liste des Clients ({{ $paginator->total() }} résultats)
                    </h3>
                </div>
                
                <div class="p-6">
                    @if($paginator->count() > 0)
                        <div class="space-y-4">
                            @foreach($paginator as $client)
                                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                    <div class="flex items-center">
                                        <!-- Avatar -->
                                        <div class="w-12 h-12 rounded-full flex items-center justify-center mr-4
                                            {{ $client->type === 'customer' ? 'bg-blue-100 text-blue-600' : 'bg-green-100 text-green-600' }}">
                                            @if($client->type === 'customer')
                                                <span class="font-medium text-sm">
                                                    {{ substr($client->first_name, 0, 1) }}{{ substr($client->last_name, 0, 1) }}
                                                </span>
                                            @else
                                                <span class="font-medium text-sm">
                                                    {{ substr($client->name, 0, 2) }}
                                                </span>
                                            @endif
                                        </div>

                                        <!-- Informations -->
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2">
                                                <h4 class="font-medium text-gray-900">{{ $client->display_name }}</h4>
                                                <span class="px-2 py-1 text-xs rounded-full 
                                                    {{ $client->type === 'customer' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                                    {{ $client->type === 'customer' ? 'Particulier' : 'Entreprise' }}
                                                </span>
                                                @if(!$client->is_active)
                                                    <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">
                                                        Inactif
                                                    </span>
                                                @endif
                                            </div>
                                            <p class="text-sm text-gray-600">{{ $client->number }}</p>
                                            @if($client->email)
                                                <p class="text-sm text-gray-500">{{ $client->email }}</p>
                                            @endif
                                            @if($client->phone)
                                                <p class="text-sm text-gray-500">{{ $client->phone }}</p>
                                            @endif
                                        </div>

                                        <!-- Points fidélité -->
                                        <div class="text-right mr-4">
                                            <p class="text-sm font-medium text-gray-900">{{ number_format($client->loyalty_points) }}</p>
                                            <p class="text-xs text-gray-500">points</p>
                                        </div>
                                    </div>

                                    <!-- Actions -->
                                    <div class="flex items-center gap-2">
                                        <a href="{{ $client->type === 'customer' ? route('clients.customers.show', $client) : route('clients.companies.show', $client) }}" 
                                           class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                            Voir
                                        </a>
                                        <a href="{{ $client->type === 'customer' ? route('clients.customers.edit', $client) : route('clients.companies.edit', $client) }}" 
                                           class="text-gray-600 hover:text-gray-800 text-sm font-medium">
                                            Modifier
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6">
                            {{ $paginator->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun client trouvé</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                @if($search || $status !== 'all' || $type !== 'all')
                                    Essayez de modifier vos critères de recherche.
                                @else
                                    Commencez par créer votre premier client.
                                @endif
                            </p>
                            <div class="mt-6">
                                <a href="{{ route('clients.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Nouveau Client
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
