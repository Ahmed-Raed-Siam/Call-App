<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Str;
use libphonenumber\PhoneNumberUtil;

class MatchPhoneCode implements Rule
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
        $spit_phone = explode(',', $value);
        $phone_number = $spit_phone[0];
        $phone_country_code = $spit_phone[1];

        $phoneUtil = PhoneNumberUtil::getInstance();
        if (is_numeric($phone_number) && Str::startsWith($phone_number, '+') && strlen($phone_number) > 5):
            $phoneNumber = $phoneUtil->parse($phone_number, $phone_country_code, null, true);
            $region_codeForCountryCode = $phoneUtil->getRegionCodesForCountryCode($phone_country_code);
            if (count($region_codeForCountryCode) > 0):
                $validRegion_codeForCountryCode = $region_codeForCountryCode[0];
                $validNumberForRegion = $phoneUtil->isValidNumberForRegion($phoneNumber, $validRegion_codeForCountryCode);
                return $validNumberForRegion !== false;
            else:
                return true;
            endif;
//            dd(
//                $spit_phone,
//                $phone_number,
//                $phone_country_code,
//                $region_codeForCountryCode,
//                $validNumberForRegion,
//            );
        endif;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The Country code doesn\'t match the mobile number.';
    }
}
