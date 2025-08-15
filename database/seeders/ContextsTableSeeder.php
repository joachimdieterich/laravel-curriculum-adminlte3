<?php

namespace Database\Seeders;

use App\Context;
use Illuminate\Database\Seeder;

class ContextsTableSeeder extends Seeder
{
    public function run()
    {
        $contexts = [
            [
                'id' => 1,
                'title' => 'user',
                'path' => 'users/',
            ],
            [
                'id' => 2,
                'title' => 'curriculum',
                'path' => 'curricula/',
            ],
            [
                'id' => 3,
                'title' => 'avatar',
                'path' => 'users/',
            ],
            [
                'id' => 4,
                'title' => 'solution',
                'path' => 'solutions/',
            ],
            [
                'id' => 5,
                'title' => 'subject',
                'path' => 'subjects/',
            ],
            [
                'id' => 6,
                'title' => 'badge',
                'path' => 'badges/',
            ],
            [
                'id' => 7,
                'title' => 'editor',
                'path' => 'user/',
            ],
            [
                'id' => 8,
                'title' => 'backup',
                'path' => 'backups/',
            ],
            [
                'id' => 9,
                'title' => 'institution',
                'path' => 'institution/',
            ],
            [
                'id' => 10,
                'title' => 'coursebook',
                'path' => 'coursebooks/',
            ],
            [
                'id' => 11,
                'title' => 'dashboard',
                'path' => null,
            ],
            [
                'id' => 12,
                'title' => 'enabling_objective',
                'path' => null,
            ],
            [
                'id' => 13,
                'title' => 'task',
                'path' => null,
            ],
            [
                'id' => 14,
                'title' => 'terms',
                'path' => null,
            ],
            [
                'id' => 15,
                'title' => 'content',
                'path' => 'curricula/',
            ],
            [
                'id' => 16,
                'title' => 'group',
                'path' => null,
            ],
            [
                'id' => 17,
                'title' => 'course',
                'path' => null,
            ],
            [
                'id' => 18,
                'title' => 'wallet',
                'path' => null,
            ],
            [
                'id' => 19,
                'title' => 'config',
                'path' => null,
            ],
            [
                'id' => 20,
                'title' => 'signature',
                'path' => null,
            ],
            [
                'id' => 21,
                'title' => 'blog',
                'path' => null,
            ],
            [
                'id' => 22,
                'title' => 'user_certificate',
                'path' => 'users/',
            ],
            [
                'id' => 23,
                'title' => 'badge_preview',
                'path' => null,
            ],
            [
                'id' => 24,
                'title' => 'license',
                'path' => 'licenses/',
            ],
            [
                'id' => 25,
                'title' => 'glossar',
                'path' => null,
            ],
            [
                'id' => 26,
                'title' => 'reference',
                'path' => null,
            ],
            [
                'id' => 27,
                'title' => 'terminal_objective',
                'path' => null,
            ],
            [
                'id' => 28,
                'title' => 'navigator',
                'path' => null,
            ],
            [
                'id' => 29,
                'title' => 'file',
                'path' => null,
            ],
            [
                'id' => 30,
                'title' => 'navigator_view',
                'path' => null,
            ],
            [
                'id' => 31,
                'title' => 'navigator_block',
                'path' => null,
            ],
            [
                'id' => 32,
                'title' => 'debug',
                'path' => null,
            ],
            [
                'id' => 33,
                'title' => 'book',
                'path' => null,
            ],
            [
                'id' => 34,
                'title' => 'imprint',
                'path' => null,
            ],
            [
                'id' => 35,
                'title' => 'privacy',
                'path' => null,
            ],
            [
                'id' => 36,
                'title' => 'information',
                'path' => null,
            ],
            [
                'id' => 37,
                'title' => 'print',
                'path' => null,
            ],
            [
                'id' => 38,
                'title' => 'accomplished',
                'path' => null,
            ],
        ];

        Context::insert($contexts);
    }
}
