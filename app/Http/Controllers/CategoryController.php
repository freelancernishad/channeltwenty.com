<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
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

        $validator = Validator::make($request->all(), [
            'label' => 'nullable',
            'slug' => 'required|unique:categories',
            'user_id' => 'required|exists:users,id',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }


        if ($request->hasFile('banner')) {
            $file = $request->file('banner');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('category/banner', $fileName, 'protected');
        } else {
            return response()->json(['error' => 'No banner file provided.'], 422);
        }
        $requestdata  = $request->all();
        $requestdata['banner'] = url('files/'.$filePath);

        $category = Category::create($requestdata);

        return response()->json($category, 201);
    }

    // Update an existing category
    public function update(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'label' => 'nullable',
            'slug' => 'required|unique:categories,slug,' . $id,
            'user_id' => 'required|exists:users,id',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
        $category = Category::findOrFail($id);



        if ($request->hasFile('banner')) {
            $file = $request->file('banner');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('category/banner', $fileName, 'protected');
        } else {
            return response()->json(['error' => 'No banner file provided.'], 422);
        }
        $requestdata  = $request->all();
        $requestdata['banner'] = url('files/'.$filePath);
        $category->update($requestdata);

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
