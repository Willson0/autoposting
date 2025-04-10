<?php

namespace App\Http\Middleware;

use App\Models\CookieUser;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

class CheckAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $cookieparam = Cookie::get("login");
        if (!$cookieparam) abort (401, "Cookie отсутствуют.");

        $cookie = CookieUser::where("cookie", $cookieparam)->first();
        if (!$cookie) abort (401, "Cookie не найдены в базе данных.");

        $user = $cookie->user;
        if (!$user) abort (401, "По данным Cookie ни один пользователь не найден");

        $request->attributes->add(["user" => $user]);
        return $next($request);
    }
}
