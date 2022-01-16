<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Rules\CountryCode;
use App\Rules\MatchPhoneCode;
use App\Rules\Phone;
use App\Rules\UniquePhone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Rules\Password;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(): Response
    {
        $users = User::paginate(10);
//        $users = DB::table('users')->paginate(10);

        return response()->view('dashboard.users.index', ['users' => $users]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(): Response
    {
        $roles = Role::all();
        return response()->view('dashboard.users.create', ['roles' => $roles]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
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
            'password' => ['required', 'string', 'confirmed', new Password],
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
        ]);

        $avatar_url = '';
        $user_name = str_replace(' ', '_', $data['name']);
        if ($request->hasFile('avatar_url')):
            $file_name = $request->file('avatar_url')->getClientOriginalName();// file name with extension
            $request_avatar_url = $user_name . '/' . $file_name;

            if (Storage::disk('uploads')->exists($request_avatar_url) ||
                in_array($request_avatar_url, Storage::disk('uploads')->allFiles($user_name), true)):
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

        $request_role_id = $request->post('role_id');
        $user_role = Role::find($request_role_id)->name;

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'phone_number' => $phone_number,
            'avatar_url' => $avatar_url,
            'role_id' => $data['role_id'] ?? 1,
        ]);


        // Status for Adding the New User To The System!
        $alert_status = 'alert-success';
        // Msg
        $msg = 'New User Added Successfully.';
        // Pref
        $pref = "You Add $user_name As New User To The System!<br>His ID : {$user->id} ,His Email : $user->email ,His Phone number : $phone_number . His role : $user_role . ";
        $status = ['alert_status' => $alert_status, 'msg' => $msg, 'pref' => $pref];

        $message = "New user added successfully>";
        return redirect()->back()->with('status', $status);

    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show(int $id): Response
    {
        $user = User::findOrfail($id);
        $user_role_id = $user->role_id;
        $role = Role::find($user_role_id)->name;

        return response()->view('dashboard.users.show', ['user' => $user, 'role' => $role]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function edit(int $id): Response
    {
        $user = User::findOrfail($id);
        $roles = Role::all();

        return response()->view('dashboard.users.edit', ['user' => $user, 'roles' => $roles]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $user = User::findOrfail($id);

        $update_phone_number = 1;
        $before_phone_number = $request->post('phone_number');
        if ($user->phone_number === $before_phone_number):
            $update_phone_number = 0;
        else:
            $request->merge(['phone_number' => $request->post('phone_number') . ',' . $request->post('country_code')]);
        endif;

//        dd(
//            $request->all(),
//            $update_phone_number,
//        );

        $request->validate([
            'name' => ['required', 'string', 'max:255',
                Rule::unique('users', 'name')->ignore($user->id),
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
            ],
            'password' => ['required', 'string', 'confirmed', new Password],
//            'country_code' => ['required', 'string', 'min:1', 'max:11', new CountryCode],
//            'phone_number' => [
//                'required',
//                'string',
//                'min:10',
//                'max:30',
//                new Phone,
//                new MatchPhoneCode(),
//            ],
            'avatar_url' => ['nullable', 'image', 'mimes:jpg,bmp,png'],
            'role_id' => ['string', 'numeric', 'exists:roles,id'],
        ]);

        $old_user_name = $user->name;
        $user_name = $user->name;
        $user_email = $user->email;
        $user_phone_number = $user->phone_number;
        $user_avatar_url = $user->avatar_url;
        $user_role_id = $user->role_id;

        $request_name = $request->post('name');
        $request_email = $request->post('email');
        $request_password = $request->post('password');
        $request_phone_number = $request->post('phone_number');
        $request_role_id = $request->post('role_id') ?? $user_role_id;

        $user_role = Role::find($request_role_id)->name;

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

        $phone_number = $before_phone_number;
        if ($update_phone_number === 1):
            $spit_phone = explode(',', $request_phone_number);
            $phone_number = $spit_phone[0];

            if ($phone_number !== $user_phone_number):
                $request->validate([
                    'country_code' => ['required', 'string', 'min:1', 'max:11', new CountryCode],
                    'phone_number' => ['required', 'string', 'min:10', 'max:30',
                        new Phone,
                        new MatchPhoneCode(),
                        new UniquePhone(),
                    ],
                ]);
            endif;
        endif;

        $request_avatar_url = '';
        $request_user_name = str_replace(' ', '_', $request_name);

        if ($request->hasFile('avatar_url')):
            $file_name = $request->file('avatar_url')->getClientOriginalName();// file name with extension
            $request_file_path = $request_user_name . '/' . $file_name;

            if (Storage::disk('uploads')->exists($request_file_path) ||
                in_array($request_file_path, Storage::disk('uploads')->allFiles($request_user_name))):
                $request_avatar_url = $request_file_path;
            else:
                Storage::disk('uploads')->deleteDirectory($user_name);
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
//            $request->all(),
//            $update_phone_number,
//            $phone_number,
//        );

        $user->update([
            'name' => $request_user_name,
            'email' => $request_email,
            'password' => Hash::make($request_password),
            'phone_number' => $phone_number,
            'avatar_url' => $request_avatar_url,
            'role_id' => $request_role_id,
        ]);


        // Status for Editing the User in The System!
        $alert_status = 'alert-success';
        // Msg
        $msg = "Edit User $old_user_name Successfully.";
        // Pref
        $pref = "You Edit $old_user_name to $user_name User in The System!<br>His ID : $id ,His Email : $request_email ,His Phone number : $phone_number . User role : $user_role . ";
        $status = ['alert_status' => $alert_status, 'msg' => $msg, 'pref' => $pref];

        return redirect()->route('dashboard.users.index')->with('status', $status);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
    {
        $user = User::findOrfail($id);
        $user_id = $id;
        $show_user_name = ucwords($user->name);
        $user_email = $user->email;

        $user_name = str_replace(' ', '_', $user->name);
        $user_avatar_url = $user->avatar_url;

        $user_avatar_url_array = explode('/', $user_avatar_url);
        $base_user_avatar_url = $user_avatar_url_array[0] . '/';

        if (Storage::disk('uploads')->exists($user_avatar_url) ||
            in_array($user_avatar_url, Storage::disk('uploads')->allFiles($user_name), true)):
            Storage::disk('uploads')->deleteDirectory($base_user_avatar_url);
            Storage::disk('uploads')->delete($user_avatar_url);
        endif;

        // Status for Deleting This User from The System!
        $alert_status = 'alert-warning';
        // Msg
        $msg = "Delete User $show_user_name Successfully.";
        // Pref
        $pref = "You Delete $show_user_name User from The System!<br>His ID : $user_id ,His Email : $user_email . ";
        $status = ['alert_status' => $alert_status, 'msg' => $msg, 'pref' => $pref];

//        $user->delete();

        $user->forceDelete();

        return redirect()->route('dashboard.users.index')->with('status', $status);
    }
}
