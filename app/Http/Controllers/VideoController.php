<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VideoController extends Controller
{

    
    public function index()
    {
        $videos = Video::orderBy('id','desc')->paginate(20);
        return response()->json($videos);
    }


    public function listByCategory(Request $request, $categoryname)
    {
        $videos = Video::where('category_name',$categoryname)->orderBy('id','desc')->paginate(18);
        return response()->json($videos);
    }

  

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'category_name' => 'required|string|max:255',
            'url' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }


         


        $video = Video::create([
            'title' => $request->title,
            'slug' => \Str::slug($request->title),
            'category_name' => $request->category_name,
            'url' => extractUrlFromIframe($request->url),
        ]);

        return response()->json($video, 201);
    }

    public function show(Video $video)
    {
        return response()->json($video);
    }

    public function showBySlug(Request $request, $slug)
    {
        $video = Video::where('slug',$slug)->first();
       
        return response()->json($video);
    }

    public function update(Request $request, Video $video)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'category_name' => 'required|string|max:255',
            'url' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }


        $video->update([
            'title' => $request->title,
            'category_name' => $request->category_name,
            'url' => extractUrlFromIframe($request->url),
        ]);

        return response()->json($video, 200);
    }

    public function destroy(Video $video)
    {
        $video->delete();
        return response()->json(null, 204);
    }
}