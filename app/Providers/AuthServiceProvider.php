<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        //$this->registerPolicies(); --> now automatic registered by Framework v.10 https://laravel.com/docs/10.x/upgrade#register-policies

        \Gate::define('viewWebSocketsDashboard', function ($user = null) {
            return in_array($user->email, [
                'admin@curriculumonline.de'
            ]);
        });

        //Passport::routes(); --> now moved to dedicated route file -> v.10 https://github.com/laravel/passport/blob/12.x/UPGRADE.md
    }
}
