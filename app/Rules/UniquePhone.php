<?php

namespace App\Rules;

use App\Models\User;
use Illuminate\Contracts\Validation\Rule;

class UniquePhone implements Rule
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
        $phone_number_exists = User::where('phone_number', '=', $phone_number)->first();
//        $phone_number_exists = User::where('phone_number', '=', $phone_number)->firstOrFail();


//        dd(
//            $spit_phone,
//            $phone_number,
//            $phone_number_exists,
//            User::find($phone_number, 'phone_number'),
//            is_null($phone_number_exists),
//            '$phone_number_exists !== null',
//            $phone_number_exists !== null,
//            $phone_number_exists === null,
//        );

        if ($phone_number_exists !== null):
            return false;
        endif;
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The mobile number has already taken.';
    }
}
