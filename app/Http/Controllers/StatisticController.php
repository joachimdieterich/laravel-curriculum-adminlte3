<?php

namespace App\Http\Controllers;

use App\Curriculum;
use App\EnablingObjective;
use App\Log;
use App\TerminalObjective;
use Carbon\Carbon;

class StatisticController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_unless(is_admin(), 403);

        if (request()->wantsJson()) {
            switch (request('chart')) {
                case 'login':
                case 'ssoLogin':
                case 'guestLogin':
                    return ['message' => $this->getLogins(request('chart'))];
                    break;
                case 'browsers':
                    return ['message' => $this->getEntriesByKey('browser', request('date_begin'), request('date_end'))];
                    break;
                case 'devices':
                    return ['message' => $this->getEntriesByKey('device', request('date_begin'), request('date_end'))];
                    break;
                case 'curricula':
                    return ['message' => $this->getEntriesByKeyWithRelatedTitle('App\Http\Controllers\CurriculumController@show', 'curricula', request('date_begin'), request('date_end'))];
                    break;
                case 'courses':
                    return ['message' => $this->getEntriesByKeyWithRelatedTitle('App\Http\Controllers\CourseController@show', 'curricula', request('date_begin'), request('date_end'))];
                    break;
                case 'eventPlugin':
                    return ['message' => $this->getEntriesByKey('App\Http\Controllers\EventSubscriptionController@getEvents', request('date_begin'), request('date_end'))];
                    break;
                case 'repositoryPlugin':
                    return ['message' => $this->getEntriesByKeyWithRelatedTitleFromUuid('App\Http\Controllers\RepositorySubscriptionController@getMedia', 'enabling_objectives', request('date_begin'), request('date_end'), 'uuid')];
                    break;
                case 'organizations':
                    return ['message' => $this->getEntriesByKeyWithRelatedTitle('activeOrg', 'organizations', request('date_begin'), request('date_end'))];
                    break;
                case 'groups':
                    return ['message' => $this->getEntriesByKeyWithRelatedTitle('App\Http\Controllers\GroupsController@show', 'groups', request('date_begin'), request('date_end'))];
                    break;
                case 'achievements':
                    return ['message' => $this->getEntriesByKeyWithRelatedTitle('App\Http\Controllers\AchievementController@store', 'roles', request('date_begin'), request('date_end'))];
                    break;
                case 'certificates':
                    return ['message' => $this->getEntriesByKeyWithRelatedTitle('App\Http\Controllers\CertificateController@generate', 'certificates', request('date_begin'), request('date_end'))];
                    break;
                default:
                   break;
            }
        }
    }

    protected function getLogins($key = 'login')
    {
        switch (request('chart')) {
            case 'login': $background = "#7eab51"; break;
            case 'ssoLogin':  $background = "#325e04"; break;
            case 'guestLogin': $background = "#0e1b01"; break;
            default: break;
        }
        $labels =  Log::select('created_at', 'counter')->where('key', $key)
            ->get()->map(function ($item) {
                return Carbon::parse($item['created_at'])->format('Y-m-d');
            });

        return [
            "labels" => $labels,
            "datasets" => [
                    "label" => $key,
                    "backgroundColor" => $background,
                    "data" => Log::select('created_at', 'counter')
                        ->where('key', $key)
                        ->get()
                        ->map(
                            function ($item) {
                                return  $item['counter'];
                            }
                        )
            ]
        ];
        /*return Log::select('created_at', 'counter')->where('key', $key)
            ->get()->map(function ($item) {
                return ['created_at' => Carbon::parse($item['created_at'])->format('Y-m-d'), 'counter' => $item['counter']];
            });*/

    }

    protected function getEntriesByKey($key, $date_begin, $date_end)
    {
        return Log::groupBy('value')
        ->selectRaw('value, sum(`counter`) AS counter')
            ->where('key', $key)
            ->whereBetween('created_at', [
                Carbon::createFromDate($date_begin)->startOfDay()->format('Y-m-d H:i:s'),
                Carbon::createFromDate($date_end)->endOfDay()->format('Y-m-d H:i:s'),
            ])//->whereDate('created_at', $date)
            ->get()->map(function ($item) {
                return ['value' => $item['value'], 'counter' => $item['counter']];
            });
    }

    protected function getEntriesByKeyWithRelatedTitle($key, $table, $date_begin, $date_end, $field = 'id')
    {
        return Log::groupBy('logs.value')
            ->selectRaw("{$table}.title, logs.value, sum(`logs`.`counter`) AS counter")
            ->where('key', $key)
            ->whereBetween('logs.created_at', [
                Carbon::createFromDate($date_begin)->startOfDay()->format('Y-m-d H:i:s'),
                Carbon::createFromDate($date_end)->endOfDay()->format('Y-m-d H:i:s'),
            ])
            //->whereDate('logs.created_at', $date)
            ->join($table, "{$table}.{$field}", '=', 'logs.value')
            ->get()->map(function ($item) {
                return ['value' =>  mb_strimwidth(strip_tags($item['title']), 0, 70, '...'), 'counter' => $item['counter']];
            });
    }

    protected function getEntriesByKeyWithRelatedTitleFromUuid($key, $table, $date_begin, $date_end, $field = 'id')
    {
        return Log::groupBy('value')
            ->selectRaw('value, sum(`counter`) AS counter')
            ->where('key', $key)
            ->whereBetween('created_at', [
                Carbon::createFromDate($date_begin)->startOfDay()->format('Y-m-d H:i:s'),
                Carbon::createFromDate($date_end)->endOfDay()->format('Y-m-d H:i:s'),
            ])//->whereDate('created_at', $date)
            ->get()->map(function ($item) {
                return ['value' => $this->getTitleFromUuid($item['value']), 'counter' => $item['counter']];
            });
    }

    protected function getTitleFromUuid($uuid)
    {
        $c = Curriculum::select('title')->where('uuid', $uuid);
        $t = TerminalObjective::select('title')->where('uuid', $uuid);

        return mb_strimwidth(
            strip_tags(
                EnablingObjective::select('title')
                    ->where('uuid', $uuid)
                    ->union($c)
                    ->union($t)
                    ->get()
                    ->first()
                    ->title),
            0,
            70,
            '...');
    }
}
