<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(is_null($request->bearerToken())){
            return response()->json([
                "status" => "invalid_token",
                "message" => "Invalid or expired token",
            ], 401);
        }

        if(is_null(Auth::guard("user")->user()) && is_null(Auth::guard("admin")->user())){
            return response()->json([
                "status" => "invalid_token",
                "message" => "Invalid or expired token",
            ], 401);
        }

        return $next($request);
    }
}
