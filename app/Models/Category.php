<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['label', 'slug', 'banner', 'user_id', 'parent_id'];

    public function parentCategory()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function childCategories()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    // Recursive relationship for unlimited child categories
    public function descendants()
    {
        return $this->hasMany(Category::class, 'parent_id')->with('descendants');
    }

    public function ancestors()
    {
        return $this->belongsTo(Category::class, 'parent_id')->with('ancestors');
    }



    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function articles()
    {
        return $this->belongsToMany(Article::class);
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }
    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = Str::slug($value);
    }
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($category) {
            $category->slug = Str::slug($category->label);
        });
    }
}
