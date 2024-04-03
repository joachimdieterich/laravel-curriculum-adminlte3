<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Course;
use App\CurriculumSubscription;
use Illuminate\Support\Facades\DB;

class TransformCurriculumSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transform:curriculumSubscriptions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Transform curriculum subscriptions from curriculum_group to curriculum_subscription';


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
     * @return int
     */
    public function handle()
    {
        $this->info('Transform curriculum subscriptions from curriculum_group to curriculum_subscriptions...');
        $subscriptions = DB::table('curriculum_group')->get();
        $this->transform($subscriptions);
        return Command::SUCCESS;
    }

    protected function transform($subscriptions)
    {
        $bar = $this->output->createProgressBar(count($subscriptions));

        $bar->start();
        foreach ($subscriptions as $subscription) {
            $subscribe = CurriculumSubscription::updateOrCreate([
                'curriculum_id' => $subscription->curriculum_id,
                'subscribable_type' => 'App\Group',
                'subscribable_id' => $subscription->group_id,
            ], [
                'editable' => false,
                'owner_id' => 1, //use global admin
            ]);
            $subscribe->save();

            $bar->advance();
        }
        $bar->finish();
    }
}
