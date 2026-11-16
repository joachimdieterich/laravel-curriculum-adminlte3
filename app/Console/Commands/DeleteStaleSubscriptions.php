<?php

namespace App\Console\Commands;

use App\CurriculumSubscription;
use App\KanbanSubscription;
use App\MapMarkerSubscription;
use App\MapSubscription;
use App\VideoconferenceSubscription;
use Illuminate\Console\Command;

class DeleteStaleSubscriptions extends Command
{
    protected $signature = 'app:delete-stale-subscriptions';

    protected $description = 'Deletes all subscriptions (e.g. KanbanSubscription), that due date was yesterday.';

    public function handle()
    {
        $this->info('Gathering subscriptions.');
        $kanbanSubscriptions          = KanbanSubscription::stale();
        $curriculumSubscriptions      = CurriculumSubscription::stale();
        $videoconferenceSubscriptions = VideoconferenceSubscription::stale();
        $mapSubscriptions             = MapSubscription::stale();
        $mapMarkerSubscriptions       = MapMarkerSubscription::stale();

        $totalStale = $kanbanSubscriptions->count() + $curriculumSubscriptions->count() + $videoconferenceSubscriptions->count() + $mapSubscriptions->count() + $mapMarkerSubscriptions->count();

        $this->info('Total found stale subscriptions: ' . $totalStale);

        $kanbanSubscriptions->delete();
        $curriculumSubscriptions->delete();
        $videoconferenceSubscriptions->delete();
        $mapSubscriptions->delete();
        $mapMarkerSubscriptions->delete();

        $this->info('Subscriptions deleted successfully.');
    }
}
