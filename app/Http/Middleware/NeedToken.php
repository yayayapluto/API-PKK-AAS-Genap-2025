<?php

namespace App\Http\Middleware;

use App\CustomHelper\Formatter;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class NeedToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
//        dd("butuh token jir");

        if (!$request->bearerToken()) {
            return Formatter::apiResponse(403, "Token not found");
        }

        if (!Auth::guard("sanctum")->check()) {
            return Formatter::apiResponse(403, "Invalid token");
        }

        return $next($request);
    }
}
