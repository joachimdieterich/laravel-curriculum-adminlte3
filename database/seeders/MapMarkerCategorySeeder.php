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
            ['title' => 'Bildunsmarker', 'color' => '#ABB2B9', 'parent_id' => NULL, 'owner_id' => 1],
            ['title' => 'Fortbildungen', 'color' => '#85C1E9', 'parent_id' => NULL, 'owner_id' => 1],
            ['title' => 'Pädagogisches Landesinstitut', 'color' => '#237d74', 'parent_id' => NULL, 'owner_id' => 1],
            ['title' => 'Digitales Kompetenzzentrum', 'color' => '#342412', 'parent_id' => 3, 'owner_id' => 1],
            ['title' => 'RegiKomp', 'color' => '#442342', 'parent_id' => 4, 'owner_id' => 1],
            ['title' => 'KMZ', 'color' => '#1443f2', 'parent_id' => 5, 'owner_id' => 1],
        ]);
    }
}
