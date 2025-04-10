<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('admin_cookies', function (Blueprint $table) {
            $table->unsignedBigInteger("user_id")->after("id");
            $table->index("user_id");
            $table->foreign("user_id")->references("id")->on("admins")->onDelete("cascade");

            $table->string("cookie")->after("user_id");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admin_cookies', function (Blueprint $table) {
            $table->dropColumn(["user_id", "cookie"]);
        });
    }
};
