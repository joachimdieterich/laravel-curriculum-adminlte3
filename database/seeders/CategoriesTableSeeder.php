<?php

namespace Database\Seeders;

use App\Categorie;
use Illuminate\Database\Seeder;

class CategoriesTableSeeder extends Seeder
{
    public function run()
    {
        $categories = [
            [
                'id' => 1,
                'title' => 'Ohne Kategorie',
            ],
            [
                'id' => 2,
                'title' => 'Themen und Inhalte',
            ],
            [
                'id' => 3,
                'title' => 'Pädagogik',
            ],
            [
                'id' => 4,
                'title' => 'Methodik',
            ],
            [
                'id' => 5,
                'title' => 'Didaktik',
            ],
        ];

        Categorie::insert($categories);
    }
}
