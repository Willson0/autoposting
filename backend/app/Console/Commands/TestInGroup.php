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
        $post = Post::find(5);
        $user = User::find(2);
        $group = Group::find(2)->group_id;
        $this->info(utils::sendToGroupByAccount($user, $group, $post->text, $post->attachment));
//        $this->info(utils::createPayment(10, User::find(2)));
    }
}
