<?php

namespace Database\Seeders;

use App\Level;
use Illuminate\Database\Seeder;

class LevelsTableSeeder extends Seeder
{
    public function run()
    {
        $levels = [
            [
                'id' => 1,
                'title' => 'A1',
                'description' => 'Newcomers (A1) have had very little contact with digital tools and need guidance to expand their repertoire.',
                'css_color' => 'bg-indigo',
                'created_at' => '2019-04-15 19:13:32',
                'updated_at' => '2019-04-15 19:13:32',
            ],
            [
                'id' => 2,
                'title' => 'A2',
                'description' => 'Explorers (A2) have started using digital tools without, however, following a comprehensive or consistent approach. Explorers need insight and inspiration to expand their competences.',
                'css_color' => 'bg-purple',
                'created_at' => '2019-04-15 19:13:32',
                'updated_at' => '2019-04-15 19:13:32',
            ],
            [
                'id' => 3,
                'title' => 'B1',
                'description' => 'Integrators (B1) use and experiment with digital tools for a range of purposes, trying to understand which digital strategies work best in which contexts.',
                'css_color' => 'bg-fuchsia',
                'created_at' => '2019-04-15 19:13:32',
                'updated_at' => '2019-04-15 19:13:32',
            ],
            [
                'id' => 4,
                'title' => 'B2',
                'description' => 'Experts (B2) use a range of digital tools confidently, creatively and critically to enhance their professional activities. They continuously expand their repertoire of practices.',
                'css_color' => 'bg-pink',
                'created_at' => '2019-04-15 19:13:32',
                'updated_at' => '2019-04-15 19:13:32',
            ],
            [
                'id' => 5,
                'title' => 'C1',
                'description' => 'Leaders (C1) rely on a broad repertoire of flexible, comprehensive and effective digital strategies. They are a source of inspiration for others.',
                'css_color' => 'bg-maroon',
                'created_at' => '2019-04-15 19:13:32',
                'updated_at' => '2019-04-15 19:13:32',
            ],
            [
                'id' => 6,
                'title' => 'C2',
                'description' => 'Pioneers (C2) question the adequacy of contemporary digital and pedagogical practices, of which they themselves are experts. They lead innovation and are a role model for younger teachers.',
                'css_color' => 'bg-navy',
                'created_at' => '2019-04-15 19:13:32',
                'updated_at' => '2019-04-15 19:13:32',
            ],
        ];

        Level::insert($levels);
    }
}
