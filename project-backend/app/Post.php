<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{

    public $timestamps = false;
    protected $fillable = [
        'image', 'title', "description","author",'category_id',"status","created_time","status_created"
    ];
    public function category(){
        return $this->belongsTo(Category::class);
    }
    public function comments(){
        return $this->hasMany(Comment::class);
    }
}
