<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $fillable = [
        'file_name',
        'file_path',
        'file_size'
    ];

    public function post(){
        return $this->belongsTo(Post::class, 'post_id');
    }
}
