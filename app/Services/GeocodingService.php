<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeocodingService
{
    public static function geocode(string $address): ?array
    {
        try {
            $response = Http::withHeaders([
                'User-Agent' => 'Mini-Rb/1.0 (student project)',
            ])->timeout(5)->get('https://nominatim.openstreetmap.org/search', [
                'q'      => $address,
                'format' => 'json',
                'limit'  => 1,
            ]);

            if (!$response->successful()) {
                return null;
            }

            $results = $response->json();
            if (empty($results)) {
                return null;
            }

            return [
                'latitude'  => (float) $results[0]['lat'],
                'longitude' => (float) $results[0]['lon'],
            ];
        } catch (\Throwable $e) {
            Log::warning('Geocoding failed', ['address' => $address, 'error' => $e->getMessage()]);
            return null;
        }
    }
}
