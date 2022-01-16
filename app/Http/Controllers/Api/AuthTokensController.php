<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Notifications\NewRecoveryCodeNotificatione;
use App\Rules\CountryCode;
use App\Rules\MatchPhoneCode;
use App\Rules\Phone;
use App\Rules\UniquePhone;
use Exception;
use Illuminate\Validation\Rule;
use Throwable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Rules\Password;
use Illuminate\Validation\Rules;
use libphonenumber\PhoneNumberUtil;


class AuthTokensController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $user_tokens = $request->user()->tokens;
        return Response::json([
            'user_tokens' => $user_tokens,
        ], 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        $request->merge(['phone_number' => $request->post('phone_number') . ',' . $request->post('country_code')]);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:users,name'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
//            'password' => ['required', 'string', 'confirmed', new Password],
            'password' => ['required', 'string', new Password],
            'country_code' => ['required', 'string', 'min:1', 'max:11', new CountryCode],
            'phone_number' => [
                'required',
                'string',
                'min:10',
                'max:30',
//                'unique:users',
                new Phone,
                new MatchPhoneCode(),
//                Rule::unique(User::class,'phone_number'),
                new UniquePhone(),
            ],
//            'permissions' => 'array',
//            'device_name' => 'required',
//            'fcm_token' => 'nullable',
        ]);

        /*        try {
                    $phoneUtil = PhoneNumberUtil::getInstance();
                    $phoneNumber = $phoneUtil->parse($data['phone_number'], 'PS', null, true);
                    $possibleNumber = $phoneUtil->isPossibleNumber($phoneNumber);//Check
                    $isPossibleNumberWithReason = $phoneUtil->isPossibleNumberWithReason($phoneNumber);
                    $validNumber = $phoneUtil->isValidNumber($phoneNumber);//Ceck
                    $validNumberForRegion = $phoneUtil->isValidNumberForRegion($phoneNumber, $phoneUtil->getRegionCodesForCountryCode($data['country_code'])[0]);
                    $phoneNumberRegion = $phoneUtil->getRegionCodeForNumber($phoneNumber);
                    $phoneNumberType = $phoneUtil->getNumberType($phoneNumber);
                    $regions = $phoneUtil->getSupportedRegions();
                    $getRegionCodesForCountryCode = $phoneUtil->getRegionCodesForCountryCode('44');
                } catch (Exception $exception) {
        //            report($exception);
                    return Response::json([
                        'exception' => $exception->getMessage(),
                    ], 200);
                }*/

        $spit_phone = explode(',', $data['phone_number']);
        $phone_number = $spit_phone[0];
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'phone_number' => $phone_number,
            'role_id' => 1,
        ]);
//        $token = $user->createToken($request->device_name, ['*']);
        $token = $user->createToken($data['email'], ['*']);

        // Notifications Email , SMS , Real-Time
//        $user->notify(new NewRecoveryCodeNotificatione($user));

        //Get Token as String without Hashing
        return Response::json([
            'token' => $token->plainTextToken,
            'user' => UserResource::make($user),
//            'request_data' => $data,
//            'phone_number' => explode(',', $data['phone_number']),
//            'phoneUtil' => $phoneUtil,
//            'phoneNumber' => $phoneNumber,
//            'possibleNumber' => $possibleNumber,
//            'isPossibleNumberWithReason' => $isPossibleNumberWithReason,
//            'validNumber' => $validNumber,
//            'validNumberForRegion' => $validNumberForRegion,
//            'phoneNumberRegion' => $phoneNumberRegion,
//            'phoneNumberType' => $phoneNumberType,
////            '$regions' => var_dump($regions),
//            'getRegionCodesForCountryCode' => ($phoneUtil->getRegionCodesForCountryCode('970')[0]),
//            'in_array' => in_array($phoneUtil->getRegionCodesForCountryCode('970')[0], $regions),
//            'country_code' => $data['country_code'],
//            '#valid#' => ($possibleNumber && $validNumber && $validNumberForRegion) === false ? false : true,
//            'valid' => ($possibleNumber && $validNumber && $validNumberForRegion),
        ], 201);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
//            'permissions' => 'array',
//            'device_name' => 'required',
//            'fcm_token' => 'nullable',
        ]);

        //Validate User email and password
//        User::where('email', '=', $request->post('email'));
        $user = User::where('email', '=', $request->email)
            ->orWhere('phone_number', $request->email)//Check if phone number is exists
            ->first();

        if ($user && Hash::check($request->password, $user->password)):
            $token = $user->createToken($request->email, ['*']);

            // Notifications Email , SMS , Real-Time
            if ($user->two_factor_secret || $user->hasEnabledTwoFactorAuthentication()):
                $user->notify(new NewRecoveryCodeNotificatione($user));
            endif;

            //Get Token as String without Hashing
            return Response::json([
                'token' => $token->plainTextToken,
                'user' => UserResource::make($user),
            ], 201);
        endif;

        return Response::json([
            "message" => 'invalid credentials!',
        ], 401);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();
        $user->tokens()->findOrFail($id)->delete();

        return Response::json([
                'message' => 'Token deleted'
            ]
            , 200);
    }

    /**
     * @return JsonResponse
     */
    public function current_logout(): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();
        $current_token = $user->currentAccessToken();
        // Logout from current device
        $current_token->delete();

        return Response::json([
                'message' => 'You delete your current token --Token deleted'
            ]
            , 200);
    }

    /**
     * @return JsonResponse
     */
    public function logout_all(): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();
        $user->tokens()->delete();

        return Response::json([
                'message' => 'You delete all tokens --Tokens deleted'
            ]
            , 200);
    }
}
