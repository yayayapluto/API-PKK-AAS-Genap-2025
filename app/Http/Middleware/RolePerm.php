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

        if (in_array("admin", $roles)) {
            $admin = Admin::query()->where("id", $currentUser->id)->where("username", $currentUser->username)->first();
            if (!is_null($admin)) {
                $request->merge(["currentUser" => $admin]);
                return $next($request);
            }
        }

        if (in_array("user", $roles)) {
            $user = User::query()->where("id", $currentUser->id)->where("username", $currentUser->username)->first();
            if (!is_null($user)) {
                $request->merge(["currentUser" => $user]);
                return $next($request);
            }
        }

        if (in_array("seller", $roles)) {
            $seller = Seller::query()->where("id", $currentUser->id)->where("username", $currentUser->username)->first();
            if (!is_null($seller)) {
                $request->merge(["currentUser" => $seller]);
                return $next($request);
            }
        }

        return Formatter::apiResponse(403, "You do not have access");
    }
}
