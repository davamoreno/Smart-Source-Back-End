<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class University extends Model
{
    protected $fillable = [
        'name',
        'created_by'
    ];

    public function faculities()
    {
        return $this->hasMany(Faculty::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function users()
    {
        return $this->hasManyThrough(User::class, Faculty::class);
    }
}
