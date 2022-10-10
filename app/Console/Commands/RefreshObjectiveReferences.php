<?php

namespace App\Console\Commands;

use App\QuoteSubscription;
use App\ReferenceSubscription;
use Illuminate\Console\Command;

class RefreshObjectiveReferences extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'objectives:refreshReferences';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh references based on reference_subscriptions and quote_subscriptions';

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
        $this->info('Task 1: Refreshing curriculum_ids based on reference_subscriptions...');
        $subscriptions = ReferenceSubscription::with([
            'siblings',
            'siblings.referenceable',

        ])->get();
        $this->referenceSubscriptions($subscriptions);
        $this->info('Task 1: Done');
        unset($subscriptions);
        $this->info('Task 2: Refreshing curriculum_ids based on quote_subscriptions...');
        $subscriptions = QuoteSubscription::with([
            'siblings',
            'siblings.quotable',

        ])->get();
        $this->referenceSubscriptions($subscriptions, 'quotable');
        $this->info('Task 2: Done');
    }

    protected function referenceSubscriptions($subscriptions, $referenceable_name = 'referenceable')
    {
        $bar = $this->output->createProgressBar(count($subscriptions));

        $bar->start();
        foreach ($subscriptions as $subscription) {
            $curriculum_id = $subscription->$referenceable_name()->get()->first()->curriculum_id;
            foreach ($subscription['siblings'] as $sibling) {
                $model = $sibling[$referenceable_name];
                $curricula_ids = (array) $model->referencing_curriculum_id;
                if (! in_array($curriculum_id, $curricula_ids)) {
                    array_push($curricula_ids, $curriculum_id);
                }

                $model->referencing_curriculum_id = $curricula_ids;
                unset($curricula_ids);
                $model->save();
                unset($model);
            }

            $bar->advance();
        }
        $bar->finish();
    }
}
