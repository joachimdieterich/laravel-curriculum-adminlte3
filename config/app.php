<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    |
    */

    'name' => env('APP_NAME', 'curriculum'),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services the application utilizes. Set this in your ".env" file.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | your application so that it is used when running Artisan tasks.
    |
    */

    'url' => env('APP_URL', 'http://127.0.0.1:8000'),

    'asset_url' => env('ASSET_URL', null),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. We have gone
    | ahead and set this to a sensible default for you out of the box.
    |
    */

    'timezone' => 'Europe/Berlin',
    //'timezone' => 'CET',

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by the translation service provider. You are free to set this value
    | to any of the locales which will be supported by the application.
    |
    */

    'locale' => 'de',
    //'locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Application Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale determines the locale to use when the current one
    | is not available. You may change the value to correspond to any of
    | the language folders that are provided through your application.
    |
    */

    'fallback_locale' => 'de',

    /*
    |--------------------------------------------------------------------------
    | Faker Locale
    |--------------------------------------------------------------------------
    |
    | This locale will be used by the Faker PHP library when generating fake
    | data for your database seeds. For example, this will be used to get
    | localized telephone numbers, street address information and more.
    |
    */

    'faker_locale' => 'de_DE',
    //'faker_locale' => 'en_US',

    /*
    |--------------------------------------------------------------------------
    | Application Template
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'template' => env('APP_TEMPLATE', 'default'),

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is used by the Illuminate encrypter service and should be set
    | to a random, 32 character string, otherwise these encrypted strings
    | will not be safe. Please do this before deploying an application!
    |
    */

    'key' => env('APP_KEY'),

    'cipher' => 'AES-256-CBC',

    /*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    |
    | The service providers listed here will be automatically loaded on the
    | request to your application. Feel free to add your own services to
    | this array to grant expanded functionality to your applications.
    |
    */

    'providers' => [

        /*
         * Laravel Framework Service Providers...
         */
        Illuminate\Auth\AuthServiceProvider::class,
        Illuminate\Broadcasting\BroadcastServiceProvider::class,
        Illuminate\Bus\BusServiceProvider::class,
        Illuminate\Cache\CacheServiceProvider::class,
        Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
        Illuminate\Cookie\CookieServiceProvider::class,
        Illuminate\Database\DatabaseServiceProvider::class,
        Illuminate\Encryption\EncryptionServiceProvider::class,
        Illuminate\Filesystem\FilesystemServiceProvider::class,
        Illuminate\Foundation\Providers\FoundationServiceProvider::class,
        Illuminate\Hashing\HashServiceProvider::class,
        Illuminate\Mail\MailServiceProvider::class,
        Illuminate\Notifications\NotificationServiceProvider::class,
        Illuminate\Pagination\PaginationServiceProvider::class,
        Illuminate\Pipeline\PipelineServiceProvider::class,
        Illuminate\Queue\QueueServiceProvider::class,
        Illuminate\Redis\RedisServiceProvider::class,
        Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
        Illuminate\Session\SessionServiceProvider::class,
        Illuminate\Translation\TranslationServiceProvider::class,
        Illuminate\Validation\ValidationServiceProvider::class,
        Illuminate\View\ViewServiceProvider::class,

        /*
         * Package Service Providers...
         */
        Bugsnag\BugsnagLaravel\BugsnagServiceProvider::class,

        /*
         * Application Service Providers...
         */
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        App\Providers\BroadcastServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\TelescopeServiceProvider::class,
        App\Providers\RouteServiceProvider::class,

        /*
         * Plugin Provider
         */
        App\Providers\PluginServiceProvider::class,

        /*
         * Datatables
         */
        Yajra\Datatables\DatatablesServiceProvider::class,

        /*
         * PDF Generator
         */
        Barryvdh\Snappy\ServiceProvider::class,
        /*
         * Messaging
         */
        Cmgmyr\Messenger\MessengerServiceProvider::class,
        /*
         *  Laravel Excel
         */
        Maatwebsite\Excel\ExcelServiceProvider::class,
        /*
         * Image Intervention
         */
        Intervention\Image\Laravel\ServiceProvider::class,

        JamesDordoy\LaravelVueDatatable\Providers\LaravelVueDatatableServiceProvider::class,

    ],

    /*
    |--------------------------------------------------------------------------
    | Class Aliases
    |--------------------------------------------------------------------------
    |
    | This array of class aliases will be registered when this application
    | is started. However, feel free to register as many as you wish as
    | the aliases are "lazy" loaded so they don't hinder performance.
    |
    */

    'aliases' => [

        'App'          => Illuminate\Support\Facades\App::class,
        'Arr'          => Illuminate\Support\Arr::class,
        'Artisan'      => Illuminate\Support\Facades\Artisan::class,
        'Auth'         => Illuminate\Support\Facades\Auth::class,
        'Blade'        => Illuminate\Support\Facades\Blade::class,
        'Broadcast'    => Illuminate\Support\Facades\Broadcast::class,
        'Bugsnag'      => Bugsnag\BugsnagLaravel\Facades\Bugsnag::class,
        'Bus'          => Illuminate\Support\Facades\Bus::class,
        'Cache'        => Illuminate\Support\Facades\Cache::class,
        'Config'       => Illuminate\Support\Facades\Config::class,
        'Cookie'       => Illuminate\Support\Facades\Cookie::class,
        'Crypt'        => Illuminate\Support\Facades\Crypt::class,
        'DB'           => Illuminate\Support\Facades\DB::class,
        'Datatables'   => Yajra\Datatables\Facades\Datatables::class,
        'Eloquent'     => Illuminate\Database\Eloquent\Model::class,
        'Event'        => Illuminate\Support\Facades\Event::class,
        'Excel'        => Maatwebsite\Excel\Facades\Excel::class,
        'File'         => Illuminate\Support\Facades\File::class,
        'Gate'         => Illuminate\Support\Facades\Gate::class,
        'Hash'         => Illuminate\Support\Facades\Hash::class,
        'Image'        => Intervention\Image\Facades\Image::class,
        'Lang'         => Illuminate\Support\Facades\Lang::class,
        'Log'          => Illuminate\Support\Facades\Log::class,
        'Mail'         => Illuminate\Support\Facades\Mail::class,
        'Notification' => Illuminate\Support\Facades\Notification::class,
        'Password'     => Illuminate\Support\Facades\Password::class,
        'PDF'          => Barryvdh\Snappy\Facades\SnappyPdf::class,
        'Queue'        => Illuminate\Support\Facades\Queue::class,
        'Redirect'     => Illuminate\Support\Facades\Redirect::class,
        'Redis'        => Illuminate\Support\Facades\Redis::class,
        'Request'      => Illuminate\Support\Facades\Request::class,
        'Response'     => Illuminate\Support\Facades\Response::class,
        'Route'        => Illuminate\Support\Facades\Route::class,
        'Schema'       => Illuminate\Support\Facades\Schema::class,
        'Session'      => Illuminate\Support\Facades\Session::class,
        'SnappyImage'  => Barryvdh\Snappy\Facades\SnappyImage::class,
        'Storage'      => Illuminate\Support\Facades\Storage::class,
        'Str'          => Illuminate\Support\Str::class,
        'URL'          => Illuminate\Support\Facades\URL::class,
        'Validator'    => Illuminate\Support\Facades\Validator::class,
        'View'         => Illuminate\Support\Facades\View::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Env-variables
    |--------------------------------------------------------------------------
    | In order to use env-variables in live-systems when caching configs,
    | they need to be listed here and called via config() helper.
    |
    */
    'ably_key' => env('ABLY_KEY'),

    'brand_menu_icon_1' => env('BRAND_MENU_ICON_1'),
    'brand_menu_icon_2' => env('BRAND_MENU_ICON_2'),
    'brand_menu_icon_3' => env('BRAND_MENU_ICON_3'),
    'brand_menu_icon_4' => env('BRAND_MENU_ICON_4'),
    'brand_menu_icon_5' => env('BRAND_MENU_ICON_5'),
    'brand_menu_icon_6' => env('BRAND_MENU_ICON_6'),
    'brand_menu_icon_7' => env('BRAND_MENU_ICON_7'),
    'brand_menu_title_1' => env('BRAND_MENU_TITLE_1'),
    'brand_menu_title_2' => env('BRAND_MENU_TITLE_2'),
    'brand_menu_title_3' => env('BRAND_MENU_TITLE_3'),
    'brand_menu_title_4' => env('BRAND_MENU_TITLE_4'),
    'brand_menu_title_5' => env('BRAND_MENU_TITLE_5'),
    'brand_menu_title_6' => env('BRAND_MENU_TITLE_6'),
    'brand_menu_title_7' => env('BRAND_MENU_TITLE_7'),
    'brand_menu_url_1' => env('BRAND_MENU_HREF_1'),
    'brand_menu_url_2' => env('BRAND_MENU_HREF_2'),
    'brand_menu_url_3' => env('BRAND_MENU_HREF_3'),
    'brand_menu_url_4' => env('BRAND_MENU_HREF_4'),
    'brand_menu_url_5' => env('BRAND_MENU_HREF_5'),
    'brand_menu_url_6' => env('BRAND_MENU_HREF_6'),
    'brand_menu_url_7' => env('BRAND_MENU_HREF_7'),

    'broadcast_connection' => env('BROADCAST_CONNECTION', 'null'),

    'bbb_logout_url' => env('BBB_LOGOUT_URL'),
    'bbb_server_name_1' => env('BBB_SERVER_NAME_1', 'Server 1'),
    'bbb_server_name_2' => env('BBB_SERVER_NAME_2', 'Server 2'),
    'bbb_server_url' => env('BBB_SERVER_BASE_URL'),
    'bbb_server_url_1' => env('BBB_SERVER_BASE_URL_1', ''),
    'bbb_server_url_2' => env('BBB_SERVER_BASE_URL_2', ''),
    'bbb_security_salt' => env('BBB_SECURITY_SALT'),
    'bbb_security_salt_1' => env('BBB_SECURITY_SALT_1', ''),
    'bbb_security_salt_2' => env('BBB_SECURITY_SALT_2', ''),

    'cache_driver' => env('CACHE_DRIVER', 'file'),

    'documentation_url' => env('DOCUMENTATION'),

    'db_connection' => env('DB_CONNECTION', 'mysql'),
    'db_database' => env('DB_DATABASE', 'curriculum'),
    'db_foreign_keys' => env('DB_FOREIGN_KEYS', true),
    'db_host' => env('DB_HOST', env('APP_HOSTNAME')),
    'db_port' => env('DB_PORT'),
    'db_socket' => env('DB_SOCKET'),
    'db_username' => env('DB_USERNAME'),
    'db_password' => env('DB_PASSWORD'),

    'edusharing_app_id' => env('EDUSHARING_APP_ID'),
    'edusharing_cloud_url' => env('EDUSHARING_CLOUD_IFRAME_URL'),
    'edusharing_upload_url' => env('EDUSHARING_UPLOAD_IFRAME_URL'),
    'edusharing_priv_key' => env('EDUSHARING_PRIV_KEY'),
    'edusharing_repo_url' => env('EDUSHARING_REPO_URL'),
    'edusharing_repo_proxy' => env('EDUSHARING_REPO_PROXY', false),
    'edusharing_repo_proxy_port' =>  env('EDUSHARING_REPO_PROXY_PORT', false),
    'edusharing_ssl_verifyhost' => env('EDUSHARING_CURLOPT_SSL_VERIFYHOST', 2),
    'edusharing_ssl_verifypeer' => env('EDUSHARING_CURLOPT_SSL_VERIFYPEER', 1),

    'eventmanagement_plugin' => env('EVENTMANAGEMENTPLUGIN'),
    'evewa_api_url' => env('EVEWA_API_URL'),
    'evewa_api_user' => env('EVEWA_API_USER'),
    'evewa_api_password' => env('EVEWA_API_PASSWORD'),
    'evewa_proxy' => env('EVEWA_PROXY', false),
    'evewa_proxy_port' => env('EVEWA_PROXY_PORT', false),

    'fallback_user_username' => env('APP_FALLBACK_USER_USERNAME', 'Deleted User'),
    'fallback_user_firstname' => env('APP_FALLBACK_USER_FIRSTNAME', 'Deleted'),
    'fallback_user_lastname' => env('APP_FALLBACK_USER_LASTNAME', 'User'),
    'fallback_user_email' => env('APP_FALLBACK_USER_EMAIL', 'deleted_user@curriculumonline.de'),

    'footer_logo_height' => env('FOOTER_LOGO_HEIGHT'),
    'footer_logo_text' => env('FOOTER_LOGO_ALT'),
    'footer_logo_url' => env('FOOTER_LOGO_URL'),
    'footer_title_1' => env('FOOTER_TITLE_1'), // Privacy Policy
    'footer_title_2' => env('FOOTER_TITLE_2'), // Terms of Service
    'footer_title_3' => env('FOOTER_TITLE_3'), // Imprint
    'footer_title_4' => env('FOOTER_TITLE_4'), // Contact
    'footer_url_1' => env('FOOTER_URL_1'),
    'footer_url_2' => env('FOOTER_URL_2'),
    'footer_url_3' => env('FOOTER_URL_3'),
    'footer_url_4' => env('FOOTER_URL_4'),

    'guest_user_id' => env('GUEST_USER', 8),

    'ilea_plus_api_key' => env('ILEAPLUS_API_KEY'),
    'ilea_plus_api_url' => env('ILEAPLUS_API_URL'),
    'ilea_plus_report_url' => env('ILEAPLUS_REPORT_URL'),
    'ilea_plus_ui_url' => env('ILEAPLUS_UI_URL'),

    'l5_swagger_const_host' => env('L5_SWAGGER_CONST_HOST'),
    'l5_swagger_generate_always' => env('L5_SWAGGER_GENERATE_ALWAYS'),
    'l5_swagger_generate_yaml_copy' => env('L5_SWAGGER_GENERATE_YAML_COPY'),
    'l5_swagger_passport_scheme' => env('L5_SWAGGER_PASSPORT_SCHEME'),
    'l5_swagger_ui_persist_authorization' => env('L5_SWAGGER_UI_PERSIST_AUTHORIZATION'),

    'lms_plugin' => env('LMSPLUGIN'),

    'logging_events' => env('APP.LOGGING.EVENTS', false),

    'mysql_attr_ssl_ca' => env('MYSQL_ATTR_SSL_CA'),

    'oidc_client_id' => env('OIDC_CLIENT_ID'),
    'oidc_client_secret' => env('OIDC_CLIENT_SECRET'),
    'oidc_host' => env('OIDC_RLP_IDP_HOST'),

    'pusher_cluster' => env('PUSHER_APP_CLUSTER'),
    'pusher_host' => env('PUSHER_HOST'),
    'pusher_port' => env('PUSHER_PORT', 443),
    'pusher_key' => env('PUSHER_APP_KEY'),
    'pusher_id' => env('PUSHER_APP_ID'),
    'pusher_secret' => env('PUSHER_APP_SECRET'),
    'pusher_scheme' => env('PUSHER_SCHEME', 'https'),

    'redis_client' => env('REDIS_CLIENT', 'predis'),
    'redis_cluster' => env('REDIS_CLUSTER', 'predis'),
    'redis_db' => env('REDIS_DB'),
    'redis_cache_db' => env('REDIS_CACHE_DB'),
    'redis_host' => env('REDIS_HOST', env('APP_HOSTNAME')),
    'redis_port' => env('REDIS_PORT'),
    'redis_password' => env('REDIS_PASSWORD'),

    'reverb_host' => env('REVERB_HOST', env('APP_HOSTNAME')),
    'reverb_port' => env('REVERB_PORT'),
    'reverb_key' => env('REVERB_APP_KEY', env('APP_KEY')),
    'reverb_id' => env('REVERB_APP_ID', env('APP_NAME')),
    'reverb_secret' => env('REVERB_APP_SECRET'),
    'reverb_server_host' => env('REVERB_SERVER_HOST', env('REVERB_HOST', env('APP_HOSTNAME'))),
    'reverb_server_port' => env('REVERB_SERVER_PORT', env('REVERB_PORT')),
    'reverb_scheme' => env('REVERB_SCHEME', 'https'),

    'session_driver' => env('SESSION_DRIVER'),
    'session_lifetime' => env('SESSION_LIFETIME'),

    'telescope_duration_filter' => env('TELESCOPE_REQUEST_DURATION_FILTER', 1000),
    'telescope_enabled' => env('TELESCOPE_ENABLED'),
    'telescope_show_type' => env('TELESCOPE_STATUS_FILTER_SHOW_TYPE', 'dump,query'),
    'telescope_status_filter' => env('TELESCOPE_STATUS_FILTER', '200, 302'),
    'telescope_users' => env('TELESCOPE_USERS'),

    'videoconference_adapter' => env('VIDEOCONFERENCE_ADAPTER'),

    'websocket_app_active' => env('WEBSOCKET_APP_ACTIVE', false),
];
