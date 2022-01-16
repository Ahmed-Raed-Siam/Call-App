<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserCollectionResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Rules\CountryCode;
use App\Rules\MatchPhoneCode;
use App\Rules\Phone;
use App\Rules\UniquePhone;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Rules\Password;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $users = User::paginate();
//        return Response::json($users, 200);
        return Response::json(new UserCollectionResource($users), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
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
                new Phone,
                new MatchPhoneCode(),
                new UniquePhone(),
            ],
            'avatar_url' => ['nullable', 'image', 'mimes:jpg,bmp,png',],
            'role_id' => ['string', 'numeric', 'exists:roles,id'],
//            'permissions' => 'array',
//            'device_name' => 'required',
//            'fcm_token' => 'nullable',
        ]);

        $avatar_url = '';
        $user_name = str_replace(' ', '_', $data['name']);
        if ($request->hasFile('avatar_url')):
            $file_name = $request->file('avatar_url')->getClientOriginalName();// file name with extension
            $request_avatar_url = $user_name . '/' . $file_name;

            if (Storage::disk('uploads')->exists($request_avatar_url) ||
                in_array($request_avatar_url, Storage::disk('uploads')->allFiles($user_name))):
                $avatar_url = $request_avatar_url;
            else:
                $avatar_url = $request->file('avatar_url')->storeAs(
                    $user_name, $file_name,
                    [
                        'disk' => 'uploads',
                    ],
                );
            endif;
        endif;

        $spit_phone = explode(',', $data['phone_number']);
        $phone_number = $spit_phone[0];

//        dd(
//            $request->file('avatar_url'),
//            $user_name,
//            $request_avatar_url,
//            $request_avatar_url,
//        );

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'phone_number' => $phone_number,
            'avatar_url' => $avatar_url,
            'role_id' => $data['role_id'] ?? 1,
        ]);

        $message = "New user added successfully>";
        return Response::json([
            'message' => $message,
            'user' => UserResource::make($user),
        ], 200);

    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $user = User::find($id);
        $msg = '';
        if ($user === null):
            $msg = 'Wrong Request ! User Not Exists.';
            return Response::json([
                'message' => $msg,
            ], 200);
        endif;

        return Response::json([
            'user' => UserResource::make($user),
        ], 200);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $user = User::find($id);
        $msg = '';
        if ($user === null):
            $msg = 'Wrong Request ! User Not Exists.';
            return Response::json([
                'message' => $msg,
            ], 200);
        endif;

        $request->merge(['phone_number' => $request->post('phone_number') . ',' . $request->post('country_code')]);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255',
                Rule::unique('users')->ignore($user->id)
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
            ],
//            'password' => ['required', 'string', 'confirmed', new Password],
            'password' => ['required', 'string', new Password],
            'country_code' => ['required', 'string', 'min:1', 'max:11', new CountryCode],
            'phone_number' => [
                'required',
                'string',
                'min:10',
                'max:30',
                new Phone,
                new MatchPhoneCode(),
            ],
            'avatar_url' => ['nullable', 'image', 'mimes:jpg,bmp,png'],
            'role_id' => ['string', 'numeric', 'exists:roles,id'],
        ]);

        $old_user_name = $user->name;
        $user_name = $user->name;
        $user_email = $user->email;
        $user_phone_number = $user->phone_number;
        $user_avatar_url = $user->avatar_url;
        $user_role_id = $user->role_id;

        $request_name = $data['name'];
        $request_email = $data['email'];
        $request_password = $data['password'];
        $request_phone_number = $data['phone_number'];
        $request_role_id = $data['role_id'] ?? $user_role_id;

        $error = false;
        if ($request_name !== $user_name):
            $validator = Validator::make($request->all(), [
                'name' => ['required', 'string', 'max:255', 'unique:users,name'],
            ]);

            if ($validator->fails()) {
                $error = 'Error';
            } else {
                $user->name = $request_name;
            }
//            $validate_user_name = $request->validate([
//                'name' => ['required', 'string', 'max:255', 'unique:users,name'],
//            ]);
//            $user->name = $request_name;
        endif;

        if ($request_email !== $user_email):
            $request->validate([
                'email' => [
                    'required',
                    'string',
                    'email',
                    'max:255',
                    Rule::unique(User::class, 'email'),
                ],
            ]);
        endif;

        $spit_phone = explode(',', $request_phone_number);
        $phone_number = $spit_phone[0];

        if ($phone_number !== $user_phone_number):
            $request->validate([
                'phone_number' => ['required', 'string', 'min:10', 'max:30',
                    new Phone,
                    new MatchPhoneCode(),
                    new UniquePhone(),
                ],
            ]);
        endif;

        $request_avatar_url = '';
        $request_user_name = str_replace(' ', '_', $request_name);

        if ($request->hasFile('avatar_url') && $error === false):
            $file_name = $request->file('avatar_url')->getClientOriginalName();// file name with extension
            $request_file_path = $request_user_name . '/' . $file_name;
//            dd(
//                $user_name_error,
//                $request->hasFile('avatar_url'),
//                $user_name_error === 'True',
//                $request->hasFile('avatar_url') && $user_name_error === 'True',
//                '$user_name',
//                $user_name,
//                '$request_user_name',
//                $request_user_name,
//                $file_name,
//                $request_file_path,
//                Storage::disk('uploads')->exists($request_file_path),
//                in_array($request_file_path, Storage::disk('uploads')->allFiles($request_user_name)),
//            );
            if (Storage::disk('uploads')->exists($request_file_path) ||
                in_array($request_file_path, Storage::disk('uploads')->allFiles($request_user_name))):
                Storage::disk('uploads')->deleteDirectory($user_name);
                Storage::disk('uploads')->delete($user_avatar_url);
                $request_avatar_url = $request_file_path;
            else:
//                dd(
//                    $user_name_error,
//                    $request->hasFile('avatar_url'),
//                    $user_name_error === 'True',
//                    $request->hasFile('avatar_url') && $user_name_error === 'True',
//                    $user_name,
//                    $request_user_name,
//                    $file_name,
//                    $request_file_path,
//                    Storage::disk('uploads')->exists($request_file_path),
//                    in_array($request_file_path, Storage::disk('uploads')->allFiles($request_user_name)),
//                    $request_avatar_url
//                );
                Storage::disk('uploads')->deleteDirectory($request_user_name);
                Storage::disk('uploads')->deleteDirectory($user_name);
                Storage::disk('uploads')->delete($request_file_path);
                Storage::disk('uploads')->delete($user_avatar_url);
                $request_avatar_url = $request->file('avatar_url')->storeAs(
                    $request_user_name, $file_name,
                    [
                        'disk' => 'uploads',
                    ],
                );
//                dd(
//                    $request_user_name,
//                    $user_name,
//                    $file_name,
//                    '$request_file_path',
//                    $request_file_path,
//                    Storage::disk('uploads')->exists($request_file_path),
//                    in_array($request_file_path, Storage::disk('uploads')->allFiles($request_user_name)),
//                    '$request_avatar_url',
//                    $request_avatar_url,
//                );
            endif;
        endif;

//        dd(
//            $request->file('avatar_url'),
//            $user_name,
//            $request_avatar_url,
//            $request_avatar_url,
//        );

//        $user->save();

        $user->update([
            'name' => $request_user_name,
            'email' => $request_email,
            'password' => Hash::make($request_password),
            'phone_number' => $phone_number,
            'avatar_url' => $request_avatar_url,
            'role_id' => $request_role_id,
        ]);

        $message = "$old_user_name user updated to $user->name successfully>";

        return Response::json([
            'message' => $message,
            'user' => UserResource::make($user),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $user = User::find($id);
        $user_name = str_replace(' ', '_', $user->name);
        $user_avatar_url = $user->avatar_url;

        $msg = '';
        if ($user === null):
            $msg = 'Wrong Request ! User Not Exists.';
            return Response::json([
                'message' => $msg,
            ], 200);
        endif;

        $user_avatar_url_array = explode('/', $user_avatar_url);
        $base_user_avatar_url = $user_avatar_url_array[0] . '/';

        if (Storage::disk('uploads')->exists($user_avatar_url) ||
            in_array($user_avatar_url, Storage::disk('uploads')->allFiles($user_name))):
            Storage::disk('uploads')->deleteDirectory($base_user_avatar_url);
            Storage::disk('uploads')->delete($user_avatar_url);
        endif;

        $user->forceDelete();

        $message = "$user_name service type deleted successfully>";
        return Response::json([
                'message' => $message
            ]
            , 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function update_user_profile_information(Request $request): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();
        $msg = '';

        $request->merge(['phone_number' => $request->post('phone_number') . ',' . $request->post('country_code')]);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            /*As Ali Shaheen wish */
//            'password' => ['required', 'string', 'confirmed', new Password],
//            'password' => ['required', 'string', new Password],
            'country_code' => ['required', 'string', 'min:1', 'max:11', new CountryCode],
            'phone_number' => [
                'required',
                'string',
                'min:10',
                'max:30',
                new Phone,
                new MatchPhoneCode(),
            ],
            'avatar_url' => ['nullable', 'image', 'mimes:jpg,bmp,png'],
        ]);

        $old_user_name = $user->name;
        $user_name = $user->name;
        $user_email = $user->email;
        $user_phone_number = $user->phone_number;
        $user_avatar_url = $user->avatar_url;
        $user_role_id = $user->role_id;

        $request_name = $data['name'];
        $request_email = $data['email'];
        /*As Ali Shaheen wish */
//        $request_password = $data['password'];
        $request_phone_number = $data['phone_number'];

        $spit_phone = explode(',', $request_phone_number);
        $phone_number = $spit_phone[0];

        if ($phone_number !== $user_phone_number):
            $request->validate([
                'phone_number' => ['required', 'string', 'min:10', 'max:30',
                    new Phone,
                    new MatchPhoneCode(),
                    new UniquePhone(),
                ],
            ]);
        endif;

        $request_avatar_url = '';
        $request_user_name = str_replace(' ', '_', $request_name);

        if ($request->hasFile('avatar_url')):
            $file_name = $request->file('avatar_url')->getClientOriginalName();// file name with extension
            $request_file_path = $request_user_name . '/' . $file_name;

            if ($request_file_path !== $user_avatar_url):
                $request->validate([
                    'avatar_url' => [
                        'nullable',
                        'image',
                        'mimes:jpg,bmp,png',
                        Rule::unique('users')->ignore($user->id),
                    ],
                ]);
            endif;
//            dd(
//                $user_name_error,
//                $request->hasFile('avatar_url'),
//                $user_name_error === 'True',
//                $request->hasFile('avatar_url') && $user_name_error === 'True',
//                '$user_name',
//                $user_name,
//                '$request_user_name',
//                $request_user_name,
//                $file_name,
//                $request_file_path,
//                Storage::disk('uploads')->exists($request_file_path),
//                in_array($request_file_path, Storage::disk('uploads')->allFiles($request_user_name)),
//            );
            if (Storage::disk('uploads')->exists($request_file_path) ||
                in_array($request_file_path, Storage::disk('uploads')->allFiles($request_user_name))):
                $request_avatar_url = $request_file_path;
            else:
//                dd(
//                    $user_name_error,
//                    $request->hasFile('avatar_url'),
//                    $user_name_error === 'True',
//                    $request->hasFile('avatar_url') && $user_name_error === 'True',
//                    $user_name,
//                    $request_user_name,
//                    $file_name,
//                    $request_file_path,
//                    Storage::disk('uploads')->exists($request_file_path),
//                    in_array($request_file_path, Storage::disk('uploads')->allFiles($request_user_name)),
//                    $request_avatar_url
//                );
                if (empty($user_avatar_url) === false):

                endif;
                Storage::disk('uploads')->deleteDirectory($user_name);
                Storage::disk('uploads')->delete($user_avatar_url . '/');
                $request_avatar_url = $request->file('avatar_url')->storeAs(
                    $request_user_name, $file_name,
                    [
                        'disk' => 'uploads',
                    ],
                );
//                dd(
//                    $request_user_name,
//                    $user_name,
//                    $file_name,
//                    '$request_file_path',
//                    $request_file_path,
//                    Storage::disk('uploads')->exists($request_file_path),
//                    in_array($request_file_path, Storage::disk('uploads')->allFiles($request_user_name)),
//                    '$request_avatar_url',
//                    $request_avatar_url,
//                );
            endif;
        endif;

//        dd(
//            $request->file('avatar_url'),
//            $user_name,
//            $request_avatar_url,
//            $request_avatar_url,
//        );

//        $user->save();

        $user->update([
            'name' => $request_user_name,
            'email' => $request_email,
            /*As Ali Shaheen wish */
//            'password' => Hash::make($request_password),
            'phone_number' => $phone_number,
            'avatar_url' => $request_avatar_url,
            'role_id' => $user_role_id,
        ]);

        $message = "$old_user_name user updated to $request_name successfully>";

        return Response::json([
            'message' => $message,
            'user' => UserResource::make($user),
        ], 200);
    }

    /**
     * @return JsonResponse
     */
    public function user_profile(): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();

        return Response::json([
            'user' => UserResource::make($user),
        ], 200);
    }

}
