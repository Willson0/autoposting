<?php

namespace App\Console\Commands;


use Telegram\Bot\Commands\Command;

class StartCommand extends Command
{
    protected string $name = 'start';
    protected string $description = 'Start command for Telegram bot';

    public function handle()
    {
        $this->replyWithMessage(['text' => 'Welcome to the bot!']);
    }
}
