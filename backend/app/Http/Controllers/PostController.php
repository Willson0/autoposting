<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostStoreRequest;
use App\Http\Requests\PostUpdateRequest;
use App\Models\Post;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function store (PostStoreRequest $request) {
        $user = $request->get("user");

        $data = $request->validated();
        $data["user_id"] = $user["id"];

        if (isset($data["date"])) {
            $data["date"] = Carbon::parse($data["date"]);
            if ($data["date"] < Carbon::now("Europe/Moscow")) abort (409, "Дата публикации не может быть раньше сегодняшней");
        }
        if (isset($data["time_repeat"])) if ($data["time_repeat"] < utils::getSettings()["time_repeat"])
            abort (409, "Время повторного поста не может быть меньше заданного.");
        if (isset($data["time_repeat"]) AND !(isset($data["end_date"]) OR isset($data["end_count"])))
            abort (409, "Укажите окончание повтора: определенную дату или кол-во раз");
        if (!isset($data["time_repeat"]) AND (isset($data["end_date"]) OR isset($data["end_count"])))
            abort (409, "Укажите кол-во повторов");
        if (isset($data["end_date"]) AND isset($data["end_count"]) )
            abort (409, "Не может быть указано два окончания повтора");

        if (isset($data["attachment"])) {
            $ext = $data["attachment"]->getClientOriginalExtension();
            $time = time();
            Storage::disk("public")->putFileAs("posts", $data["attachment"], "post_" . $time . ".$ext");
            $data["attachment"] = "posts/post_" . $time . ".$ext";
        }

        if (!$user->subscription) {
            $post = Post::create([
                "user_id" => $user["id"],
                "text" => $data["text"],
                "attachment" => isset($data["attachment"]) ? $data["attachment"] : null,
                "date" => Carbon::now("Europe/Moscow"),
            ]);

            Schedule::create([
                "post_id" => $post["id"],
                "next" => $post["date"],
                "status" => 0
            ]);

            return response()->json();
        }

        $post = Post::create($data);

        Schedule::create([
            "post_id" => $post["id"],
            "next" => $post["date"],
            "status" => 0
        ]);

        return response()->json($post, 201);
    }

    public function destroy (Post $post, Request $request) {
        $user = $request->get("user");
        if ($post->user_id !== $user["id"]) abort (404);

        if ($post->attachment) Storage::disk("public")->delete($post["attachment"]);
        $post->delete();

        return response()->json(null, 204);
    }

    public function update (Post $post, PostUpdateRequest $request) {
        $data = $request->validated();

        if (isset($data["date"])) {
            $data["date"] = Carbon::parse($data["date"]);
            if ($data["date"] < Carbon::now("Europe/Moscow")) abort (409, "Дата публикации не может быть раньше сегодняшней");
        }
        if (isset($data["end_date"])) {
            $data["end_date"] = Carbon::parse($data["end_date"]);
            if ($data["end_date"] < Carbon::now("Europe/Moscow")) abort (409, "Дата окончания не может быть раньше сегодняшней");
        }
        if (isset($data["time_repeat"])) if ($data["time_repeat"] < utils::getSettings()["time_repeat"])
            abort (409, "Время повторного поста не может быть меньше заданного.");
        if (isset($data["end_date"]) AND isset($data["end_count"]) )
            abort (409, "Не может быть указано два окончания повтора");

        if (isset($data["end_date"])) $data["end_count"] = null;
        else if (isset($data["end_count"])) $data["end_date"] = null;

        if ($request->hasAny("attachment")) {
            if ($request->hasFile('attachment')) {
                if ($post->attachment) Storage::disk("public")->delete($post["attachment"]);
                $ext = $request["attachment"]->getClientOriginalExtension(); $time = time();
                Storage::disk("public")->putFileAs("posts", $request["attachment"], "post_" . $time . ".$ext");
                $data["attachment"] = "posts/post_" . $time . ".$ext";
            }
            else if (!json_decode($request->attachment)) $data["attachment"] = null;
        }

        Log::critical($data);
        $post->update($data);

        Schedule::where("post_id", $post["id"])->where("status", 0)->delete();
        Schedule::create([
            "post_id" => $post["id"],
            "next" => $post["date"],
            "status" => 0
        ]);

        return response()->json($post, 200);
    }
}
