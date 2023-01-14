<?php

namespace App\Providers;

use App\Domains\Tests\Interfaces\TestToolkitInterface;
use App\Domains\Tests\Requests\TestToolRequest;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Laravel\Dusk\DuskServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment('local', 'testing')) {
            $this->app->register(DuskServiceProvider::class);
        }

        $this->app->singleton(TestToolkitInterface::class, function ($app) {
            $request = app(TestToolRequest::class);
            switch ($request->input('tool')) {
                    case  'ilea_plus':
                        return $app->make('config')->get('test_tools.tools.ilea_plus.adapter');
                    default:
                        throw  new  \RuntimeException('Unknown Tool Service');
                }
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        $this->app->alias('bugsnag.logger', \Illuminate\Contracts\Logging\Log::class);
        $this->app->alias('bugsnag.logger', \Psr\Log\LoggerInterface::class);
    }
}
