<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class WeatherController extends Controller
{
    public function index()
    {
        // Make a GET request to weather API
        $client = new Client();
        $response = $client->get('https://api.openweathermap.org/data/2.5/weather?q=YourCity&appid=YOUR_API_KEY&units=metric');
        $data = json_decode($response->getBody(), true);

        // Return weather data as JSON
        return response()->json($data);
    }
}
