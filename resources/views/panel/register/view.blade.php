<x-app-layout>
    <div class="bg-white dark:bg-gray-800 shadow-inner dark:text-gray-200 h-full">
        <div class="lg:flex items-stretch h-full">
            @include('panel.register.cart-column')
            @include('panel.register.tools-column')
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('js/register.js') }}"></script>
        <script>
            window.registerConfig = {
                routes: {
                    cart: '/register/partials/cart',
                    products: '/register/partials/products',
                    payment: '/register/partials/payment',
                    transactions: '/register/partials/transactions'
                },
                user: @json(auth()->user()),
                cashRegister: @json(session('current_cash_register_id')),
                categories: @json($categories ?? []),
                paymentMethods: @json($paymentMethods ?? []),
                customerManagement: @json($customerManagementEnabled ?? false)
            };
        </script>
    @endpush
</x-app-layout>
