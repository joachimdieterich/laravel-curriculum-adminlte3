<?php

namespace App\Http\Controllers\Tests;

use App\Events\OrderShipmentStatusUpdated;
use App\Http\Controllers\Controller;
use App\Order;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;

class TestController extends Controller
{

    public function index()
    {
        abort_unless(\Gate::allows('test_access'), 403);
        $tests = [];
        foreach (config('test_tools.tools') as $tool)
        {
            $tests = array_merge($tool['adapter']->getTests(), $tests);
        }
        return $tests;
    }
}
