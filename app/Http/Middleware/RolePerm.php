<?php

namespace App\Http\Middleware;

use App\CustomHelper\Formatter;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RolePerm
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $currentUser = Auth::guard("sanctum")->user();

        if (!$currentUser) {
            return Formatter::apiResponse(401, "Unauthorized");
        }

        $userType = $currentUser->role;

        if ($userType && in_array($userType, $roles)) {
            $request->merge([$userType => $currentUser]);
            return $next($request);
        }

        return Formatter::apiResponse(403, "You do not have access");
    }
}
