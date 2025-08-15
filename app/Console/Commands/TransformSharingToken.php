<?php

namespace App\Console\Commands;

use App\KanbanSubscription;
use App\User;
use Illuminate\Console\Command;

class TransformSharingToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sharing:transformToken';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Transform sharing token';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('- search for old uses with sharing token...');
        $users = User::whereNotNull('sharing_token')->get();
        $users_trashed = User::whereNotNull('sharing_token')->onlyTrashed()->get();
        $this->info('- Found '. $users->count() .' users with token. Found '. $users_trashed->count() .' trashed users.');

        $this->info('- transform to new subscription token');
        $this->transformToken($users);

        $users_trashed = User::whereNotNull('sharing_token')->onlyTrashed()->get();
        $this->forceDelete($users_trashed);

        $this->info('Done');
    }

    protected function transformToken($users)
    {
        $bar = $this->output->createProgressBar(count($users));

        $bar->start();
        foreach ($users as $user) {
            $subscriptions = $user->kanbanSubscription;
            if ($subscriptions != null)
            {
                foreach ($subscriptions as $subscription)
                    $subscription->update([
                        'subscribable_id'   => env('GUEST_USER'),
                        'title'             => $user->username,
                        'sharing_token'     => $user->sharing_token,
                    ]);
            }
            $this->info('- sharing_token for '. $user->username . ' transformed to guest user.');
            if (User::where('id',$user->id)->delete())
            {
                $this->info('- old token user deleted ('. $user->username . ').');
            }

            $bar->advance();
        }
        $bar->finish();
    }

    protected function forceDelete($users_trashed)
    {

        if ($this->confirm('Delete all trashed users (' . $users_trashed->count() . ')? Important entries of users will be transfered to a fallback user named "deleted user"  '))
        {
            $bar = $this->output->createProgressBar(count($users_trashed));

            $bar->start();

            foreach ($users_trashed as $user) {
                if ((new \App\Http\Controllers\UsersController())->forceDestroy($user, true)) {
                    $this->info('- old token user deleted was removed from db ('. $user->username . ').');
                } else {
                    return ['message' => 'Deleting failed'];
                }
                $bar->advance();
            }
            $bar->finish();
        }

    }
}
