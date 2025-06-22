<div x-show="showClientModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
    <!-- Contenu modal client -->
    <div class="bg-black bg-opacity-50 flex items-center justify-center p-4 h-full w-full backdrop-blur-sm">
        <div class="bg-white rounded xl:w-2/5 md:w-2/3 w-full max-h-[90vh] flex flex-col">
            <div class="border-b flex gap-2 items-center py-2 px-3">
                <h2 class="text-xl">Sélectionner un client</h2>
                <button type="button" class="ms-auto bg-green-500 text-white hover:opacity-75 rounded py-1 px-2 text-sm hover:shadow hover:scale-105 duration-300" title="Nouveau client">
                    <i class="fas fa-user-plus"></i>
                </button>
                <button @click="closeAllModals()" class="bg-red-500 text-white hover:opacity-75 rounded py-1 px-2 text-sm hover:shadow hover:scale-105 duration-300">
                    <i class="fas fa-x"></i>
                </button>
            </div>
            <div class="p-4 flex-1 overflow-hidden flex flex-col">
                <!-- Barre de recherche -->
                <div class="border rounded flex items-center text-base">
                    <i class="fas fa-search px-3 text-gray-400"></i>
                    <input type="text" name="search_client" id="search_client" placeholder="Rechercher un client..." class="border-0 px-3 py-2 flex-1 outline-0 focus:text-black">
                </div>

                @php
                    $faker = Faker\Factory::create();
                    $clients = collect(range(1, 4))->map(function() use ($faker) {
                        $firstName = $faker->firstName();
                        $lastName = $faker->lastName();
                        $initials = substr($firstName, 0, 1) . substr($lastName, 0, 1);

                        return (object) [
                            'id' => $faker->numberBetween(1, 1000),
                            'firstName' => $firstName,
                            'lastName' => $lastName,
                            'name' => $firstName . ' ' . $lastName,
                            'email' => $faker->email(),
                            'phone' => $faker->phoneNumber(),
                            'initials' => $initials,
                            'company' => $faker->optional(0.6)->company(),
                            'point' => $faker->numberBetween(0, 5250),
                            'lastVisit' => $faker->dateTimeBetween('-6 months', 'now')->format('d/m/Y'),
                            'bgColor' => $faker->randomElement(['bg-blue-500', 'bg-green-500', 'bg-purple-500', 'bg-pink-500', 'bg-indigo-500'])
                        ];
                    });
                @endphp

                    <!-- Liste des clients -->
                <div class="mt-4 flex-1 overflow-y-auto">
                    <ul role="list" class="divide-y divide-slate-200">
                        @foreach($clients as $client)
                            <li class="flex py-3 hover:bg-gray-50 cursor-pointer rounded px-2 transition-all group hover:shadow-sm">
                                <!-- Avatar avec initiales -->
                                <div class="h-12 w-12 {{ $client->bgColor }} rounded-full flex items-center justify-center text-white font-medium">
                                    {{ $client->initials }}
                                </div>

                                <div class="ml-3 overflow-hidden flex-1">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-slate-900">{{ $client->name }}</p>
                                            <p class="text-sm text-slate-500 truncate">{{ $client->email }}</p>
                                            @if($client->company)
                                                <p class="text-xs text-slate-400 flex items-center">
                                                    <i class="fas fa-building text-xs mr-1"></i>
                                                    {{ $client->company }}
                                                </p>
                                            @endif
                                            <p class="text-xs text-slate-400 mt-1">Dernière visite: {{ $client->lastVisit }}</p>
                                        </div>
                                        <div class="text-right ml-4">
                                            <p class="text-sm font-medium text-green-600">{{ number_format($client->point, 0, ',', ' ') }}</p>
                                            <p class="text-xs text-slate-400">Points</p>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
