<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WebsiteInfo;
use App\Rules\CountryCode;
use App\Rules\MatchPhoneCode;
use App\Rules\Phone;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class WebsiteInfoController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function store(Request $request)
    {
        $website_info_model = WebsiteInfo::all();
        $count_website_info = WebsiteInfo::all()->count();
        if ($count_website_info === 0):
            $request->merge(['phone_number' => $request->post('phone_number') . ',' . $request->post('country_code')]);

            $request->validate([
                'logo' => ['nullable', 'image', 'mimes:jpg,bmp,png'],
                'name' => ['required', 'string', 'max:255'],
                'country_code' => ['required', 'string', 'min:1', 'max:11', new CountryCode],
                'phone_number' => [
                    'required',
                    'string',
                    'min:10',
                    'max:30',
                    new Phone(),
                    new MatchPhoneCode(),
                ],
            ]);

            $request_website_name = $request->post('name');

            $website_logo = '';
            $request_new_website_name = str_replace(' ', '_', $request_website_name);

            if ($request->hasFile('logo')):
                $file_name = $request->file('logo')->getClientOriginalName();// file name with extension
                $request_file_path = $request_new_website_name . '/' . $file_name;

                if (Storage::disk('uploads')->exists($request_file_path) ||
                    in_array($request_file_path, Storage::disk('uploads')->allFiles($request_new_website_name), true)):
                    $website_logo = $request_file_path;
                else:
                    $website_logo = $request->file('logo')->storeAs(
                        $request_new_website_name, $file_name,
                        [
                            'disk' => 'uploads',
                        ],
                    );
                endif;
            endif;

            $spit_phone = explode(',', $request->post('phone_number'));
            $phone_number = $spit_phone[0];

            $website_info = WebsiteInfo::create([
                'logo' => $website_logo,
                'name' => $request_website_name,
                'phone_number' => $phone_number,
            ]);

            $message = "New website info added to $website_info->name successfully>";
            return Response::json([
                'message' => $message,
                'website_info' => $website_info,
            ], 200);

        endif;

        $url = route('api.website_info.show');
        $message = "website info already exists! go to this $url";
        return Response::json([
            'message' => $message,
            'website_info' => $website_info_model->first(),
        ], 200);

    }


    /**
     * @return JsonResponse
     */
    public function get_website_info(): JsonResponse
    {
        $website_info = WebsiteInfo::first();

//        dd($website_info);

        return Response::json([
            'website_info' => $website_info,
        ], 200);

    }

}
