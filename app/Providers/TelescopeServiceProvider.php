<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Laravel\Telescope\IncomingEntry;
use Laravel\Telescope\Telescope;
use Laravel\Telescope\TelescopeApplicationServiceProvider;

class TelescopeServiceProvider extends TelescopeApplicationServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Telescope::night();

        $this->hideSensitiveRequestDetails();

        // Add Telescope tag status
        Telescope::tag(function (IncomingEntry $entry) {
            if ($entry->type === 'request') {
                return [$entry->content['response_status']];
            }

            return [];
        });
//        Telescope::filter(function (IncomingEntry $entry) {
//            if ($this->app->isLocal()) {
//                return true;
//            }
//
//            return $entry->isReportableException() ||
//                   $entry->isFailedJob() ||
//                   $entry->isScheduledTask() ||
//                   $entry->hasMonitoredTag();
//        });
        // Fix ReflectionException: Class env does not exist #347
        // see:
        // https://github.com/laravel/telescope/issues/347#issuecomment-523550515
        // Disable the filter while in local or testing environment.
            /*$disableFilter = $this->app->environment(['local', 'testing', 'production']);
            Telescope::filter(function (IncomingEntry $entry) use ($disableFilter) {
                if ($disableFilter) {
                    return true;
                }

                return $entry->isReportableException() ||
                    $entry->isFailedRequest() ||
                    $entry->isFailedJob() ||
                    $entry->isScheduledTask() ||
                    $entry->hasMonitoredTag();
            });*/

        Telescope::filter(function (IncomingEntry $entry) {
            if($entry->type == 'request' && !in_array($entry->content['response_status'],  explode(',', env("TELESCOPE_STATUS_FILTER", "200, 302")))){
                return true;
            }
            else if (in_array($entry->type, explode(',', env("TELESCOPE_STATUS_FILTER_TYPE", "dump,query")))) //store specific types
            {
                return true;
            }
            else {
                return $entry->isReportableException() ||
                    $entry->isFailedRequest() ||
                    $entry->isFailedJob() ||
                    $entry->isScheduledTask() ||
                    $entry->hasMonitoredTag();
            }
        });
    }

    /**
     * Prevent sensitive request details from being logged by Telescope.
     *
     * @return void
     */
    protected function hideSensitiveRequestDetails()
    {
        if ($this->app->isLocal()) {
            return;
        }

        Telescope::hideRequestParameters(['_token']);

        Telescope::hideRequestHeaders([
            'cookie',
            'x-csrf-token',
            'x-xsrf-token',
        ]);
    }

    /**
     * Register the Telescope gate.
     *
     * This gate determines who can access Telescope in non-local environments.
     *
     * @return void
     */
    protected function gate()
    {
        Gate::define('viewTelescope', function ($user) {
            return in_array($user->email, explode(',', env("TELESCOPE_USERS"))
            );
        });
    }
}
