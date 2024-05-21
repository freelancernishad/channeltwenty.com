<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SelectedArticle extends Model
{
    use HasFactory;


    protected $fillable = ['article_id', 'date'];

    public function article()
    {
        return $this->belongsTo(Article::class);
    }
}
