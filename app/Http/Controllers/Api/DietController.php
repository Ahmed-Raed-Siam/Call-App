<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\Rule;

class DietController extends Controller
{

    /**
     * @return JsonResponse
     */
    public function current_diets(): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();

        $user_diets = $user->diets()->where('diets.status', '<>', 'completed')->latest()->get();

        if (count($user_diets) > 0):
            return Response::json([
                'user_diets' => $user_diets,
            ], 200);
        endif;

        return Response::json([
            'message' => "you don't have any current diets orders",
        ], 500);
    }

    /**
     * @return JsonResponse
     */
    public function completed_diets(): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();

        $user_diets = $user->diets()->where('diets.status', '=', 'completed')->latest()->get();

        if (count($user_diets) > 0):
            return Response::json([
                'user_diets' => $user_diets,
            ], 200);
        endif;

        return Response::json([
            'message' => "you don't have any completed diets orders",
        ], 500);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();
        $user_id = $user->id;

        $user_diets = $user->diets();
        $user_diets_latest = $user_diets->latest();

//        dd(
//            $user_diets->get(),
//            $user_diets_latest->first(),
//        );


        $request->validate([
            'gender' => ['required', 'string', Rule::in(['male', 'female', 'ذكر', 'أنثي', 'أنثى',])],
            /*As Ali Shaheen wish */
            'physical_activities' => ['required', 'string', 'min:3'],
//            'status' => ['required', 'int', 'exists:products,id'],
            'age' => ['required', 'int', 'min:1'],
            'weight' => ['required', 'numeric', 'min:1'],
            'height' => ['required', 'numeric', 'min:1'],
            'chronic_diseases' => ['required', 'string', 'max:255'],
            'meals_you_like' => ['required', 'string', 'max:255'],
            'meals_you_dont_like' => ['required', 'string', 'max:255'],
        ]);

        $request_gender = $request->post('gender');
        $request_physical_activities = $request->post('physical_activities');
        $request_age = $request->post('age');
        $request_weight = $request->post('weight');
        $request_height = $request->post('height');
        $request_chronic_diseases = $request->post('chronic_diseases');
        $request_meals_you_like = $request->post('meals_you_dont_like');
        $request_meals_you_dont_like = $request->post('meals_you_dont_like');

        if ($user_diets->count() === 0):

            $user_diet = $user->diets()->create([
                'user_id' => $user_id,
                'physical_activities' => $request_physical_activities,
                'gender' => $request_gender,
//            'status' => $request_,
                'age' => $request_age,
                'weight' => $request_weight,
                'height' => $request_height,
                'chronic_diseases' => $request_chronic_diseases,
                'meals_you_like' => $request_meals_you_like,
                'meals_you_dont_like' => $request_meals_you_dont_like,
            ]);

            return Response::json([
                'message' => "you order diet service! you will attached soon.",
                'user_diet' => $user_diet,
            ], 200);

        endif;

        if ($user_diets->count() > 0 && $user_diets_latest->first()->status !== 'on-cart'):
            $user_diet = $user->diets()->create([
                'user_id' => $user_id,
                'physical_activities' => $request_physical_activities,
                'gender' => $request_gender,
//            'status' => $request_,
                'age' => $request_age,
                'weight' => $request_weight,
                'height' => $request_height,
                'chronic_diseases' => $request_chronic_diseases,
                'meals_you_like' => $request_meals_you_like,
                'meals_you_dont_like' => $request_meals_you_dont_like,
            ]);

            return Response::json([
                'message' => "you order diet service! you will attached soon.",
                'user_diet' => $user_diet,
            ], 200);
        endif;

        return Response::json([
            'message' => "you have ordered diet service! you will attached soon.",
        ], 500);

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
     * @return JsonResponse
     */
    public function place_order(): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();
        $user_id = $user->id;

        $user_diets = $user->diets();
        $user_diets_latest = $user_diets->latest();


        if ($user_diets->count() > 0 && $user_diets_latest->first()->status === 'on-cart'):

            $diet = $user_diets_latest->first();
            $diet->update(['status' => 'pending-payment']);

            $message = "diet order created successfully>";
            return Response::json([
                'message' => $message,
            ], 200);

        endif;

        return Response::json([
            'message' => "you don't have any diets orders!",
        ], 500);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
