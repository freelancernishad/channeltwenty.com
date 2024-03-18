<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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

        $validator = Validator::make($request->all(), [
            'content' => 'required',
            'user_id' => 'required|exists:users,id',
            'article_id' => 'required|exists:articles,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $comment = Comment::create($request->all());

        return response()->json($comment, 201);
    }

    // Update an existing comment
    public function update(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'content' => 'required',
            'user_id' => 'required|exists:users,id',
            'article_id' => 'required|exists:articles,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

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
