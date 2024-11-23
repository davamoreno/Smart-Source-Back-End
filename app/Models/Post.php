<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Post extends Model
{
    protected $fillable = 
    [
        'title',
        'description',
        'file_path',
        'file_name',
        'file_size',
        'category_id',
        'paper_type_id'
    ];

    protected $casts = [
        'approved_at' => 'datetime', 
    ];


    public function superAdmin(){
        return $this->belongsTo(User::class, 'super_admin_id');
    }

    public function admin(){
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function member(){
        return $this->belongsTo(User::class, 'member_id');
    }

    public function category(){
        return $this->belongsTo(PostCategory::class, 'category_id');
    }

    public function paperType(){
        return $this->belongsTo(PostPaperType::class, 'paper_type_id');
    }

    public function comment(){
        return $this->hasMany(UserComment::class);
    }

    public function generateSlug(){
        $slug = Str::slug($this->title);

        $count = Post::where('slug', $slug)->count();

        if($count > 0){
            $slug .= '.' . ($count + 1);
        }

        return $slug;
    }
}
