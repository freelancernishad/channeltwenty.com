<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VideoCategory;
use Illuminate\Support\Facades\Validator;

class VideoCategoryController extends Controller
{
    public function index()
    {
        $categories = VideoCategory::all();

        return response()->json($categories, 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:video_categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $category = VideoCategory::create($request->all());

        return response()->json($category, 201);
    }

    public function show(VideoCategory $videoCategory)
    {
        return response()->json($videoCategory, 200);
    }

    public function update(Request $request, VideoCategory $videoCategory)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:video_categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $videoCategory->update($request->all());

        return response()->json(['message' => 'Video category updated successfully'], 200);
    }

    public function destroy(VideoCategory $videoCategory)
    {
        $videoCategory->delete();

        return response()->json(['message' => 'Video category deleted successfully'], 200);
    }
}
