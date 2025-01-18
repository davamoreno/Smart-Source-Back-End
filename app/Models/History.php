<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'user_id',
        'post_id',
        'seen_at'
    ];

    public function users(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function posts(){
        return $this->belongsTo(Post::class, 'post_id');
    }
}
