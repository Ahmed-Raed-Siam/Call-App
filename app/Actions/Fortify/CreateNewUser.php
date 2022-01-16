<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Rules\CountryCode;
use App\Rules\MatchPhoneCode;
use App\Rules\Phone;
use App\Rules\UniquePhone;

//use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param array $input
     * @return User
     */
    public function create(array $input)
    {
        $input['phone_number'] .= ',' . $input['country_code'];
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255', 'unique:users,name'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'password' => $this->passwordRules(),
            'country_code' => ['required', 'string', 'min:1', 'max:11', new CountryCode],
            'phone_number' => [
                'required',
                'string',
                'min:10',
                'max:30',
                new Phone,
                new MatchPhoneCode(),
                new UniquePhone(),
            ],
        ])->validate();

        $spit_phone = explode(',', $input['phone_number']);
        $phone_number = $spit_phone[0];
        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'phone_number' => $phone_number,
            'role_id' => 1,
        ]);
    }
}
