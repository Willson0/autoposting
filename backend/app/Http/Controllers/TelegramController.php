<?php

namespace App\Http\Controllers;

use App\Console\Commands\StartCommand;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Api;

class TelegramController extends Controller
{
    public function handle (Request $request) {
        Log::critical("Handle");
        $telegram = new Api(env("TELEGRAM_BOT_TOKEN"));

        $telegram->addCommands([
            StartCommand::class,
        ]);

        $update = $telegram->commandsHandler(true);

        if ($update->getMessage() && $update->getMessage()->getContact()) {
            $chatId = $update->getMessage()->getChat()->getId();
            $phoneNumber = $update->getMessage()->getContact()->getPhoneNumber();

            Log::info("Received phone number from $chatId: $phoneNumber");

            if (!User::where("telegram_id", $chatId)->exists()) return response()->json(['status' => 'ok'], 200);
            User::where("telegram_id", $chatId)->update(['phone' => $phoneNumber]);

            $telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => "Спасибо! Ваш номер успешно добавлен.",
            ]);
        } else {
            $chatId = $update->getMessage()->getChat()->getId();
            $text = $update->getMessage()->getText();

            Log::info("Received message from $chatId: $text");
        }

        return response()->json(['status' => 'ok'], 200);
    }
}
