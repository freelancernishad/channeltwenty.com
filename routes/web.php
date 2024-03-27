<?php

use App\Models\Article;
use App\Services\DateService;
use App\Services\ContentService;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});


Route::get('/news/{slug}', function ($slug) {


    $article = Article::with('categories')->where('slug', $slug)->firstOrFail();
    $article = DateService::formatArticleDate($article);
    $article = ContentService::sortArticleContent($article);











    return view('sharedPost',compact('article'));
});

require __DIR__.'/auth.php';


Route::get('/files/{path}', function ($path) {

    // Serve the file from the protected disk
    return response()->file(Storage::disk('protected')->path($path));
})->where('path', '.*');
