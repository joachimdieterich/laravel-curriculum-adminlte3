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
                // don't filter requests that take too long to process (default 1000ms)
                if($entry->content['duration'] >= config('telescope.duration_filter')) {
                    return true;
                }
                // only show requests with a response status code greater than or equal to the configured status filter (default 200)
                if ($entry->content['response_status'] >= config('telescope.status_filter')) {
                    return true;
                }
            }

            $statusFilterShowTypeArray = explode(',', config('telescope.show_type'));
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
            return in_array($user->email, explode(',', config('telescope.users')));
        });
    }
}