<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ArticleController extends Controller
{
     // Get list of articles with their categories
     public function index()
     {
        $articles = Article::with('categories')->get();

        $articles->transform(function ($article) {
            $article->categories = $article->formattedCategories;
            unset($article->formattedCategories);
            return $article;
        });

        return $articles;


     }

     // Get list of articles by category
     public function getByCategory($categoryId)
     {
         $articles = Article::whereHas('categories', function ($query) use ($categoryId) {
             $query->where('category_id', $categoryId);
         })->with('categories')->get();

         return $articles;
     }

       // Create a new article
       public function store(Request $request)
       {

           $validator = Validator::make($request->all(), [
            'title' => 'required',
            'content' => 'required',
            'categories' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
           // Get the authenticated user using the bearer token
           $user = auth()->user();



           if ($request->hasFile('banner')) {
            $file = $request->file('banner');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('post/banner', $fileName, 'protected');
        } else {
            return response()->json(['error' => 'No banner file provided.'], 422);
        }





           $article = Article::create([
               'title' => $request->title,
               'author' => $user->name,
               'date' => date('Y-m-d'),
               'content' => $request->content,
               'banner' => url('files/'.$filePath),
               'user_id' => $user->id, // Get the user_id from the authenticated user
           ]);

           $article->categories()->attach($request->categories);

           return response()->json($article, 201);
       }

       // Update an existing article
       public function update(Request $request, $id)
       {

           $validator = Validator::make($request->all(), [
            'title' => 'required',
            'content' => 'required',
            'categories' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
           // Get the authenticated user using the bearer token
           $user = auth()->user();

           $article = Article::findOrFail($id);
           // Check if the authenticated user owns the article
           if ($article->user_id !== $user->id) {
               return response()->json(['error' => 'Unauthorized'], 401);
           }






        $updatedData = [
            'title' => $request->title,
            'author' => $user->name,
            'content' => $request->content,

        ];

        if ($request->hasFile('banner')) {
            $file = $request->file('banner');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('post/banner', $fileName, 'protected');
            $updatedData['banner'] = url('files/'.$filePath);
        }
           $article->update($updatedData);

           $article->categories()->sync($request->categories);

           return response()->json($article, 200);
       }

     // Delete an article
     public function destroy($id)
     {
         $article = Article::findOrFail($id);
         $article->delete();

         return response()->json(null, 204);
     }

     // Show a specific article with its categories
     public function show($id)
     {
         $article = Article::with('categories')->findOrFail($id);

         return response()->json($article, 200);
     }


     function getArticlesBySlug($slug) {
        $categorySlug = $slug;
        $perPage = 15;

        $articles = Article::getByCategorySlug($categorySlug, $perPage);

        return $articles;
     }
     function getLatestarticles() {

        $latestArticles = Article::latestArticles(10);
        return $latestArticles;
     }


}
