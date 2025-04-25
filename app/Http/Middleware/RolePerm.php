<?php

namespace App\Http\Middleware;

use App\CustomHelper\Formatter;
use App\Models\Admin;
use App\Models\Seller;
use App\Models\User;
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
            return Formatter::apiResponse(401, "Unauthorized"); // Or handle unauthenticated users as needed
        }

        $userType = null;

        if (Admin::where('id', $currentUser->id)->where('username', $currentUser->username)->exists()) {
            $userType = 'admin';
            $request->merge(['admin' => Admin::find($currentUser->id)]);
        } elseif (User::where('id', $currentUser->id)->where('username', $currentUser->username)->exists()) {
            $userType = 'user';
            $request->merge(['user' => User::find($currentUser->id)]);
        } elseif (Seller::where('id', $currentUser->id)->where('username', $currentUser->username)->exists()) {
            $userType = 'seller';
            $request->merge(['seller' => Seller::find($currentUser->id)]);
        }

        if ($userType && in_array($userType, $roles)) {
            return $next($request);
        }

        return Formatter::apiResponse(403, "You do not have access");
    }
}
