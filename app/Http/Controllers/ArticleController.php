<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
     // Get list of articles with their categories
     public function index()
     {
         return Article::with('categories')->get();
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
           $request->validate([
               'title' => 'required',
               'author' => 'required',
               'date' => 'required|date',
               'content' => 'required',
               'categories' => 'required|array',
           ]);

           // Get the authenticated user using the bearer token
           $user = auth()->user();

           $article = Article::create([
               'title' => $request->title,
               'author' => $request->author,
               'date' => $request->date,
               'content' => $request->content,
               'user_id' => $user->id, // Get the user_id from the authenticated user
           ]);

           $article->categories()->attach($request->categories);

           return response()->json($article, 201);
       }

       // Update an existing article
       public function update(Request $request, $id)
       {
           $request->validate([
               'title' => 'required',
               'author' => 'required',
               'date' => 'required|date',
               'content' => 'required',
               'categories' => 'required|array',
           ]);

           // Get the authenticated user using the bearer token
           $user = auth()->user();

           $article = Article::findOrFail($id);
           // Check if the authenticated user owns the article
           if ($article->user_id !== $user->id) {
               return response()->json(['error' => 'Unauthorized'], 401);
           }

           $article->update([
               'title' => $request->title,
               'author' => $request->author,
               'date' => $request->date,
               'content' => $request->content,
           ]);

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
}
