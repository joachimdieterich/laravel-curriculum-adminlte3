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
    public function index()
    {
        abort_unless(is_admin(), 403);

        $result = [];

        switch (request('chart')) {
            case 'login':
            case 'ssoLogin':
            case 'guestLogin':
                $result = $this->getLogins(request('chart'), request('date_begin'), request('date_end'));
                break;
            case 'browsers':
                $result = $this->getEntriesByKey('browser', request('date_begin'), request('date_end'));
                break;
            case 'devices':
                $result = $this->getEntriesByKey('device', request('date_begin'), request('date_end'));
                break;
            case 'curricula':
                $result = $this->getEntriesByKeyWithRelatedTitle('App\Http\Controllers\CurriculumController@show', 'curricula', request('date_begin'), request('date_end'));
                break;
            case 'courses':
                $result = $this->getEntriesByKeyWithRelatedTitle('App\Http\Controllers\CourseController@show', 'curricula', request('date_begin'), request('date_end'));
                break;
            case 'eventPlugin':
                $result = $this->getEntriesByKey('App\Http\Controllers\EventSubscriptionController@getEvents', request('date_begin'), request('date_end'));
                break;
            case 'repositoryPlugin':
                $result = $this->getEntriesByKeyWithRelatedTitleFromUuid('App\Http\Controllers\RepositorySubscriptionController@getMedia', 'enabling_objectives', request('date_begin'), request('date_end'), 'uuid');
                break;
            case 'bbbPlugin':
                $result = $this->getEntriesByKey('App\Http\Controllers\VideoconferenceController@start', request('date_begin'), request('date_end'));
                break;
            case 'bbbPluginParticipants':
                $result = $this->getEntriesByKey('App\Http\Controllers\VideoconferenceController@endCallback->participantCount',  request('date_begin'), request('date_end'));
                break;
            case 'organizations':
                $result = $this->getEntriesByKeyWithRelatedTitle('activeOrg', 'organizations', request('date_begin'), request('date_end'));
                break;
            case 'groups':
                $result = $this->getEntriesByKeyWithRelatedTitle('App\Http\Controllers\GroupsController@show', 'groups', request('date_begin'), request('date_end'));
                break;
            case 'achievements':
                $result = $this->getEntriesByKeyWithRelatedTitle('App\Http\Controllers\AchievementController@store', 'roles', request('date_begin'), request('date_end'));
                break;
            case 'certificates':
                $result = $this->getEntriesByKeyWithRelatedTitle('App\Http\Controllers\CertificateController@generate', 'certificates', request('date_begin'), request('date_end'));
                break;
            case 'kanbans':
                $result = $this->getEntriesByKeyWithRelatedTitle('App\Http\Controllers\KanbanController@show', 'kanbans', request('date_begin'), request('date_end'));
                break;
            case 'model':
                $result = $this->getEntriesByModel(request('model'), request('date_begin'), request('date_end'));
                break;
            case 'favour':
                $kanbansCounter = $this->getEntriesByKeyWithRelatedTitle('App\Http\Controllers\KanbanController@favourKanban', 'kanbans', request('date_begin'), request('date_end'))
                    ->pluck('counter')
                    ->sum();

                if ($kanbansCounter > 0) {
                    $result[] = [
                        'value' => trans('global.kanban.title'),
                        'counter' => $kanbansCounter,
                    ];
                }

                $curriculaCounter = $this->getEntriesByKeyWithRelatedTitle('App\Http\Controllers\KanbanController@favourCurriculum', 'curricula', request('date_begin'), request('date_end'))
                    ->pluck('counter')
                    ->sum();

                if ($curriculaCounter > 0) {
                    $result[] = [
                        'value' => trans('global.curriculum.title'),
                        'counter' => $curriculaCounter,
                    ];
                }

                break;
            case 'hidden':
                $kanbansCounter = $this->getEntriesByKeyWithRelatedTitle('App\Http\Controllers\KanbanController@hideKanban', 'kanbans', request('date_begin'), request('date_end'))
                    ->pluck('counter')
                    ->sum();

                if ($kanbansCounter > 0) {
                    $result[] = [
                        'value' => trans('global.kanban.title'),
                        'counter' => $kanbansCounter,
                    ];
                }

                $curriculaCounter = $this->getEntriesByKeyWithRelatedTitle('App\Http\Controllers\KanbanController@hideCurriculum', 'curricula', request('date_begin'), request('date_end'))
                    ->pluck('counter')
                    ->sum();

                if ($curriculaCounter > 0) {
                    $result[] = [
                        'value' => trans('global.curriculum.title'),
                        'counter' => $curriculaCounter,
                    ];
                }
                break;
            default:
                break;
        }

        return $result;
    }

    protected function getLogins($key, $date_begin, $date_end)
    {
        switch (request('chart')) {
            case 'ssoLogin':  $background = '#325e04'; break;
            case 'guestLogin': $background = '#0e1b01'; break;
            default: //case 'login':
                $key = 'login';
                $background = '#7eab51';
                break;
        }

        $data = Log::select('created_at', 'counter')
            ->where('key', $key)
            ->whereBetween('created_at', [
                Carbon::parse($date_begin)->startOfDay()->format('Y-m-d H:i:s'),
                Carbon::parse($date_end)->endOfDay()->format('Y-m-d H:i:s'),
            ])
            ->get();


        return [
            'labels' => $data->map(function($item) {
                return Carbon::parse($item['created_at'])->format('Y-m-d');
            }),
            'datasets' => [
                'label' => $key,
                'backgroundColor' => $background,
                'data' => $data->map(function ($item) {
                    return $item['counter'];
                }),
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
        $entries = $class::whereBetween('created_at', [
                Carbon::parse($date_begin)->startOfDay()->format('Y-m-d H:i:s'),
                Carbon::parse($date_end)->endOfDay()->format('Y-m-d H:i:s'),
            ])->count();

        return [
            'value' => $model,
            'counter' => $entries,
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
