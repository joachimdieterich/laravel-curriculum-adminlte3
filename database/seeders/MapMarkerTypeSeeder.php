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
            ['title' => 'Organisation', 'color' => '#2471A3', 'css_icon' => 'fa-graduation-cap', 'owner_id' => 1],
            ['title' => 'Projekt', 'color' => '#17A589', 'owner_id' => 1],
            ['title' => 'Service', 'color' => '#D4AC0D', 'css_icon' => 'fa-person-chalkboard', 'owner_id' => 1],
            ['title' => 'Veranstaltung', 'color' => '#52BE80', 'owner_id' => 1],
            ['title' => 'Tool', 'color' => '#C0392B', 'owner_id' => 1],
            ['title' => 'Veröffentlichung', 'color' => '#F4D03F', 'owner_id' => 1],
            ['title' => 'Person', 'color' => '#7F8C8D', 'owner_id' => 1],
        ]);
    }
}
