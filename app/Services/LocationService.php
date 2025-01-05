<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class LocationService
{
    public function getLocation(string $ip): ?array
    {
        try {
            $response = Http::get("http://ip-api.com/json/{$ip}");
            
            if ($response->successful()) {
                $data = $response->json();
                return [
                    'city' => $data['city'] ?? null,
                    'region' => $data['regionName'] ?? null,
                    'country' => $data['country'] ?? null,
                    'latitude' => $data['lat'] ?? null,
                    'longitude' => $data['lon'] ?? null
                ];
            }
        } catch (\Exception $e) {
            return null;
        }

        return null;
    }
} 