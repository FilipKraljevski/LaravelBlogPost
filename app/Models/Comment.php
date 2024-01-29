<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function author()
    {
        return $this->belongsTo('App\Models\User','from_user');
    }

    public function post()
    {
        return $this->belongsTo('App\Models\Post','on_post');
    }
}
