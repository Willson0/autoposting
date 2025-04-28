<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthSettingsRequest;
use App\Models\CookieUser;
use App\Models\Post;
use App\Models\User;
use Carbon\Carbon;
use danog\MadelineProto\Settings;
use danog\MadelineProto\Settings\AppInfo;
use danog\MadelineProto\SettingsAbstract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Api;
use Telegram\Bot\Keyboard\Keyboard;

class AuthController extends Controller
{
    public function login (Request $request) {
        $data = $request->all();
        $hash = $data["hash"];
        unset($data["hash"]);

        ksort($data);
        $dataString = collect($data)->map(function ($value, $key) {
            return "$key=$value";
        })->implode("\n");

        $secretKey = hash('sha256', env("TELEGRAM_BOT_TOKEN"), true);

        $calcHash = hash_hmac('sha256', $dataString, $secretKey);

        if (!hash_equals($calcHash, $hash))
            return response()->json(["message" => "Недействительные данные"], 403);

        Log::critical($data);
        $user = User::where("telegram_id", $data["id"])->first();
        if (!$user) {
            $user = User::create([
                "telegram_id" => $data["id"],
                "username" => $data["username"] ?? $data["first_name"],
            ]);
        }

        $cookie = utils::gen_cookie($user);
        $respcookie = Cookie::forever("login", $cookie);

        return response()->json(["message" => "Успешная авторизация", "cookie" => $cookie])->withCookie($respcookie);
    }

    public function test (Request $request) {
        $user = User::find(2);

        $cookie = utils::gen_cookie($user);
        $respcookie = Cookie::forever("login", $cookie);

        return response()->json(["message" => "Успешная авторизация", "cookie" => $cookie])->withCookie($respcookie);
    }

    public function profile (Request $request) {
        $user = $request->get("user");
        $user->groups;
        $user->notifications;

        $path = public_path("sessions/session_" . $user->id . '.session');
        if (file_exists($path)) $user["session"] = true;
        else $user["session"] = false;

        $user["posts"] = Post::where("user_id", $user->id)
            ->whereNotNull('date')
            ->where(function ($query) {
                $query->where('date', '>', Carbon::now("Europe/Moscow"))
                ->orWhere(function ($subQuery) {
                    $subQuery->whereNotNull('end_count')
                        ->orWhereNotNull('end_date');
                });
            })
            ->where(function ($query) {
                $query->whereNull("end_count")
                    ->orWhere("end_count", "!=", 0);
            })
            ->where(function ($query) {
                $query->whereNull("end_date")
                    ->orWhere("end_date", ">", Carbon::now("Europe/Moscow"));
            })->get();

        $user["time_repeat"] = intval(utils::getSettings()["time_repeat"]);

        return $user;
    }

    public function logout () {
        $cookie = Cookie::get("login");
        CookieUser::where("cookie", $cookie)->delete();

        $respcookie = Cookie::forget("login");

        return response()->json(["message" => "Успешный выход из аккаунта!"])->withCookie($respcookie);
    }

    public function settings (AuthSettingsRequest $request) {
        $user = $request->get("user");
        $data = $request->validated();

        $user->update($data);
        return response()->json($user);
    }

    public function phone (Request $request) {
        $user = $request->get("user");
        $user->phone = $request->phone;
        $user->save();

        $settings = (new \danog\MadelineProto\Settings)
            ->setAppInfo((new AppInfo)->setApiId(intval($user->api_id))->setApiHash($user->api_hash))
            ->setIpc((new Settings\Ipc()));

        $MadelineProto = new \danog\MadelineProto\API("sessions/session_" . $user->id . '.session', $settings);
        $MadelineProto->phoneLogin($user->phone);

        return response()->json();
    }

    public function checkCode(Request $request) {
        $user = $request->get("user");
        $settings = (new \danog\MadelineProto\Settings)
            ->setAppInfo((new AppInfo)->setApiId($user->api_id)->setApiHash($user->api_hash))
            ->setIpc((new Settings\Ipc()));

        $MadelineProto = new \danog\MadelineProto\API("sessions/session_" . $user->id . '.session', $settings);
        $response = $MadelineProto->completePhoneLogin($request->code);

        Log::critical($response);
        if ($response["_"] !== "account.password") {
            $self = $MadelineProto->getSelf();
            if (!$self) {
                Log::error('Не удалось получить информацию о себе. Вероятно, сессия не авторизована.');
                abort(401, "Ошибка авторизации");
            }
            return response()->json(["next" => "end"]);
        }

        return response()->json(["next" => $response["_"]]);
    }

    public function checkPassword(Request $request)
    {
        $user = $request->get("user");
        $settings = (new \danog\MadelineProto\Settings)
            ->setAppInfo((new AppInfo)->setApiId($user->api_id)->setApiHash($user->api_hash))
            ->setIpc((new Settings\Ipc()));

        $MadelineProto = new \danog\MadelineProto\API("sessions/session_" . $user->id . '.session', $settings);
        $response = $MadelineProto->complete2falogin($request->password);

        $self = $MadelineProto->getSelf();
        if (!$self) {
            Log::error('Не удалось получить информацию о себе. Вероятно, сессия не авторизована.');
            return response()->json(['status' => 'error', 'message' => 'Не авторизован']);
        }
        if ($response["_"] === "auth.authorization") return response()->json(["next" => "end"]);

        abort(400);
    }

    public function destroySession (Request $request) {
        $user = $request->get("user");
        $path = public_path("sessions/session_" . $user->id . '.session');

        if (file_exists($path)) {
            utils::deleteDirRecursive($path);
        } else abort (404);

//        $settings = (new \danog\MadelineProto\Settings)
//            ->setAppInfo((new AppInfo)->setApiId($user->api_id)->setApiHash($user->api_hash))
//            ->setIpc((new Settings\Ipc()));
//
//        $MadelineProto = new \danog\MadelineProto\API("sessions/session_" . $user->id . '.session', $settings);
//
//        try {
//            $me = $MadelineProto->getSelf();
//
//            if ($me) utils::deleteDirRecursive("sessions/session_" . $user->id . '.session');
//            else abort (404);
//        } catch (\danog\MadelineProto\Exception $e) {
//            abort (404);
//        }

        return response()->json(["status" => "ok"]);
    }
}
