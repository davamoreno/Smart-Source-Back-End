<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'reason',
        'status',
        'handled_at'
    ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function post(){
        return $this->belongsTo(Post::class, 'post_id');
    }
}
