<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Str;
use libphonenumber\PhoneNumberUtil;
use libphonenumber\ShortNumberInfo;

class Phone implements Rule
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
        $valid_phone_number = '';
        $phone_country_code = $spit_phone[1];
        $phoneUtil = PhoneNumberUtil::getInstance();
        $shortNumberUtil = ShortNumberInfo::getInstance();
        if (is_numeric($phone_number) && Str::startsWith($phone_number, '+') && strlen($phone_number) > 5):
//            dd(
//                $spit_phone,
//                $phone_number,
//                ($phone_number),
//            );
            $phoneNumber = $phoneUtil->parse($phone_number, $phone_country_code, null, true);
            $possibleNumber = $phoneUtil->isPossibleNumber($phoneNumber);
            $validNumber = $phoneUtil->isValidNumber($phoneNumber);
//            dd(
//                $spit_phone,
//                $phone_number,
//            );
            return ($possibleNumber && $validNumber) !== false;
        else:
            return false;
        endif;


//        dd(
//            $spit_phone,
//            $phone_number,
//            $possibleNumber,
//            $validNumber,
//        );


    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The mobile number should be number starts with + #Invalid number.';
    }
}
