<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        'Aacotroneo\Saml2\Events\Saml2LoginEvent' => [
            'App\Listeners\SAMLLoginListener',
        ],
        'Aacotroneo\Saml2\Events\Saml2LogoutEvent' => [
            'App\Listeners\SAMLLogoutListener',
        ],
    ];

    protected $subscribe = [

    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        if (env('APP.LOGGING.EVENTS', false)) {
            Event::listen('*', function ($event, array $data) {
                if ($event == 'Illuminate\\Log\\Events\\MessageLogged'){ return;}
                Log::debug($event . ' invoked');
                Log::debug(json_encode($data, JSON_PRETTY_PRINT));
            });
        }
    }
}
