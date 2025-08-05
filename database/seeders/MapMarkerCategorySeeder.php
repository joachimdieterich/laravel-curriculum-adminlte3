<?php

namespace Database\Seeders;

use App\MapMarkerCategory;
use Illuminate\Database\Seeder;

class MapMarkerCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MapMarkerCategory::insert([
            ['title' => 'Bildunsmarker', 'color' => '#000000', 'owner_id' => 1],
            ['title' => 'Pädagogisches Landesinstitut', 'color' => '#000000', 'owner_id' => 1],
            ['title' => 'Digitales Kompetenzzentrum', 'color' => '#000000', 'owner_id' => 1],
        ]);
    }
}
