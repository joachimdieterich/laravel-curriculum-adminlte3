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
            'app_id' => env('EDUSHARING_APP_ID', ''),
            'priv_key' => env('EDUSHARING_PRIV_KEY', ''),
            'repo_url' => env('EDUSHARING_REPO_URL', ''),
            'repo_proxy' => env('EDUSHARING_REPO_PROXY', false),
            'repo_proxy_port' =>  env('EDUSHARING_REPO_PROXY_PORT', false),
            'ssl_verify_host' => env('EDUSHARING_CURLOPT_SSL_VERIFYHOST', 2),
            'ssl_verify_peer' => env('EDUSHARING_CURLOPT_SSL_VERIFYPEER', 1),
            'upload_iframe_url' => env('EDUSHARING_UPLOAD_IFRAME_URL', ''),
            'cloud_iframe_url' => env('EDUSHARING_CLOUD_IFRAME_URL', ''),
        ],

    ],

];