<?php

namespace Database\Seeders;

use App\Models\Amenity;
use Illuminate\Database\Seeder;

class AmenitySeeder extends Seeder
{
    public function run(): void
    {
        $amenities = [
            ['name' => 'WiFi', 'icon' => '📶'],
            ['name' => 'TV', 'icon' => '📺'],
            ['name' => 'Cuisine', 'icon' => '🍳'],
            ['name' => 'Parking', 'icon' => '🅿️'],
            ['name' => 'Climatisation', 'icon' => '❄️'],
            ['name' => 'Chauffage', 'icon' => '🔥'],
            ['name' => 'Machine à laver', 'icon' => '🧺'],
            ['name' => 'Piscine', 'icon' => '🏊'],
            ['name' => 'Spa', 'icon' => '🛁'],
            ['name' => 'Gym', 'icon' => '💪'],
            ['name' => 'Balcon', 'icon' => '🌳'],
            ['name' => 'Terrasse', 'icon' => '☀️'],
        ];

        foreach ($amenities as $amenity) {
            Amenity::updateOrCreate(
                ['name' => $amenity['name']],
                ['icon' => $amenity['icon']]
            );
        }
    }
}
