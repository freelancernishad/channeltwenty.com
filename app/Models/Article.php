<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Article extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'author', 'date', 'content', 'user_id', 'banner'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function getFormattedCategoriesAttribute()
    {
        return $this->categories()->get()->map(function ($category) {
            return [
                'value' => $category->slug,
                'label' => $category->label,
                'isCategory' => true,
            ];
        });
    }



    public static function getByCategorySlug($categorySlug)
    {
        return static::whereHas('categories', function ($query) use ($categorySlug) {
            $query->where('slug', $categorySlug);
        })->get();
    }

}
