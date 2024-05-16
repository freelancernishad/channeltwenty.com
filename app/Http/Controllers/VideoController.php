<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class VideoController extends Controller
{


    public function index()
    {
        $videos = Video::orderBy('id','desc')->paginate(20);
        return response()->json($videos);
    }


    public function allListByCategory(Request $request)
    {
        $categories = DB::table('videos')
        ->select('category_name')
        ->groupBy('category_name')
        ->get();

    $videos = [];
    foreach ($categories as $category) {
        $categoryVideos = Video::where('category_name', $category->category_name)
        ->orderBy('id', 'desc')
            ->take(3)
            ->get();
        $videos[] = [
            'category_name' => $category->category_name,
            'category_videos' => $categoryVideos
        ];
    }


    $latestVideos = Video::orderBy('updated_at', 'desc')
    ->take(3)
    ->get();

    $videos[] = [
    'category_name' => "latest",
    'category_videos' => $latestVideos
    ];


    // Get 3 most popular videos
    $popularVideos = Video::orderBy('views', 'desc')
                        ->take(3)
                        ->get();
    $videos[] = [
        'category_name' => "popular",
        'category_videos' => $popularVideos
    ];



        return response()->json($videos);
    }

    public function listByCategory(Request $request, $categoryname)
    {


        if ($categoryname === 'latest') {
            // Get 3 latest videos
            $videos = Video::orderBy('updated_at', 'desc')
                            ->paginate(18);
        } elseif ($categoryname === 'popular') {
            // Get 3 most popular videos
            $videos = Video::orderBy('views', 'desc')
                            ->paginate(18);
        } else {
            // Get videos for the specified category
            $videos = Video::where('category_name', $categoryname)
                            ->orderBy('id', 'desc')
                            ->paginate(18);
        }

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
    //    return $youtubeViews = $video->youtube_views;
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
