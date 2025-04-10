<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminCookie extends Model
{
    protected $guarded = false;

    public function user () {
        return $this->belongsTo(Admin::class);
    }
}
