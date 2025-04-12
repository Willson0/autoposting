<?php

namespace App\Console\Commands;

use App\Http\Controllers\utils;
use App\Models\Post;
use App\Models\Schedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Api;

class CheckPost extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schedule:check-posts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Запускает каждую секунду проверку постов на время отправки';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting checking...');
        $bots = [];

        while (true) {
            $schedules = Schedule::where("next", "<=", Carbon::now("Europe/Moscow"))->where("status", 0)->get();
            foreach ($schedules as $schedule) {
                $post = $schedule->post;

                $user = $post->user;
                if (!isset($bots[$user->id]["groups"][$post->id])) $bots[$user->id]["groups"][$post->id] = [];
                $bots[$user->id]["groups"][$post->id] = array_merge($bots[$user->id]["groups"][$post->id], array_map(function($obj) {
                    return $obj["group_id"];
                }, $user->groups->toArray()));

                if ($post->end_count !== null) $post->update(["end_count" => $post->end_count-1]);
                if ($post->end_count === null AND $post->end_time === null) {}
                else if (($post->end_time === null || $post->end_time > Carbon::now("Europe/Moscow"))
                    && ($post->end_count === null || $post->end_count > 0)) {
                    Schedule::create([
                        "post_id" => $post->id,
                        "next" => Carbon::now("Europe/Moscow")->addMinutes($post->time_repeat),
                        "status" => 0,
                    ]);
                }

                $schedule->status = 1;
                $schedule->save();
            }

            foreach ($bots as $key => &$bot) {
                if (!isset($bot["last_send"])) $bot["last_send"] = Carbon::now()->subMinutes(60);
                if ($bot["last_send"] < Carbon::now()->subSeconds(intval(utils::getSettings()["cooldown"]))) {
                    $firstPost = array_key_first($bot["groups"]);
                    if (!empty($bot["groups"][$firstPost]) && is_array($bot["groups"][$firstPost])) {
                        $group = array_shift($bot["groups"][$firstPost]);

                        $post = Post::find($firstPost);
                        $user = User::find($key);

                        Log::critical("SEND TO {$group} BY {$user->username} WITH TEXT {$post->text}");

                        try {
                            if ($user->bot_token) utils::sendToGroupByBot($user, $group, $post->text, $post->attachment);
                            else if ($user->api_id AND $user->api_hash) utils::sendToGroupByAccount($user, $group, $post->text, $post->attachment);
                        } catch (\Exception $e) {}

                        $bot["last_send"] = Carbon::now();
                    } else {
                        unset($bot["groups"][$firstPost]);
                    }
                }
            }

            sleep (1);
        }
    }
}
