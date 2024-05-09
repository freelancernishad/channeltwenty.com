<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Advertisement;
use Illuminate\Support\Facades\Validator;


class AdvertisementController extends Controller
{
    public function index(Request $request)
    {
        $query = Advertisement::query();

        if ($request->has('page')) {
            $query->where('page', 'like', '%' . $request->input('page') . '%');
        }

        if ($request->has('url')) {
            $query->where('url', 'like', '%' . $request->input('url') . '%');
        }

        if ($request->has('banner_size')) {
            $query->where('banner_size', $request->input('banner_size'));
        }

        if ($request->has('slug')) {
            $query->where('slug', 'like', $request->input('slug') );
        }

        $advertisements = $query->inRandomOrder()->get();

        return response()->json($advertisements, 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'page' => 'required',
            'banner' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
            'banner_size' => 'required',
            'url' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }



        // $bannerName = time().'.'.$request->banner->extension();  
        // $request->banner->move(public_path('images'), $bannerName);
        // $bannerSize = $request->banner->getSize();

        if ($request->hasFile('banner')) {
            $file = $request->file('banner');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('ads/banner', $fileName, 'protected');
        } else {
            return response()->json(['error' => 'No banner file provided.'], 422);
        }


   
        $advertisement = Advertisement::create([
            'page' => $request->page,
            'banner' => url('files/'.$filePath),
            'banner_size' => $request->banner_size,
            'url' => $request->url
        ]);

        return response()->json(['advertisement' => $advertisement,  'message' => 'Advertisement created successfully.'], 201);
    }

    public function destroy($slug)
    {
        $advertisement = Advertisement::where('slug', $slug)->first();
        
        if(!$advertisement) {
            return response()->json(['message' => 'Advertisement not found'], 404);
        }

        $advertisement->delete();
        return response()->json(['message' => 'Advertisement deleted successfully'], 200);
    }
}
