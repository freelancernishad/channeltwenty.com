<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Advertisement extends Model
{
    use HasFactory;

    protected $fillable = ['page', 'url', 'banner', 'slug', 'banner_size'];

    protected static function boot()
    {
        parent::boot();
    
        static::creating(function ($advertisement) {
            $slug = Str::slug($advertisement->page);
            $originalSlug = $slug;
            $count = 1;
    
            while (Advertisement::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $count;
                $count++;
            }
    
            $advertisement->slug = $slug;
        });
    }
}
