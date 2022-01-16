@if(Route::is('dashboard.users.edit'))
    <select id="countryCode" name="country_code" x-model="StartCountryCode" x-on:change="start='+'"
            class="focus:ring-indigo-500 focus:border-indigo-500 h-full py-0 pl-2 pr-7 border-transparent bg-transparent text-gray-500 sm:text-sm rounded-md">
        @foreach ($countries_codes as $country_code)
            <option value="{{ $phoneUtil->getCountryCodeForRegion($country_code) }}"
                    @if($selected === $phoneUtil->getCountryCodeForRegion($country_code)) selected @endif
            >{{$country_code}}</option>
        @endforeach
    </select>
@else
    <select id="countryCode" name="country_code" x-model="StartCountryCode" x-on:change="start='+'"
            class="focus:ring-indigo-500 focus:border-indigo-500 h-full py-0 pl-2 pr-7 border-transparent bg-transparent text-gray-500 sm:text-sm rounded-md">
        @foreach ($countries_codes as $country_code)
            <option value="{{ $phoneUtil->getCountryCodeForRegion($country_code) }}"
                    @if($selected == $phoneUtil->getCountryCodeForRegion($country_code)) selected @endif
            >{{ $country_code }}
            </option>
        @endforeach
    </select>
@endif
