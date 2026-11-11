<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;

trait Subscription
{
    public static function stale(): Builder
    {
        return self::where('due_date', '<', now())->whereNotNull('due_date');
    }
}
