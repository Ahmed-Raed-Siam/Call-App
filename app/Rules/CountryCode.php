<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use libphonenumber\PhoneNumberUtil;

class CountryCode implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $phoneUtil = PhoneNumberUtil::getInstance();
        $region_codeForCountryCode = $phoneUtil->getRegionCodesForCountryCode($value);
        $regions = $phoneUtil->getSupportedRegions();

        if (count($region_codeForCountryCode) > 0):
            $validRegion_codeForCountryCode = $region_codeForCountryCode[0];
            if (in_array($validRegion_codeForCountryCode, $regions) === false):
                return false;
            else:
                return true;
            endif;
        else:
            return false;
        endif;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The country code doesn\'t exists.';
    }
}
