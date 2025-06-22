
<div class="lg:w-2/3 bg-gray-50 dark:bg-gray-900 z-0 flex flex-col" x-data="registerTools()">
    <div class="border-b dark:border-gray-700 bg-white dark:bg-gray-800 lg:px-6 px-3 py-2 text-gray-400 dark:text-gray-600 text-xs pb-3 shadow">
        <div class="grid lg:grid-cols-2 grid-cols-1 gap-6">
            <!-- tools -->
            <div class="flex gap-2">
                @include('panel.register.tools.tool-buttons')
            </div>
            <!-- Category -->
            <div class="flex gap-2 justify-end">
                @include('panel.register.tools.category-buttons')
            </div>
        </div>
        <div>
            <div class="border rounded flex items-center mt-3 text-base dark:border-gray-700">
                <i class="fas fa-barcode px-3 dark:text-gray-300"></i>
                <input type="text" name="barcode" id="barcode" class="border-0 px-3 py-1.5 flex-1 outline-0 focus:text-black dark:bg-gray-800 dark:focus:text-white dark:text-gray-300">
                <button id="search_barcode" class="rounded-r bg-blue-500 h-full px-3 py-1.5 text-white hover:px-4 hover:opacity-75 duration-300"><i class="fas fa-search"></i></button>
            </div>
        </div>
    </div>
    @include('panel.register.tools.products-grid')
    @include('panel.register.modals.all-modals')
</div>
