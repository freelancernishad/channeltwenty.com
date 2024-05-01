<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'slug', 'category_name', 'url'];

    public function categoryVideos()
    {
        return $this->hasMany(Video::class, 'category_name', 'category_name');
    }

}
