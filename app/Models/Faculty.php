<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faculty extends Model
{
    protected $fillable = [
        'name'
    ];

    public function university(){
        return $this->belongsTo(University::class);
    }

    public function users(){
        return $this->hasMany(User::class);
    }
}
