<?php

namespace App\Http\Controllers;

use App\Statistic;
use App\Log;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class StatisticController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->wantsJson())
        {
            switch (request('chart')) {
                case 'login':
                case 'guestLogin':
                    return ['message' => $this->getLogins(request('chart'))];
                    break;
                case 'browser':
                    return ['message' => $this->getEntriesByKey('browser', request('date'))];
                    break;
                case 'devices':
                    return ['message' => $this->getEntriesByKey('device', request('date'))];
                    break;
                case 'curricula':
                    return ['message' => $this->getEntriesByKeyWithRelatedTitle('App\Http\Controllers\CurriculumController@show', 'curricula', request('date'))];
                    break;
                case 'courses':
                    return ['message' => $this->getEntriesByKeyWithRelatedTitle('App\Http\Controllers\CourseController@show', 'curricula', request('date'))];
                    break;
                case 'eventplugin':
                    return ['message' => $this->getEntriesByKey('App\Http\Controllers\EventSubscriptionController@getEvents', request('date'))];
                    break;
                case 'repositoryplugin':
                    return ['message' => $this->getEntriesByKeyWithRelatedTitle('App\Http\Controllers\RepositorySubscriptionController@getMedia', 'enabling_objectives', request('date'), 'uuid')];
                    break;
                case 'organizations':
                    return ['message' => $this->getEntriesByKeyWithRelatedTitle('activeOrg', 'organizations', request('date'))];
                    break;
                case 'groups':
                    return ['message' => $this->getEntriesByKeyWithRelatedTitle('App\Http\Controllers\GroupsController@show', 'groups', request('date'))];
                    break;
                case 'achievements':
                    return ['message' => $this->getEntriesByKeyWithRelatedTitle('App\Http\Controllers\AchievementController@store', 'roles', request('date'))];
                    break;
                case 'certificates':
                    return ['message' => $this->getEntriesByKeyWithRelatedTitle('App\Http\Controllers\CertificateController@generate', 'certificates', request('date'))];
                    break;
                default:
                   break;
            }
        }
    }

    protected function getLogins($key = 'login')
    {
        return Log::select('created_at', 'counter')->where('key', $key)
            ->get()->map(function ($item) {
                return ['created_at' => Carbon::parse($item['created_at'])->format('Y-m-d'), 'counter' => $item['counter']];
            });
    }

    protected function getEntriesByKey($key, $date)
    {
        return Log::select( 'value', 'counter')
            ->where('key', $key)
            ->whereDate('created_at', $date)
            ->get()->map(function ($item) {
                return ['value' => $item['value'], 'counter' => $item['counter']];
            });
    }

    protected function getEntriesByKeyWithRelatedTitle($key, $table, $date, $field = 'id')
    {
        return Log::select( "{$table}.title AS value", 'logs.counter')
            ->where('key', $key)
            ->whereDate('logs.created_at', $date)
            ->join($table, "{$table}.{$field}", '=', 'logs.value')
            ->get()->map(function ($item) {
                return ['value' =>  mb_strimwidth(strip_tags($item['value']), 0, 70, "..."), 'counter' => $item['counter']];
            });

    }

}
