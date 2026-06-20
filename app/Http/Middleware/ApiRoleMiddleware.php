<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiRoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user('api');

        if (! $user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Token API tidak valid atau tidak disertakan.',
            ], 401);
        }

        if (! in_array($user->role, $roles, true)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Forbidden. Role tidak memiliki akses ke endpoint ini.',
            ], 403);
        }

        return $next($request);
    }
}
