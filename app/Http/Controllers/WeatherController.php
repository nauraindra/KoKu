<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class WeatherController extends Controller
{
    public function current()
    {
        $preference = auth()->user()->preference;

        $latitude = $preference->latitude ?? -6.200000;
        $longitude = $preference->longitude ?? 106.816666;
        $city = $preference->weather_city ?? 'Jakarta';

        $weather = Cache::remember(
            'weather_' . auth()->id() . '_' . $latitude . '_' . $longitude,
            now()->addMinutes(15),
            function () use ($latitude, $longitude) {
                $response = Http::get('https://api.open-meteo.com/v1/forecast', [
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'current' => 'temperature_2m,relative_humidity_2m,wind_speed_10m',
                    'timezone' => 'Asia/Jakarta',
                ]);

                if (! $response->successful()) {
                    return null;
                }

                return $response->json('current');
            }
        );

        return response()->json([
            'city' => $city,
            'temperature' => $weather['temperature_2m'] ?? null,
            'humidity' => $weather['relative_humidity_2m'] ?? null,
            'wind' => $weather['wind_speed_10m'] ?? null,
        ]);
    }
}
