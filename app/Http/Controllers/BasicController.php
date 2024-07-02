<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class BasicController extends Controller
{
    public function greet(Request $request)
    {
        // Get visitor name from query parameter
        $visitorName = $request->input('visitor_name', 'Guest');

        // Get client IP address
        $clientIp = $request->ip();

        // Fetch location based on IP address (using a service like ip-api.com)
        $location = $this->getLocationByIp($clientIp);

        // Fetch current temperature for the location (using a weather API like OpenWeatherMap)
        $temperature = $this->getCurrentTemperature($location['city']);

        // Prepare response data
        $response = [
            'client_ip' => $clientIp,
            'location' => $location['city'],
            'greeting' => "Hello, $visitorName! The temperature is $temperature degrees Celsius in {$location['city']}."
        ];

        return response()->json($response);
    }

    private function getLocationByIp($ip)
    {
        $client = new Client();
        $response = $client->get("http://ip-api.com/json/$ip");
        $data = json_decode($response->getBody(), true);

        return [
            'city' => $data['city']
        ];
    }

    private function getCurrentTemperature($city)
    {
        $apiKey = env('OPENWEATHERMAP_API_KEY');
        $client = new Client();
        $response = $client->get("http://api.openweathermap.org/data/2.5/weather?q=$city&appid=$apiKey&units=metric");
        $data = json_decode($response->getBody(), true);

        return $data['main']['temp'];
    }
}
