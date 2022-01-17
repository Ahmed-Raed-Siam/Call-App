<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PhysicalActivity;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;

class PhysicalActivitiesController extends Controller
{

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $physical_activity = PhysicalActivity::all();
        return Response::json([
            'physical_activity' => $physical_activity,
        ], 200);
    }


    /**
     * @return JsonResponse
     */
    public function paginate_index(): JsonResponse
    {
        $physical_activity = PhysicalActivity::paginate(5);
        return Response::json([
            'physical_activity' => $physical_activity,
        ], 200);
    }
}
