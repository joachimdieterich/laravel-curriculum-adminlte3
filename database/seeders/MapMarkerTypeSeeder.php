<?php

namespace Database\Seeders;

use App\MapMarkerType;
use Illuminate\Database\Seeder;

class MapMarkerTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MapMarkerType::insert([
            [null], // TODO: get data from PROD
            ['title' => 'Organisation', 'color' => '#000000', 'owner_id' => 1],
            ['title' => 'Projekt', 'color' => '#000000', 'owner_id' => 1],
            ['title' => 'Service', 'color' => '#000000', 'owner_id' => 1],
        ]);
    }
}
