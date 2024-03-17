<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CommentController extends Controller
{
    // Get list of comments with their users and articles
    public function index()
    {
        return Comment::with('user', 'article')->get();
    }

    // Create a new comment
    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required',
            'user_id' => 'required|exists:users,id',
            'article_id' => 'required|exists:articles,id',
        ]);

        $comment = Comment::create($request->all());

        return response()->json($comment, 201);
    }

    // Update an existing comment
    public function update(Request $request, $id)
    {
        $request->validate([
            'content' => 'required',
            'user_id' => 'required|exists:users,id',
            'article_id' => 'required|exists:articles,id',
        ]);

        $comment = Comment::findOrFail($id);
        $comment->update($request->all());

        return response()->json($comment, 200);
    }

    // Delete a comment
    public function destroy($id)
    {
        $comment = Comment::findOrFail($id);
        $comment->delete();

        return response()->json(null, 204);
    }

    // Show a specific comment with its user and article
    public function show($id)
    {
        $comment = Comment::with('user', 'article')->findOrFail($id);

        return response()->json($comment, 200);
    }
}
