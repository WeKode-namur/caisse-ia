<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
{{--            <x-authentication-card-logo />--}}
        </x-slot>

        <x-validation-errors class="mb-4" />

        @session('status')
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ $value }}
            </div>
        @endsession

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div>
                <x-label for="email" value="{{ __('Adresse mail') }}" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            </div>

            <div class="mt-4">
                <x-label for="password" value="{{ __('Mot de passe') }}" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
            </div>

            <div class="lg:flex items-center justify-between mt-4 mb-4 text-sm text-gray-600 sm:text-right sm:block">
                <label for="remember_me" class="flex items-center">
                    <x-checkbox id="remember_me" name="remember" />
                    <span class="ms-2 text-sm text-gray-600">{{ __('Se souvenir de moi') }}</span>
                </label>

                <x-button class="lg:ms-4 lg:mt-0 mt-3 lg:w-auto w-full">
                    {{ __('Se connecter') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
