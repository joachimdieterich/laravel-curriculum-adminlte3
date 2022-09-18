<?php

use App\Domains\Tests\Services\IleaPlusToolAdapter;

return [ 'tools' => [
            'ilea_plus' => [
                'base_url' => env("ILEAPLUS_API_URL"),
                'report_base_url' => env("ILEAPLUS_REPORT_URL"),
                'login_url' => env("ILEAPLUS_UI_URL"),
                'apiKey' => env("ILEAPLUS_API_KEY"),
                'adapter' => new IleaPlusToolAdapter()
            ]
        ]

];
