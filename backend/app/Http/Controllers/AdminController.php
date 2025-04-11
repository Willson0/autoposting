<?php

namespace App\Http\Controllers;

use App\Http\Requests\SettingsPostRequest;
use App\Models\Admin;
use App\Models\Payment;
use App\Models\Post;
use App\Models\Proxy;
use App\Models\Schedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class AdminController extends Controller
{
    public function login(Request $request) {
        $admin = Admin::where("login", $request->login)->first();
        if (!$admin or !password_verify($request->password, $admin->password))
            abort (403, "Неверный логин или пароль");

        $cookie = utils::gen_cookie($admin, isadmin: true);
        $respcookie = Cookie::forever("admin", $cookie);

        return response()
            ->json(["Message" => "Успешная авторизация!", "cookie" => $cookie])
            ->withCookie($respcookie);
    }

    public function profile (Request $request) {
        return $request->get("user");
    }

    public function stats (Request $request) {
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;
        $accountarr = [];

        for ($month = 1; $month <= 12; $month++) {
            $startMonth = Carbon::create($currentYear, $month, 1)->startOfMonth();
            $endMonth = Carbon::create($currentYear, $month, 1)->endOfMonth();

            $count = User::whereBetween("created_at", [$startMonth, $endMonth])->count();
            $accountarr[] = $count;
        }
        $startMonth = Carbon::create($currentYear, $currentMonth, 1)->startOfMonth();
        $money = Payment::where("created_at", ">=", Carbon::now()->subDays(30))->count();
        $money30d = Post::where("created_at", ">=", Carbon::now()->subDays(30))->count();
        $usersperday = Schedule::where("created_at", ">=", Carbon::now()->subDays(30))->count();
        $logsperday = User::where("created_at", ">=", Carbon::now()->subDays(30))->count();

        return response()->json(["accounts" => $accountarr,
            "money" => $money, "money30" => $money30d, "usersPerDay" => $usersperday,
            "logsPerDay" => $logsperday, "settings" => utils::getSettings(),
            "proxy" => Proxy::all()->toArray()
        ], 200);
    }

    public function settings (SettingsPostRequest $request) {
        $data = $request->validated();
        foreach ($data as $key => $value) utils::updateSettings($key, $value);
        return response()->json();
    }
}
