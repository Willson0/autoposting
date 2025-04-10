<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $guarded = false;

    public function post () {
        return $this->belongsTo(Post::class);
    }
}
