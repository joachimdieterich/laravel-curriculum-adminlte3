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
     */
    public function index() : array
    {
        abort_unless(is_admin(), 403);

        if (request()->wantsJson()) {
            switch (request('chart')) {
                case 'login':
                case 'ssoLogin':
                case 'guestLogin':
                    $result =  ['message' => $this->getLogins(request('chart'), request('date_begin'), request('date_end'))];
                    break;
                case 'browsers':
                    $result =  ['message' => $this->getEntriesByKey('browser', request('date_begin'), request('date_end'))];
                    break;
                case 'devices':
                    $result =  ['message' => $this->getEntriesByKey('device', request('date_begin'), request('date_end'))];
                    break;
                case 'curricula':
                    $result =  ['message' => $this->getEntriesByKeyWithRelatedTitle('App\Http\Controllers\CurriculumController@show', 'curricula', request('date_begin'), request('date_end'))];
                    break;
                case 'courses':
                    $result =  ['message' => $this->getEntriesByKeyWithRelatedTitle('App\Http\Controllers\CourseController@show', 'curricula', request('date_begin'), request('date_end'))];
                    break;
                case 'eventPlugin':
                    $result =  ['message' => $this->getEntriesByKey('App\Http\Controllers\EventSubscriptionController@getEvents', request('date_begin'), request('date_end'))];
                    break;
                case 'repositoryPlugin':
                    $result =  ['message' => $this->getEntriesByKeyWithRelatedTitleFromUuid('App\Http\Controllers\RepositorySubscriptionController@getMedia', 'enabling_objectives', request('date_begin'), request('date_end'), 'uuid')];
                    break;
                case 'bbbPlugin':
                    $result =  ['message' => $this->getEntriesByKey('App\Http\Controllers\VideoconferenceController@start', request('date_begin'), request('date_end'))];
                    break;
                case 'bbbPluginParticipants':
                    $result =  ['message' => $this->getEntriesByKey('App\Http\Controllers\VideoconferenceController@endCallback->participantCount',  request('date_begin'), request('date_end'))];
                    break;
                case 'organizations':
                    $result =  ['message' => $this->getEntriesByKeyWithRelatedTitle('activeOrg', 'organizations', request('date_begin'), request('date_end'))];
                    break;
                case 'groups':
                    $result =  ['message' => $this->getEntriesByKeyWithRelatedTitle('App\Http\Controllers\GroupsController@show', 'groups', request('date_begin'), request('date_end'))];
                    break;
                case 'achievements':
                    $result =  ['message' => $this->getEntriesByKeyWithRelatedTitle('App\Http\Controllers\AchievementController@store', 'roles', request('date_begin'), request('date_end'))];
                    break;
                case 'certificates':
                    $result =  ['message' => $this->getEntriesByKeyWithRelatedTitle('App\Http\Controllers\CertificateController@generate', 'certificates', request('date_begin'), request('date_end'))];
                    break;
                case 'kanbans':
                    $result =  ['message' => $this->getEntriesByKeyWithRelatedTitle('App\Http\Controllers\KanbanController@show', 'kanbans', request('date_begin'), request('date_end'))];
                    break;
                case 'model':
                    $result =  ['message' => $this->getEntriesByModel(request('model'), request('date_begin'), request('date_end'))];
                    break;
                default:
                   break;
            }
        }
        return $result;
    }

    protected function getLogins($key, $date_begin, $date_end )
    {
        switch (request('chart')) {

            case 'ssoLogin':  $background = '#325e04'; break;
            case 'guestLogin': $background = '#0e1b01'; break;
            default: //case 'login':
                $key = 'login';
                $background = '#7eab51';
                break;
        }
        $labels = Log::select('created_at', 'counter')
            ->where('key', $key)
            ->whereBetween('created_at', [
                Carbon::parse($date_begin)->startOfDay()->format('Y-m-d H:i:s'),
                Carbon::parse($date_end)->endOfDay()->format('Y-m-d H:i:s'),
            ])
            ->get()->map(function ($item) {
                return Carbon::parse($item['created_at'])->format('Y-m-d');
            });


        return [
            'labels' => $labels,
            'datasets' => [
                'label' => $key,
                'backgroundColor' => $background,
                'data' => Log::select('created_at', 'counter')
                    ->where('key', $key)
                    ->get()
                    ->map(
                        function ($item) {
                            return  $item['counter'];
                        }
                    ),
            ],
        ];
    }

    protected function getEntriesByKey($key, $date_begin, $date_end)
    {
        return Log::groupBy('value')
        ->selectRaw('value, sum(`counter`) AS counter')
            ->where('key', $key)
            ->whereBetween('created_at', [
                Carbon::parse($date_begin)->startOfDay()->format('Y-m-d H:i:s'),
                Carbon::parse($date_end)->endOfDay()->format('Y-m-d H:i:s'),
            ])//->whereDate('created_at', $date)
            ->get()->map(function ($item) {
                return ['value' => $item['value'], 'counter' => $item['counter']];
            });
    }

    protected function getEntriesByModel($model, $date_begin, $date_end)
    {
        $class = 'App\\'.$model;
        $entries =  $class::whereBetween('created_at', [
                Carbon::parse($date_begin)->startOfDay()->format('Y-m-d H:i:s'),
                Carbon::parse($date_end)->endOfDay()->format('Y-m-d H:i:s'),
            ])
            ->get();
        return [
            'value' => $model,
            'counter' => count($entries),
            ];
    }

    protected function getEntriesByKeyWithRelatedTitle($key, $table, $date_begin, $date_end, $field = 'id')
    {
        return Log::selectRaw("{$table}.title, logs.value, sum(logs.counter) AS counter")
            ->join($table, "{$table}.{$field}", '=', 'logs.value')
            ->where('key', $key)
            ->whereBetween('logs.created_at', [
                Carbon::parse($date_begin)->startOfDay()->format('Y-m-d H:i:s'),
                Carbon::parse($date_end)->endOfDay()->format('Y-m-d H:i:s'),
            ])
            ->groupBy('logs.value', "{$table}.title") // Füge die title-Spalte zur GROUP BY-Klausel hinzu
            ->get()
            ->map(function ($item) {
                return [
                    'value' => mb_strimwidth(strip_tags($item['title']), 0, 70, '...'),
                    'counter' => $item['counter']
                ];
            });
    }

    protected function getEntriesByKeyWithRelatedTitleFromUuid($key, $table, $date_begin, $date_end, $field = 'id')
    {
        return Log::selectRaw('logs.value, groups.title, sum(logs.counter) AS counter')
            ->join('groups', 'groups.id', '=', 'logs.value') // Füge den Join zur Gruppen-Tabelle hinzu
            ->where('key', $key)
            ->whereBetween('logs.created_at', [
                Carbon::parse($date_begin)->startOfDay()->format('Y-m-d H:i:s'),
                Carbon::parse($date_end)->endOfDay()->format('Y-m-d H:i:s'),
            ])
            ->groupBy('logs.value', 'groups.title') // Füge die title-Spalte zur GROUP BY-Klausel hinzu
            ->get()
            ->map(function ($item) {
                return [
                    'value' => $item['title'], // Verwende den Titel direkt aus dem Ergebnis
                    'counter' => $item['counter']
                ];
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
