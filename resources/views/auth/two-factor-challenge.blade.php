<x-guest-layout>
    <x-auth-card>
        <x-slot name="logo">
            <a href="/">
                <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
            </a>
        </x-slot>

        <div class="mb-4 text-sm text-gray-600">
            {{ __('Two Factory Challenge') }}
        </div>

        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <form method="POST" action="{{ route('two-factor.login') }}">
        @csrf

        <!-- Two Factory Code -->
            <div>
                <x-label for="code" :value="__('code')" />

                <x-input id="code" class="block mt-1 w-full"
                         type="code"
                         name="code"
                         required autocomplete="current-password" />
            </div>

            <div class="flex justify-end mt-4">
                <x-button>
                    {{ __('Submit') }}
                </x-button>
            </div>
        </form>

        <div class="border-t-2 mt-4 py-12">
            <div>
                <div class="mb-4 text-sm text-gray-600">
                    {{ __('Two Factory Recovery Code') }}
                </div>

                <!-- Validation Errors -->
                <x-auth-validation-errors class="mb-4" :errors="$errors"/>

                <form method="POST" action="{{ route('two-factor.login') }}">
                @csrf

                <!-- Two Factory Recovery Code -->
                    <div>
                        <x-label for="recoveryCode" :value="__('Recovery Code')"/>

                        <x-input id="recoveryCode" class="block mt-1 w-full"
                                 type="recovery_code"
                                 name="recovery_code"
                                 required autocomplete="recovery_code"/>
                    </div>

                    <div class="flex justify-end mt-4">
                        <x-button>
                            {{ __('Submit') }}
                        </x-button>
                    </div>
                </form>
            </div>
        </div>

    </x-auth-card>
</x-guest-layout>
