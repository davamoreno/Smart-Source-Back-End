<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'post_id',
        'parent_id',
        'content',
    ];

    public function member(){
        return $this->belongsTo(User::class, 'member_id');
    }

    public function parent(){
        return $this->belongsTo(UserComment::class, 'parent_id');
    }

    public function post(){
        return $this->belongsTo(Post::class, 'post_id');
    }

    public function replies(){
        return $this->hasMany(UserComment::class, 'parent_id');
    }
}
