<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    protected $fillable = [
        'seen_at'
    ];

    public function users(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function posts(){
        return $this->belongsTo(Post::class, 'post_id');
    }
}
