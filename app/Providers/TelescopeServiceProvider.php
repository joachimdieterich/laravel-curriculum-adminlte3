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
     */
    public function register(): void
    {
        $this->hideSensitiveRequestDetails();

        // Add Telescope tag status
        Telescope::tag(function (IncomingEntry $entry) {
            if ($entry->type === 'request') {
                $tags = [
                    $entry->content['response_status'],
                    preg_split("/[\/|?]/", $entry->content['uri'])[1] ?? null,
                ];

                if ($entry->content['controller_action']) {
                    // Controller with method
                    $tags[] = array_last(explode('\\', $entry->content['controller_action']));
                }

                if ($entry->content['duration'] >= config('telescope.duration_filter')) {
                    $tags[] = 'exceeded_duration';
                }

                return $tags;
            }

            return [];
        });

        Telescope::filter(function (IncomingEntry $entry) {
            if ($entry->type === 'request') {
                // discard requests trying to fetch old assets
                if (in_array('build', $entry->tags)) {
                    return false;
                }

                // expired links are not interesting
                if (in_array(410, $entry->tags)) {
                    return false;
                }

                // only filter long (default 1000ms) requests if it's media (edusharing)
                if (in_array('exceeded_duration', $entry->tags) && in_array('media', $entry->tags)) {
                    return false;
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
