<?php

namespace App\View\Components;

use Illuminate\View\Component;
use libphonenumber\PhoneNumberUtil;

class CountryCodeSelect extends Component
{
    public $countries_codes;
    public $phoneUtil;

    public $selected;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($selected = null)
    {
        $this->phoneUtil = PhoneNumberUtil::getInstance();
        $this->countries_codes = $this->phoneUtil->getSupportedRegions();
        $this->selected = $selected;
//        $region_codeForCountryCode = $phoneUtil->getRegionCodesForCountryCode($phone_country_code);
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.country-code-select');
    }
}
