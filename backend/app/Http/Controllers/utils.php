<?php

namespace App\Http\Controllers;

use App\Models\AdminCookie;
use App\Models\CookieUser;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\Proxy;
use danog\MadelineProto\LocalFile;
use danog\MadelineProto\ParseMode;
use danog\MadelineProto\Settings\AppInfo;
use danog\MadelineProto\Settings\Connection;
use danog\MadelineProto\Settings\Ipc;
use danog\MadelineProto\Settings\Peer;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Telegram\Bot\Api;

class utils
{
    static function gen_cookie ($user, $isadmin = false) {
        if ($isadmin) $cookieclass = AdminCookie::class;
        else $cookieclass = CookieUser::class;

        do $cookie = self::gen_str(32);
        while ($cookieclass::where("cookie", $cookie)->exists());

        $cookieclass::create([
            "user_id" => $user->id,
            "cookie" => $cookie
        ]);
        return $cookie;
    }

    static public function gen_str ($length) {
        $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';
        $random_string = '';
        for($i = 0; $i < $length; $i++) {
            $random_character = $permitted_chars[mt_rand(0, strlen($permitted_chars) - 1)];
            $random_string .= $random_character;
        }
        return $random_string;
    }

    public static function getSettings() {
        return json_decode(file_get_contents(storage_path('app/settings.json')), true);
    }

    public static function updateSettings($param, $arg) {
        $settings = self::getSettings();
        $settings[$param] = $arg;
        file_put_contents(storage_path('app/settings.json'), json_encode($settings));
        return true;
    }

    public static function sendToGroupByBot ($user, $group, $text, $photo = null) {
        if (!$user->bot_token) return 0;
        while (true) {
            try {
                $proxy = Proxy::inRandomOrder()->first();
                if ($proxy) $options = [
                    'proxy' => self::proxyToString($proxy->ip, $proxy->port, $proxy->type, $proxy->username, $proxy->password),
                    'timeout' => 10,    // Тайм-аут
                ]; else $options = [];

                if ($photo)
                    $response = Http::withOptions($options)
                        ->attach('photo', Storage::disk("public")->get($photo), 'photo.jpg') // передаем сам файл
                        ->post('https://api.telegram.org/bot' . $user->bot_token . '/sendPhoto', [
                            'chat_id' => -$group,
                            'caption' => $text,
                            "parse_mode" => "HTML"
                        ]);
//                    $response = Http::withOptions($options)
//                        ->get('https://api.telegram.org/bot' . $user->bot_token . '/sendPhoto?chat_id=' . (-$group)
//                            . '&caption=' . $text . "&photo=" . ("https://" . env("DOMAIN") . "/storage/" . $photo));
                else $response = Http::withOptions($options)
                    ->get('https://api.telegram.org/bot' . $user->bot_token . '/sendMessage?chat_id=' . (-$group) . '&text=' . $text . "&parse_mode=HTML");
                Log::critical($response->json());

                if (!$response->ok()) Notification::create([
                    "user_id" => $user->id,
                    "status" => "error",
                    "text" => "Бот не смог получить доступ к группе с ID: $group"
                ]); else Notification::create([
                    "user_id" => $user->id,
                    "status" => "success",
                    "text" => "Бот успешно отправил сообщение в группу " . $group
                ]);
                break;
            } catch (Exception $e) {
                Log::critical($e->getMessage());
            }
        }

        return 1;
    }

    public static function escapeMarkdownV2($text) {
        $escape = ['_', '*', '[', ']', '(', ')', '~', '`', '>', '#', '+', '-', '=', '|', '{', '}', '.', '!', '\\'];
        foreach ($escape as $char) {
            $text = str_replace($char, '\\' . $char, $text);
        }
        return $text;
    }


    public static function sendToGroupByAccount ($user, $group, $text, $photo = null) {
        if (!$user->api_id or !$user->api_hash) return 0;

        $settings = (new \danog\MadelineProto\Settings)
            ->setAppInfo((new AppInfo)->setApiId($user->api_id)->setApiHash($user->api_hash))
            ->setIpc((new Ipc()))
            ->setPeer((new Peer)->setFullFetch(true));

        try {
            $proxy = Proxy::inRandomOrder()->first();
            if ($proxy)
                $settings->setConnection((new Connection)
                    ->addProxy(self::proxyToString($proxy->ip, $proxy->port, $proxy->type, $proxy->username, $proxy->password)));

            $MadelineProto = new \danog\MadelineProto\API("public/sessions/session_" . $user->id .  '.session' , $settings);
            $dialogs = $MadelineProto->getFullDialogs();

            if (!$MadelineProto->getSelf()) return "Не авторизован";


            if ($photo) $MadelineProto->sendPhoto(peer: -$group, file: new LocalFile(storage_path("app/public/" . $photo)), caption: $text, parseMode: ParseMode::HTML);
            else $MadelineProto->messages->sendMessage(peer: -$group, message: $text, parse_mode: ParseMode::HTML);

            Notification::create([
                "user_id" => $user->id,
                "status" => "success",
                "text" => "Бот успешно отправил сообщение в группу " . $group
            ]);
        } catch (Exception $e) {
            Log::critical($e->getMessage());
            Notification::create([
                "user_id" => $user->id,
                "status" => "error",
                "text" => "Бот не смог получить доступ к группе с ID: $group"
            ]);
        }

        return 1;
    }

    public static function checkProxy ($ip, $port, $type, $username, $password) {
        if (Proxy::where("ip", $ip)->where("port", $port)->exists()) abort (409, "Уже существует");

        $proxy = self::proxyToString($ip, $port, $type, $username, $password);

        $response = Http::withOptions([
            'proxy' => $proxy,
            'timeout' => 10,
        ])->get('https://httpbin.org/ip');

        if ($response->successful()) {
            return 1;
        }

        abort (409, "Непредвиденная ошибка");
    }

    public static function proxyToString ($ip, $port, $type, $username = null, $password = null) {
        $proxy = "{$type}://{$ip}:{$port}";
        if ($username && $password) $proxy = "{$type}://{$username}:{$password}@{$ip}:{$port}";

        return $proxy;
    }

    public static function createPayment ($sum, $user) {
//        $payment = Payment::create([
//            "user_id" => $user->id,
//            "sum" => $sum,
//        ]);
//
//        $data = [
//            "wallet_to" => env("LAVA_API_WALLET"),
//            "sum" => sprintf("%.2f", $sum),
//            "order_id" => $payment->id,
//            "success_url" => "https://" . env("DOMAIN") . "/webhook/lava/success",
//            "fail_url" => "https://" . env("DOMAIN") . "/webhook/lava/fail",
//        ];
//
//        $response = Http::withHeader("Authorization", env("LAVA_API_KEY"))
//            ->post("https://api.lava.ru/withdraw/info", $data);

        $jwt = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1aWQiOiJhZjk3MWFiOS04NjQ3LTQzZDgtYjg0ZC1jYjU1M2U2OWE3ODIiLCJ0aWQiOiI3NTE4ZDZhZi03MjFhLTAzNmMtYWY5ZC1iMDUzMGU1MDQ3YmMifQ.MeU8iMW33wTFXDg3oBzxKO1jpQB8RWZYsbUSbD-7aFsИ";

        $url = "https://api.lava.ru/invoice/create";

        $data = [
            'walexport const_to' => 'R40510054',
            'sum'      => 10.00,
            'order_id' => 'order_221',
            'hook_url' => 'https://lava.ru/hook',
            'success_url' => 'https://lava.ru/success',
            'fail_url' => 'https://lava.ru/fail',
            'expire' => 300,
            'subtract' => '0',
            'merchant_id' => '123',
            'merchant_name' => 'lava.ru'
        ];

        $headers = [
            "Authorization: ".$jwt,
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = json_decode(curl_exec($ch),true);
        curl_close($ch);

        Log::critical($response);
        return 1;
    }
}
