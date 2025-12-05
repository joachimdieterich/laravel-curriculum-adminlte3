<?php

use App\Interfaces\Implementations\LocalMediaAdapter;
use App\Interfaces\Implementations\EdusharingMediaAdapter;

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

        'edusharing' => [
            'adapter' => new EdusharingMediaAdapter(),
            'upload_iframe_url' => env('EDUSHARING_UPLOAD_IFRAME_URL', ''),
            'cloud_iframe_url' => env('EDUSHARING_CLOUD_IFRAME_URL', ''),
        ],

    ],

];
