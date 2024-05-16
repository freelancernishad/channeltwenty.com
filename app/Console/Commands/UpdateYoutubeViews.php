<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Video;
use Illuminate\Support\Facades\Log;

class UpdateYoutubeViews extends Command
{
    protected $signature = 'youtube:views:update';
    protected $description = 'Update YouTube views for all videos';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Log::info('YouTube views update command executed.');

        $videos = Video::all();
        foreach ($videos as $video) {
            if (strpos($video->url, 'youtube.com') !== false) {
                $videoId = $this->getYoutubeVideoId($video->url);


                if ($videoId) {
                    $views = $this->getYouTubeViews($videoId);
                    if ($views !== null) {
                        $video->update(['views' => $views]);
                        // Log::info("Views for video ID {$video->id} updated to $views");
                    }
                }
            }
        }

        $this->info('YouTube views updated successfully.');
    }

    private function getYoutubeVideoId($url)
    {
        preg_match('/^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#\&\?]*).*/', $url, $match);
        return $match[7] ?? '';
    }

    private function getYouTubeViews($videoId)
    {
        $client = new \Google\Client();
        $client->setDeveloperKey(env('YOUTUBE_API_KEY'));
        $service = new \Google\Service\YouTube($client);

        try {
            $videoResponse = $service->videos->listVideos('statistics', array('id' => $videoId));
            $video = $videoResponse[0];

            return $video->getStatistics()->getViewCount();
        } catch (\Google\Service\Exception $e) {
            return null;
        }
    }
}
