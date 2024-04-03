<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BrowserInfo extends Model
{
    use HasFactory;

    protected $fillable = [
        'browser',
    ];

    public function readArticle()
    {
        return $this->belongsTo(ReadArticle::class);
    }
}
