<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProxyController;
use App\Http\Controllers\TelegramController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\CheckAdminMiddleware;
use App\Http\Middleware\CheckAuthMiddleware;
use Illuminate\Support\Facades\Route;

Route::group(["prefix" => "api"], function () {
    Route::group(["prefix" => "auth"], function () {
        Route::post("login", [AuthController::class, "login"]);
        Route::post("login/test", [AuthController::class, "test"]);
        Route::group(["middleware" => CheckAuthMiddleware::class], function () {
            Route::post("profile", [AuthController::class, "profile"]);
            Route::post("logout", [AuthController::class, "logout"]);
            Route::post("settings", [AuthController::class, "settings"]);
            Route::post("phone", [AuthController::class, "phone"]);
            Route::post("code", [AuthController::class, "checkCode"]);
            Route::post("password", [AuthController::class, "checkPassword"]);
        });
    });

    Route::group(["prefix" => "group", "middleware" => CheckAuthMiddleware::class], function () {
        Route::post("/", [GroupController::class, "store"]);
        Route::delete("/{group}", [GroupController::class, "destroy"]);
    });

    Route::group(["prefix" => "post", "middleware" => CheckAuthMiddleware::class], function () {
        Route::post("/", [PostController::class, "store"]);
        Route::delete("/{post}", [PostController::class, "destroy"]);
        Route::post("/{post}", [PostController::class, "update"]);
    });

    Route::group(["prefix" => "webhook"], function () {
        Route::post("telegram", [TelegramController::class, "handle"]);
        Route::group(["prefix" => "lava"], function () {
            Route::post("/success", [PaymentCOntroller::class, "success"]);
            Route::post("/fail", [PaymentController::class, "fail"]);
        });
    });

    Route::group(["prefix" => "proxy", "middleware" => CheckAdminMiddleware::class], function () {
        Route::post("/", [ProxyController::class, "store"]);
        Route::delete("/{proxy}", [ProxyController::class, "destroy"]);
        Route::post("/{proxy}", [ProxyController::class, "update"]);
        Route::get("/", [ProxyController::class, "index"]);
    });

    Route::post("/admin/login", [AdminController::class, "login"]);
    Route::group(["prefix" => "admin", "middleware" => CheckAdminMiddleware::class], function () {
        Route::get("/profile", [AdminController::class, "profile"]);
        Route::get("/stats", [AdminController::class, "stats"]);
        Route::post("/settings", [AdminController::class, "settings"]);
    });

    Route::post("/test", [UserController::class, "test"])->middleware(CheckAuthMiddleware::class);
});
