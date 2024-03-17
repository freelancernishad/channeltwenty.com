<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // Get list of categories with their parent categories
    public function index()
    {
        return Category::with('parent')->get();
    }

    // Create a new category
    public function store(Request $request)
    {
        $request->validate([
            'label' => 'nullable',
            'slug' => 'required|unique:categories',
            'banner' => 'nullable',
            'user_id' => 'required|exists:users,id',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        $category = Category::create($request->all());

        return response()->json($category, 201);
    }

    // Update an existing category
    public function update(Request $request, $id)
    {
        $request->validate([
            'label' => 'nullable',
            'slug' => 'required|unique:categories,slug,' . $id,
            'banner' => 'nullable',
            'user_id' => 'required|exists:users,id',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        $category = Category::findOrFail($id);
        $category->update($request->all());

        return response()->json($category, 200);
    }

    // Delete a category
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return response()->json(null, 204);
    }

    // Show a specific category with its parent category
    public function show($id)
    {
        $category = Category::with('parent')->findOrFail($id);

        return response()->json($category, 200);
    }

    // Get list of subcategories for a specific category
    public function getSubcategories($id)
    {
        $subcategories = Category::where('parent_id', $id)->get();

        return response()->json($subcategories, 200);
    }
}
