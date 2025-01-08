<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class File extends Model
{
    protected $fillable = [
        'file_name',
        'file_path',
        'file_size',
        'file_type'
    ];

    public function post(){
        return $this->belongsTo(Post::class, 'post_id');
    }
}
