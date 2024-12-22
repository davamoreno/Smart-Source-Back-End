<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaperType extends Model
{
    protected $fillable = [
        'name',
        'created_by'
    ];
    
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
