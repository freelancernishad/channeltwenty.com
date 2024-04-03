<?php

namespace App\Services;

use App\Models\ReadArticle;
use App\Models\BrowserInfo;
use App\Models\UserDetails;

class ReadArticleService
{
    public static function createReadArticle($data)
    {
        // Create ReadArticle record
        $readArticle = ReadArticle::create([
            'user_id' => $data['user_id'] ?? null,
            'article_id' => $data['article_id'],
            'browser' => $data['browser'],
            'ip_address' => $data['ip_address'],
            'location' => $data['location'],
            'mac_address' => $data['mac_address'],
            'date' => $data['date'],
            'month' => $data['month'],
            'year' => $data['year'],
            'unique_reader_id' => $data['unique_reader_id'] ?? null,
        ]);

        // Create BrowserInfo record
        $readArticle->browserInfo()->create([
            'browser' => $data['browser'],
        ]);

        // Create UserDetails record
        $readArticle->userDetails()->create([
            'location' => $data['location'],
            'ip_address' => $data['ip_address'],
            'mac_address' => $data['mac_address'],
        ]);

        return $readArticle;
    }
}
