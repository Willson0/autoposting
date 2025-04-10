<?php

namespace App\Http\Controllers;

use App\Http\Requests\GroupStoreRequest;
use App\Models\Group;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function store (GroupStoreRequest $request) {
        $user = $request->get("user");
        $data = $request->validated();

        if (Group::where("user_id", $user->id)->where("group_id", $data["group"])->exists())
            abort (409, "Запись уже существует");

        $group = Group::create([
            "user_id" => $user->id,
            "group_id" => $data["group"],
        ]);

        return response()->json($group);
    }

    public function destroy (Group $group, Request $request) {
        $user = $request->get("user");
        if ($group->user_id !== $user->id) abort (401);

        $group->delete();

        return response()->json(["success" => true]);
    }
}
