<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Article extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'author', 'date', 'content', 'user_id', 'banner', 'slug'];

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



    public static function getByCategorySlug($categorySlug, $perPage = 10)
    {
        return static::whereHas('categories', function ($query) use ($categorySlug) {
            $query->where('slug', $categorySlug);
        })->orderBy('id','desc')->paginate($perPage);
    }


    public static function getByDate($date, $perPage = 10)
    {
        return static::whereDate('date', $date)
                     ->orderBy('id', 'desc')
                     ->paginate($perPage);
    }



    public static function latestArticles($limit = 10)
    {
        return static::latest()->take($limit)->orderBy('id','desc')->get();
    }


    public function relatedArticlesByArticleSlug($articleSlug, $limit = 5)
    {
        // Find the article by slug or fail if not found
        $article = static::where('slug', $articleSlug)->firstOrFail();

        // Get related articles based on shared categories, excluding the current article
        $relatedArticles = static::whereHas('categories', function ($query) use ($article) {
                $query->whereIn('categories.id', $article->categories()->pluck('categories.id')); // Specify the table name or alias for the id column
            })
            ->where('articles.id', '!=', $article->id) // Specify the table name or alias for the id column
            ->latest()
            ->take($limit)
            ->get();



        return $relatedArticles;
    }


    public function setSlugAttribute($value)
    {
        $originalSlug = $slug = Str::slug($value);

        // Check if the slug already exists in the database
        $count = Article::where('slug', $slug)->where('id', '<>', $this->id)->count();

        $i = 1;
        while ($count > 0) {
            $slug = $originalSlug . '-' . $i; // Append a number to make the slug unique
            $count = Article::where('slug', $slug)->where('id', '<>', $this->id)->count();
            $i++;
        }

        $this->attributes['slug'] = $slug;
    }


    public static function getByAuthorName($authorName, $perPage = 10)
{
    // Find users whose names partially match the provided author name
    $users = User::where('name', 'like', '%' . $authorName . '%')->get();

    if ($users->isNotEmpty()) {
        // Extract user IDs from the collection of users
        $userIds = $users->pluck('id')->toArray();

        // Filter articles by user IDs
        return static::whereIn('user_id', $userIds)
                     ->orderBy('id', 'desc')
                     ->paginate($perPage);
    } else {
        // If no matching users found, return an empty result
        return static::where('author', $authorName)
        ->orderBy('id', 'desc')
        ->paginate($perPage);
    }
}


}
