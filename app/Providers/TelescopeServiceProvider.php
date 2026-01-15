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
    public function register(): void
    {
        $this->hideSensitiveRequestDetails();

        // Add Telescope tag status
        Telescope::tag(static function (IncomingEntry $entry) {
            if ($entry->type === 'request') {
                return [$entry->content['response_status']];
            }

            return [];
        });

        Telescope::filter(static function (IncomingEntry $entry) {
            if ($entry->type === 'request') {
                // Request mit einer Ladezeit von über 1 Sekunde NICHT rausfiltern
                if($entry->content['duration'] >= 1000) {
                    return true;
                }

                $statusFilterArray = explode(',', env("TELESCOPE_STATUS_FILTER", "200, 302"));
                if (!in_array($entry->content['response_status'], $statusFilterArray)) {
                    return true;
                }
            }

            $statusFilterShowTypeArray = explode(
                ',',
                env("TELESCOPE_STATUS_FILTER_SHOW_TYPE", "dump,query")
            );
            //store specific types
            if (in_array($entry->type, $statusFilterShowTypeArray)) {
                return true;
            }

            return $entry->isReportableException() ||
                   $entry->isFailedRequest() ||
                   $entry->isFailedJob() ||
                   $entry->isScheduledTask() ||
                   $entry->hasMonitoredTag();
        });
    }

    /**
     * Prevent sensitive request details from being logged by Telescope.
     *
     * @return void
     */
    protected function hideSensitiveRequestDetails(): void
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
