<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Google\Client as GoogleClient;

class Video extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'slug', 'category_name', 'url','views'];

    public function categoryVideos()
    {
        return $this->hasMany(Video::class, 'category_name', 'category_name');
    }

    public function getYoutubeViewsAttribute()
    {
        if (Str::contains($this->url, 'youtube.com')) {


            $videoId = $this->getYoutubeVideoId("jQEJtMITIRQ");
            if ($videoId) {
                return $this->getYouTubeViews($videoId);
            }
        }

        return null;
    }



    public function getYouTubeViews($videoId)
    {
        $client = new GoogleClient();
        $client->setDeveloperKey(env('YOUTUBE_API_KEY'));
        $service = new \Google\Service\YouTube($client);

        try {
            $videoResponse = $service->videos->listVideos('statistics', array('id' => $videoId));
            $video = $videoResponse[0];

            $views = $video->getStatistics()->getViewCount();
            $this->update(['views' => $views]);

            return $views;
        } catch (\Google\Service\Exception $e) {
            return null;
        }
    }



    private function getYoutubeVideoId($url)
    {
        $videoId = '';
        $parts = parse_url($url);
        if (isset($parts['query'])) {
            parse_str($parts['query'], $query);
            if (isset($query['v'])) {
                $videoId = $query['v'];
            }
        } elseif (isset($parts['path'])) {
            $path = explode('/', $parts['path']);
            $videoId = end($path);
        }

        return $videoId;
    }

    // private function getYouTubeViews($videoId)
    // {
    //     $client = app(GoogleClient::class);
    //     $service = new \Google\Service\YouTube($client);

    //     try {
    //         $videoResponse = $service->videos->listVideos('statistics', array('id' => $videoId));
    //         $video = $videoResponse[0];

    //         return $video->getStatistics()->getViewCount();
    //     } catch (\Google\Service\Exception $e) {
    //         return null;
    //     }
    // }


}
