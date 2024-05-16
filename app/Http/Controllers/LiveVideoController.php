<?php

namespace App\Http\Controllers;

use App\Models\LiveVideo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LiveVideoController extends Controller
{
    public function index()
    {
        $liveVideos = LiveVideo::all();
        return response()->json($liveVideos);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'description' => 'nullable|string',
            'video_url' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $validator = $validator->validated();

        $validator['video_url'] = extractUrlFromIframe($request->video_url);

        $liveVideo = LiveVideo::create($validator);
        return response()->json($liveVideo, 201);
    }

    public function show($id)
    {
        $liveVideo = LiveVideo::findOrFail($id);
        return response()->json($liveVideo);
    }

    public function getLastVideo()
    {
        $lastVideo = LiveVideo::latest()->first();
        return response()->json($lastVideo);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'description' => 'nullable|string',
            'video_url' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $liveVideo = LiveVideo::findOrFail($id);
        $validator = $validator->validated();
        $validator['video_url'] = extractUrlFromIframe($request->video_url);
        $liveVideo->update($validator);
        return response()->json($liveVideo, 200);
    }

    public function updateLastVideo(Request $request)
{
    $lastVideo = LiveVideo::latest()->first();

    $validator = Validator::make($request->all(), [
        'title' => 'required|string',
        'description' => 'nullable|string',
        'video_url' => 'required|string',
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()], 400);
    }

    $validator = $validator->validated();
    $validator['video_url'] = extractUrlFromIframe($request->video_url);
    $lastVideo->update($validator);
    return response()->json($lastVideo, 200);
}
    public function destroy($id)
    {
        $liveVideo = LiveVideo::findOrFail($id);
        $liveVideo->delete();
        return response()->json(null, 204);
    }
}
