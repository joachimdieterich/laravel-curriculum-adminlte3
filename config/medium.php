<?php

use App\Interfaces\Implementations\LocalMediaAdapter;
return [

    /*
    |--------------------------------------------------------------------------
    | Repositories
    |--------------------------------------------------------------------------
    |
    | Media Repositories
    |
    */

    'repositories' => [

        'default' => env('MEDIA_ADAPTER', 'local'),

        'local' => [
            'adapter' => new LocalMediaAdapter(),
        ],

    ],

];
