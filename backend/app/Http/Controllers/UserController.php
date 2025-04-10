<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function test (Request $request) {
        $user = $request->get("user");

        utils::sendToGroupByAccount($user, $request->group, $request->text, "posts/photo_2025-03-10_22-27-06.jpg");
    }
}
