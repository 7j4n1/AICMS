<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $permission
     * @param  string  $guard
     */
    public function handle(Request $request, Closure $next, $permission, $guard = 'api'): Response
    {
        $user = auth($guard)->user();

        if (!$user || !$user->hasPermissionTo($permission, $guard)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Insufficient permissions to access this resource'
            ], 403);
        }
        
        return $next($request);
    }
}
