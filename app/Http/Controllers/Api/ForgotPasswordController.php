<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password as FacadesPassword;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Rules\Password;

class ForgotPasswordController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function submitForgetPasswordForm(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email', 'max:255', 'exists:users,email'],
//            'phone_number' => ['required', 'string', 'exists:users,phone_number'],
        ]);

        $token = Str::random(64);
        $request_email = $request->email;

        DB::table('password_resets')->insert([
            'email' => $request_email,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);

        $user = User::where('email', '=', $request_email)->first();
        $user->sendPasswordResetNotification($token);

//        dd(
//            $user,
//            $token,
//        );

        return Response::json([
            "msg" => 'Reset password link sent on your email id.',
            "token" => $token,
        ], 200);
    }


    /**
     * @param $token
     */
    public function showResetPasswordForm($token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    public function submitResetPasswordForm(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'string', 'email', 'max:255', 'exists:users,email'],
//            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'password' => ['required', 'string', 'confirmed', new Password],
        ]);

        $updatePassword = DB::table('password_resets')
            ->where([
                'email' => $request->email,
                'token' => $request->token
            ])->first();

//        dd(
//            $updatePassword,
//            $request->post('token'),
//        );
        if (!$updatePassword) {
            return Response::json([
                "error" => 'Invalid token!',
            ], 200);
        }

        $user = User::where('email', $request->email)
            ->update(['password' => Hash::make($request->password)]);

        DB::table('password_resets')->where(['email' => $request->email])->delete();

        return Response::json([
            "message" => 'Your password has been changed!',
        ], 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function forgot_password(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255', 'exists:users,email'],
//            'phone_number' => ['required', 'string', 'exists:users,phone_number'],
        ]);

//        $response = FacadesPassword::sendResetLink($credentials);
        $status = FacadesPassword::sendResetLink(
            $request->only('email')
        );

        if ($status === FacadesPassword::RESET_LINK_SENT) {
            return Response::json([
//                "status" => __($status),
                "msg" => 'Reset password link sent on your email id.',
            ], 200);
        }

        throw ValidationException::withMessages([
            'email' => [trans($status)],
        ]);

//        return Response::json([
//            "msg" => 'Reset password link sent on your email id.',
//            "response" => $response,
//        ], 200);

//        return response()->json(["msg" => 'Reset password link sent on your email id.']);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function reset(Request $request): JsonResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'string', 'email', 'max:255', 'exists:users,email'],
            'password' => ['required', 'string', 'confirmed', new Password],
        ]);

        $status = FacadesPassword::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                $user->tokens()->delete();

                event(new PasswordReset($user));
            }
        );

        if ($status === FacadesPassword::PASSWORD_RESET) {
            return Response::json([
                'message' => 'Password reset successfully',
            ], 200);
        }

        return Response::json([
            'message' => __($status)
        ], 500);


    }

}
