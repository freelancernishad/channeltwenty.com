<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VideoCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'parent_id'];

    public function parent()
    {
        return $this->belongsTo(VideoCategory::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(VideoCategory::class, 'parent_id');
    }

    public static function boot()
    {
        parent::boot();

        self::creating(function($model) {
            $slug = Str::slug($model->name);
            $count = VideoCategory::where('slug', 'like', "{$slug}%")->count();

            if ($count > 0) {
                $model->slug = $slug . '-' . ($count + 1);
            } else {
                $model->slug = $slug;
            }
        });
    }
}
