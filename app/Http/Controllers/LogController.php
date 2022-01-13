<?php

namespace App\Http\Controllers;

use App\Log;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Browser;

class LogController extends Controller
{

    /**
     * Set log entries
     * @param string $key
     * @param string $value
     * @param int $increase
     * @return mixed
     */
    public static function set(string $key, $value = null, $increase = 1)
    {
        if (app()->runningUnitTests() or app()->runningInConsole()) {
            return ;
        }
        return Log::updateOrCreate(
            [
                'key' => $key,
                'value' => $value,
                'created_at' => Carbon::today()],
            [
                'value' => $value ?: null,
                'counter' => DB::raw('counter+' . $increase),
            ]
        ); //todo very slow -> use queue
    }

    public static function  setDevice()
    {
        if  (Browser::isMobile())
        {
            return LogController::set('device', 'Mobile');
        }
        if  (Browser::isTablet())
        {
            return LogController::set('device', 'Tablet');
        }
        if  (Browser::isDesktop())
        {
            return LogController::set('device', 'Desktop');
        }
        if  (Browser::isBot())
        {
            return LogController::set('device', 'Bot');
        }
    }

    public static function  setStatistics()
    {
        if (app()->runningUnitTests() or app()->runningInConsole()) {
            return ;
        }
        LogController::set('browser', Browser::browserName());
        LogController::setDevice();
    }

}
