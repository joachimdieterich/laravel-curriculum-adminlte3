<?php

use App\Providers\AppServiceProvider;
use App\Providers\EventServiceProvider;
use App\Providers\HorizonServiceProvider;
use App\Providers\PluginServiceProvider;
use App\Providers\RouteServiceProvider;
use App\Providers\TelescopeServiceProvider;
use Barryvdh\Snappy\Facades\SnappyImage;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Barryvdh\Snappy\ServiceProvider;
use Cmgmyr\Messenger\MessengerServiceProvider;
use Illuminate\Auth\AuthServiceProvider;
use Illuminate\Auth\Passwords\PasswordResetServiceProvider;
use Illuminate\Broadcasting\BroadcastServiceProvider;
use Illuminate\Bus\BusServiceProvider;
use Illuminate\Cache\CacheServiceProvider;
use Illuminate\Cookie\CookieServiceProvider;
use Illuminate\Database\DatabaseServiceProvider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Encryption\EncryptionServiceProvider;
use Illuminate\Filesystem\FilesystemServiceProvider;
use Illuminate\Foundation\Providers\ConsoleSupportServiceProvider;
use Illuminate\Foundation\Providers\FoundationServiceProvider;
use Illuminate\Hashing\HashServiceProvider;
use Illuminate\Mail\MailServiceProvider;
use Illuminate\Notifications\NotificationServiceProvider;
use Illuminate\Pagination\PaginationServiceProvider;
use Illuminate\Pipeline\PipelineServiceProvider;
use Illuminate\Queue\QueueServiceProvider;
use Illuminate\Redis\RedisServiceProvider;
use Illuminate\Session\SessionServiceProvider;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Illuminate\Translation\TranslationServiceProvider;
use Illuminate\Validation\ValidationServiceProvider;
use Illuminate\View\ViewServiceProvider;
use Intervention\Image\Laravel\Facades\Image;
use JamesDordoy\LaravelVueDatatable\Providers\LaravelVueDatatableServiceProvider;
use Maatwebsite\Excel\ExcelServiceProvider;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTablesServiceProvider;
use Yajra\DataTables\Facades\DataTables;

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

    'asset_url' => env('ASSET_URL'),

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
    // 'timezone' => 'CET',

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
    // 'locale' => 'en',

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
    // 'faker_locale' => 'en_US',

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
        AuthServiceProvider::class,
        BroadcastServiceProvider::class,
        BusServiceProvider::class,
        CacheServiceProvider::class,
        ConsoleSupportServiceProvider::class,
        CookieServiceProvider::class,
        DatabaseServiceProvider::class,
        EncryptionServiceProvider::class,
        FilesystemServiceProvider::class,
        FoundationServiceProvider::class,
        HashServiceProvider::class,
        MailServiceProvider::class,
        NotificationServiceProvider::class,
        PaginationServiceProvider::class,
        PipelineServiceProvider::class,
        QueueServiceProvider::class,
        RedisServiceProvider::class,
        PasswordResetServiceProvider::class,
        SessionServiceProvider::class,
        TranslationServiceProvider::class,
        ValidationServiceProvider::class,
        ViewServiceProvider::class,

        /*
         * Application Service Providers...
         */
        AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        App\Providers\BroadcastServiceProvider::class,
        EventServiceProvider::class,
        TelescopeServiceProvider::class,
        HorizonServiceProvider::class,
        RouteServiceProvider::class,

        /*
         * Plugin Provider
         */
        PluginServiceProvider::class,

        /*
         * Datatables
         */
        DataTablesServiceProvider::class,

        /*
         * PDF Generator
         */
        ServiceProvider::class,
        /*
         * Messaging
         */
        MessengerServiceProvider::class,
        /*
         *  Laravel Excel
         */
        ExcelServiceProvider::class,
        /*
         * Image Intervention
         */
        Intervention\Image\Laravel\ServiceProvider::class,

        LaravelVueDatatableServiceProvider::class,

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
        'Arr'          => Arr::class,
        'Artisan'      => Artisan::class,
        'Auth'         => Auth::class,
        'Blade'        => Blade::class,
        'Broadcast'    => Broadcast::class,
        'Bus'          => Bus::class,
        'Cache'        => Cache::class,
        'Config'       => Config::class,
        'Cookie'       => Cookie::class,
        'Crypt'        => Crypt::class,
        'DB'           => DB::class,
        'Datatables'   => DataTables::class,
        'Eloquent'     => Model::class,
        'Event'        => Event::class,
        'Excel'        => Excel::class,
        'File'         => File::class,
        'Gate'         => Gate::class,
        'Hash'         => Hash::class,
        'Image'        => Image::class,
        'Lang'         => Lang::class,
        'Log'          => Log::class,
        'Mail'         => Mail::class,
        'Notification' => Notification::class,
        'Password'     => Password::class,
        'PDF'          => SnappyPdf::class,
        'Queue'        => Queue::class,
        'Redirect'     => Redirect::class,
        'Redis'        => Redis::class,
        'Request'      => Request::class,
        'Response'     => Response::class,
        'Route'        => Route::class,
        'Schema'       => Schema::class,
        'Session'      => Session::class,
        'SnappyImage'  => SnappyImage::class,
        'Storage'      => Storage::class,
        'Str'          => Str::class,
        'URL'          => URL::class,
        'Validator'    => Validator::class,
        'View'         => View::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Miscellaneous
    |--------------------------------------------------------------------------
    */
    'allow_password_reset' => env('ALLOW_PASSWORD_RESET', false),

    'brand_menu' => [
        'icons'  => env('BRAND_MENU_ICONS'),
        'links'  => env('BRAND_MENU_LINKS'),
        'titles' => env('BRAND_MENU_TITLES'),
    ],

    'documentation_url' => env('DOCUMENTATION', '/documentation'),

    'eventmanagement_plugin' => env('EVENTMANAGEMENTPLUGIN'),

    'evewa' => [
        'api_url'      => env('EVEWA_API_URL', ''),
        'api_user'     => env('EVEWA_API_USER', ''),
        'api_password' => env('EVEWA_API_PASSWORD', ''),
        'proxy'        => env('EVEWA_PROXY', false),
        'proxy_port'   => env('EVEWA_PROXY_PORT', false),
    ],

    'fallback' => [
        'username'  => env('APP_FALLBACK_USER_USERNAME', 'Deleted User'),
        'firstname' => env('APP_FALLBACK_USER_FIRSTNAME', 'Deleted'),
        'lastname'  => env('APP_FALLBACK_USER_LASTNAME', 'User'),
        'email'     => env('APP_FALLBACK_USER_EMAIL', 'deleted_user@curriculumonline.de'),
    ],

    'footer' => [
        'logo' => [
            'src'    => env('FOOTER_LOGO_URL'),
            'alt'    => env('FOOTER_LOGO_ALT'),
            'height' => env('FOOTER_LOGO_HEIGHT'),
        ],
        'menu' => [
            '1' => [ // Privacy Policy
                'title' => env('FOOTER_TITLE_1'),
                'url'   => env('FOOTER_URL_1'),
            ],
            '2' => [ // Terms of Service
                'title' => env('FOOTER_TITLE_2'),
                'url'   => env('FOOTER_URL_2'),
            ],
            '3' => [ // Imprint
                'title' => env('FOOTER_TITLE_3'),
                'url'   => env('FOOTER_URL_3'),
            ],
            '4' => [ // Contact
                'title' => env('FOOTER_TITLE_4'),
                'url'   => env('FOOTER_URL_4'),
            ],
        ],
    ],

    'guest_user_id' => env('GUEST_USER', 8),

    'lms_plugin' => env('LMSPLUGIN'),

    'oidc_client_id'     => env('OIDC_CLIENT_ID'),
    'oidc_client_secret' => env('OIDC_CLIENT_SECRET'),
    'oidc_host'          => env('OIDC_RLP_IDP_HOST'),

    'show_impressum' => env('SHOW_IMPRESSUM', false),

    'videoconference_adapter' => env('VIDEOCONFERENCE_ADAPTER'),
];
