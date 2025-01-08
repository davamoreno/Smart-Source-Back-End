<?php

namespace App\Models;

use Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Post extends Model
{
    protected $fillable = 
    [
        'title',
        'description',
        'category_id',
        'paper_type_id',
        'user_id',         
        'slug',           
        'status',
        'approve_by',
        'approve_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime', 
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function approvedBy() {
        return $this->belongsTo(User::class, 'approve_by');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function paperType()
    {
        return $this->belongsTo(PaperType::class, 'paper_type_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function histories()
    {
        return $this->hasMany(History::class);
    }

    public function bookmarks(){
        return $this->hasMany(Bookmark::class);
    }

    public function reports(){
        return $this->hasMany(Report::class, 'post_id');
    } 

    public function file(){
        return $this->hasOne(File::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function generateSlug()
    {
        $slug = Str::slug($this->title);

        $count = Post::where('slug', 'like', $slug.'%')->count();

        if($count > 0){
            $slug .= '-' . ($count + 1);
        }

        return $slug;
    }

    protected function setTitleAttribute($value)
    {
        $this->attributes['title'] = $value;
        $this->slug = $this->generateSlug($value);
    }
}
