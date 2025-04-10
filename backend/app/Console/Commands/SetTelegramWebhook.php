<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Api;

class SetTelegramWebhook extends Command
{
    protected $signature = 'telegram:set-webhook {url?}';
    protected $description = 'Set Telegram bot webhook';

    public function handle()
    {
        $telegram = new Api(env("TELEGRAM_BOT_TOKEN"));
        $url = $this->argument('url') ?? config('app.url') . '/api/webhook/telegram';

        Log::critical($url);
        $response = $telegram->setWebhook(['url' => $url]);

        if ($response) {
            $this->info("Webhook set successfully to: $url");
        } else {
            $this->error('Failed to set webhook');
        }
    }
}
