<x-guest-layout>
    <x-auth-card>
        <x-slot name="logo">
            <a href="/">
                <x-application-logo class="w-20 h-20 fill-current text-gray-500"/>
            </a>
        </x-slot>

        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors"/>

        <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
            <div>
                <x-label for="name" :value="__('Name')"/>

                <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required
                         autofocus/>

                @error('name')
                <span class="flex items-center mt-1 font-medium ml-1 text-pink-600 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Email Address -->
            <div class="mt-4">
                <x-label for="email" :value="__('Email')"/>

                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required/>

                @error('email')
                <span class="flex items-center mt-1 font-medium ml-1 text-pink-600 text-sm">{{ $message }}</span>
                @enderror
            </div>


            <!-- Mobile Number -->
            <div class="mt-4" x-data="{ StartCountryCode:'' , start:'' }">
                <label for="mobile_number" class="block text-sm font-medium text-gray-700">Mobile Number</label>
                <div class="mt-1 relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    </div>
                    <input type="text" name="phone_number" id="mobile_number"
                           class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-2.5 pr-12 sm:text-sm border-gray-300 rounded-md"
                           x-bind:placeholder="start + StartCountryCode +' xxx xxx xxx'"
                           x-bind:value="start+StartCountryCode">
                    <div class="absolute inset-y-0 right-0 flex items-center">
                        <label for="countryCode" class="sr-only">country Code</label>
                        <x-country-code-select :selected="old('country_code')"/>
                    </div>
                </div>
                @error('country_code')
                <span class="flex items-center mt-1 font-medium ml-1 text-pink-600 text-sm">{{ $message }}</span>
                @enderror
                @error('phone_number')
                <span class="flex items-center mt-1 font-medium ml-1 text-pink-600 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-label for="password" :value="__('Password')"/>

                <x-input id="password" class="block mt-1 w-full"
                         type="password"
                         name="password"
                         required autocomplete="new-password"/>

                @error('password')
                <span class="flex items-center mt-1 font-medium ml-1 text-pink-600 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div class="mt-4">
                <x-label for="password_confirmation" :value="__('Confirm Password')"/>

                <x-input id="password_confirmation" class="block mt-1 w-full"
                         type="password"
                         name="password_confirmation" required/>
                @error('password')
                <span class="flex items-center mt-1 font-medium ml-1 text-pink-600 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div class="flex items-center justify-end mt-4">
                <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('login') }}">
                    {{ __('Already registered?') }}
                </a>

                <x-button class="ml-4">
                    {{ __('Register') }}
                </x-button>
            </div>
        </form>
    </x-auth-card>
</x-guest-layout>
