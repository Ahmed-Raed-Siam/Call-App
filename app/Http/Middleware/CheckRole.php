<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return Response|RedirectResponse|JsonResponse
     */
    public function handle(Request $request, Closure $next, string $role = null)
    {

//        dd(
//            $request,
//            $request->header('x-api'),
//            Auth::user(),
//            $role,
//        );

        if ($role === 'user' && auth()->user()->role_id !== 1) :
            if ($request->header('x-api') === 'api'):
                return response()->json([
                    'message' => 'You Not Authorize to this page',
//                    'user' => auth()->user(),
                    'code' => '403',
                ], 403);
            endif;
            $message = 'You are admin! Please Enter to admin link';
//            redirect()->route('dashboard.admin');
//            abort(403, $message);
            abort(403);
        endif;

        if ($role === 'admin' && auth()->user()->role_id !== 2) :
            if ($request->header('x-api') === 'api'):
                return response()->json([
                    'message' => 'You Not Authorize to this page',
//                    'user' => auth()->user(),
                    'Code' => '403',
                ], 403);
            else:
                abort(403);
            endif;
//            abort(403);
        endif;
        return $next($request);
    }
}
