<?php

namespace App\Console\Commands;

use App\Http\Controllers\utils;
use App\Models\Group;
use App\Models\Post;
use App\Models\User;
use Illuminate\Console\Command;

class TestInGroup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-in-group';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info(utils::sendToGroupByAccount(User::find(3), -1387210618, "привет, кстати, ты еще тот пидорас", null));
    }
}
