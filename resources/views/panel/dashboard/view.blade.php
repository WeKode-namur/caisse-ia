<x-app-layout>
    <div class="lg:py-12">
        <div class="max-w-7xl mx-auto px-0 lg:px-8">
            <div class="bg-white/50 backdrop-blur dark:bg-gray-800/50  dark:text-gray-200 overflow-hidden shadow-xl lg:rounded-lg mb-6">
                <h1 class="text-gray-900 dark:text-gray-50 font-bold px-3 py-2 border-b border-gray-300 dark:border-gray-700 text-3xl lg:block hidden">Caisse {{ config('app.name') }}</h1>
                <div class="p-4">
                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Accusantium ad autem cumque, expedita nam quae temporibus. A, adipisci ex ipsam minus odit optio quae, quod recusandae rem tempora ullam voluptatem.</p>
                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Accusantium ad autem cumque, expedita nam quae temporibus. A, adipisci ex ipsam minus odit optio quae, quod recusandae rem tempora ullam voluptatem.</p>
                </div>
            </div>
            <div class="grid lg:grid-cols-4 grid-cols-2 lg:gap-12 gap-6 mb-6 lg:px-0 px-6 hover:*:scale-105 *:duration-300">
                <div class="bg-white/50 backdrop-blur-sm dark:bg-gray-800/50  dark:text-gray-200 overflow-hidden shadow-xl rounded-lg">
                    <div class="p-4">
                        <div class="flex gap-6 items-center justify-center mb-3">
                            <i class="fas fa-user"></i>
                            <p>Clients</p>
                        </div>
                        <div class="text-center text-4xl text-gray-300 dark:text-gray-700">- -</div>
                    </div>
                </div>
                <div class="bg-white/50 backdrop-blur-sm dark:bg-gray-800/50  dark:text-gray-200 overflow-hidden shadow-xl rounded-lg">
                    <div class="p-4">
                        <div class="flex gap-6 items-center justify-center mb-3">
                            <i class="fas fa-cash-register"></i>
                            <p>Transactions</p>
                        </div>
                        <div class="text-center text-4xl text-gray-300 dark:text-gray-700">- -</div>
                    </div>
                </div>
                <div class="bg-white/50 backdrop-blur-sm dark:bg-gray-800/50  dark:text-gray-200 overflow-hidden shadow-xl rounded-lg">
                    <div class="p-4">
                        <div class="flex gap-6 items-center justify-center mb-3">
                            <i class="fas fa-boxes-stacked"></i>
                            <p>Stock</p>
                        </div>
                        <div class="text-center text-4xl text-gray-300 dark:text-gray-700">- -</div>
                    </div>
                </div>
                <div class="dark:text-gray-200 border-2 border-dashed dark:border-gray-700 overflow-hidden rounded-lg">
                    <div class="p-4 h-full">
                        <div class="flex gap-6 items-center justify-center h-full text-4xl text-gray-300 dark:text-gray-700">
                            <i class="fas fa-question"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="grid lg:grid-cols-2 grid-cols-4 lg:gap-12 gap-6 mb-6 lg:px-0 px-6 hover:*:scale-105 *:duration-300">

                <div class="bg-white/50 backdrop-blur-sm dark:bg-gray-800/50  dark:text-gray-200 overflow-hidden shadow-xl rounded-lg">
                    <h2 class="font-semibold text-xl px-3 py-2 border-b border-gray-300 dark:border-gray-700">Chiffre de la semaine</h2>
                    <div class="p-4 h-full text-gray-500">
                        <div class="flex">
                            <div class="pr-1">Chiffre</div>
                            <div class="flex-1 h-48 p-3 text-gray-300 dark:text-gray-700 border-l border-b">
                                Graphique
                            </div>
                        </div>
                        <div class="text-gray-500 text-end pt-1">Date</div>
                    </div>
                </div>

                <div class="bg-white/50 backdrop-blur-sm dark:bg-gray-800/50  dark:text-gray-200 overflow-hidden shadow-xl rounded-lg">
                    <h2 class="font-semibold text-xl px-3 py-2 border-b border-gray-300 dark:border-gray-700">Chiffre d'affaire du mois</h2>
                    <div class="p-4 h-full text-gray-500">
                        <div class="flex">
                            <div class="pr-1">Chiffre</div>
                            <div class="flex-1 h-48 p-3 text-gray-300 dark:text-gray-700 border-l border-b">
                                Graphique
                            </div>
                        </div>
                        <div class="text-gray-500 text-end pt-1">Date</div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
