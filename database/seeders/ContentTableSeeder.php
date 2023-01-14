<?php

namespace Database\Seeders;

use App\Content;
use Illuminate\Database\Seeder;

class ContentsTableSeeder extends Seeder
{
    public function run()
    {
        $contents = [
            [
                'id' => 1,
                'title' => 'Impressum',
                'content' => 'Impressum',
                'owner_id' => 1,
            ],
            [
                'id' => 2,
                'title' => 'Terms',
                'content' => 'Terms',
                'owner_id' => 1,
            ],
            [
                'id' => 3,
                'title' => 'About',
                'content' => 'About',
                'owner_id' => 1,
            ],
            [
                'id' => 4,
                'title' => 'First Steps',
                'content' => 'First Steps',
                'owner_id' => 1,
            ],
            [
                'id' => 5,
                'title' => 'FAQs',
                'content' => 'FAQs',
                'owner_id' => 1,
            ],

        ];

        Content::insert($contents);
    }
}
