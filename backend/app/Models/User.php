<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $guarded = false;

    public function groups () {
        return $this->hasMany(Group::class);
    }

    public function posts () {
        return $this->hasMany(Post::class);
    }

    public function notifications () {
        return $this->hasMany(Notification::class);
    }
}
