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

        $clientIp = $request->ip();
        // Fetch location based on IP address (using a service like ip-api.com)
        $location = $this->getLocationByIp($clientIp);
        $temperature = $this->getTemperature($location);

        // Prepare response data
        $response = [
            'client_ip' => $location['ip'],
            'location' => $location['location']['city'],
            'greeting' => "Hello, $visitorName! The temperature is {$temperature['current']['temp_c']} degrees Celsius in {$location['city']}."
        ];

        return response()->json($response);
    }
    
    private function getLocationByIp($ip)
    {
        // Validate if $ip is a client IP or server IP
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            // It's likely a client IP, use it directly
            $clientIp = $ip;
        } else {
            // Use $_SERVER['REMOTE_ADDR'] as a fallback
            $clientIp = $_SERVER['REMOTE_ADDR'];
        }

        $client = new Client();
        $apikey = 'at_AREGixbN54oYfVFZ3hDwbbNDRJ0HT';

        // $response = $client->get("http://ip-api.com/json/{$clientIp}");
        $response = $client->get("https://geo.ipify.org/api/v1?apiKey={$apikey}&ipAddress={$clientIp}");
        $data = json_decode($response->getBody(), true);

        return $data;
    }
    
    private function getTemperature($getLoc)
    {
        $client = new Client();
        $api = "30329a0a640c479ebec65139240307";
        $response = $client->get("http://api.weatherapi.com/v1/current.json?key=$api&q={$getLoc['location']['lat']},{$getLoc['location']['lng']}");
        $data = json_decode($response->getBody(), true);

        return $data;
    }
}
