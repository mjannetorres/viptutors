<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Illuminate\Http\Response|mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        // Check if the user is authenticated and if the user is an admin
        if (!auth()->check() || !auth()->user()->is_admin) {
            return response()->json(['message' => 'Unauthorized. You must be an admin to access this resource.'], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
