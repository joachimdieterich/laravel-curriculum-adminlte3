<?php

namespace Database\Seeders;

use App\Task;
use Illuminate\Database\Seeder;

class TaskTableSeeder extends Seeder
{
    public function run()
    {
        $tasks = [[
            'id' => 1,
            'title' => 'Testaufgabe',
            'description' => 'Beschreibung der Testaufgabe',
            'start_date' => '2019-04-15 19:14:42',
            'due_date' => '2021-04-15 19:14:42',
            'owner_id' => 1,
        ],

        ];

        Task::insert($tasks);
    }
}
