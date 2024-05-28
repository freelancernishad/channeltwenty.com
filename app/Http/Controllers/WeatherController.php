<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class WeatherController extends Controller
{
    public function index()
    {
        // Make a GET request to weather API
        $client = new Client();
        $response = $client->get('https://api.openweathermap.org/data/2.5/weather?q=Rangpur Division, BD&appid=c97986297f1e4ab6aca9bd348513f09f&units=metric');
        $data = json_decode($response->getBody(), true);






        // Return weather data as JSON
        return response()->json($data);
    }
}
