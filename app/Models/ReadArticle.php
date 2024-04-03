<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReadArticle extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'article_id',
        'browser',
        'ip_address',
        'location',
        'mac_address',
        'date',
        'month',
        'year',
        'unique_reader_id',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    public function browserInfo()
    {
        return $this->hasOne(BrowserInfo::class);
    }

    public function userDetails()
    {
        return $this->hasOne(UserDetails::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($readArticle) {
            // Create BrowserInfo
            $readArticle->browserInfo()->create([
                'browser' => $readArticle->browser,
            ]);

            // Create UserDetails
            $readArticle->userDetails()->create([
                'location' => $readArticle->location,
                'ip_address' => $readArticle->ip_address,
                'mac_address' => $readArticle->mac_address,
            ]);
        });
    }


}
