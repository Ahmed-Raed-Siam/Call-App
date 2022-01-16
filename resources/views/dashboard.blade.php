<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    You're logged in!
                </div>
            </div>
            <div class="bg-white overflow-hidden sm:rounded-lg mt-2 py-6">
                <div class="block text-gray-700 text-sm font-bold mb-2">
                    <div
                        class="uppercase py-2 px-6 bg-white border-b-2 border-gray-200">{{ __('Tw Factor Authentication')}}</div>
                    <form class="bg-white rounded py-2 px-8 mb-4" method="POST"
                          action="{{ route('two-factor.enable') }}">
                        @csrf
                        @if (session('status') === 'two-factor-authentication-enabled')
                            <div
                                class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 mb-3 rounded relative"
                                role="alert">
                                <strong class="font-bold">2FA Enabled!</strong>
                                <span class="block sm:inline">Two factor authentication has been enabled.</span>
                                <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                                    <svg class="fill-current h-6 w-6 text-green-500" role="button"
                                         xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path
                                            d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
                                </span>
                            </div>
                        @endif
                        @if (session('status') === 'two-factor-authentication-disabled')
                            <div
                                class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 mb-3 rounded relative"
                                role="alert">
                                <strong class="font-bold">2FA Disabled!</strong>
                                <span class="block sm:inline">Two factor authentication has been disabled.</span>
                                <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                                    <svg class="fill-current h-6 w-6 text-red-500" role="button"
                                         xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path
                                            d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
                                </span>
                            </div>
                        @endif
                        @if(Auth::user()->two_factor_secret)
                            @method('DELETE')
                            <div class="pt-3 pb-8">
                                {{ Auth::user()->twoFactorQrCodeUrl() }}
                                {!! Auth::user()->twoFactorQrCodeSvg() !!}
                            </div>
                            <div class="pt-3 pb-8">
                                <h4>{{ __('Recovery Codes') }}</h4>
{{--                                @dd(Auth::user()->recoveryCodes()[0])--}}
                                <ul>
                                    @foreach(Auth::user()->recoveryCodes() as $recoverycode)
                                        <li>
                                            {{ $recoverycode }}
                                        </li>
                                    @endforeach
                                </ul>
{{--                                {{ dd((array) Auth::user()->recoveryCodes()) }}--}}

                            </div>
                            <label class="inline-block text-gray-700 text-sm font-bold mb-2">
                                Click to Disabled 2FA
                            </label>
                            <button
                                class="bg-transparent hover:bg-red-500 text-red-700 font-semibold hover:text-white py-2 px-4 border border-red-500 hover:border-transparent rounded"
                                type="submit">
                                <b class="font-bold">Disable</b> two-factor authentication
                            </button>
                        @else
                            <label class="inline-block text-gray-700 text-sm font-bold mb-2">
                                Click to Activate 2FA
                            </label>
                            <button
                                class="bg-transparent hover:bg-blue-500 text-blue-700 font-semibold hover:text-white py-2 px-4 border border-blue-500 hover:border-transparent rounded"
                                type="submit">
                                Activate 2FA
                            </button>
                        @endif
                    </form>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
