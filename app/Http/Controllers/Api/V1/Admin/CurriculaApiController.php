<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Config;
use App\Curriculum;
use App\Http\Controllers\Controller;
use App\Http\Controllers\LogController;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class CurriculaApiController extends Controller
{
    public function index(Request $request)
    {
        $config = Config::where([
            ['key', '=',  'metadata_password'],
        ])->first();

        if ($config !== null) {
            if ($config->value != $request->query('password')) {
                return response()->json(['error' => 'Not authorized.'], 403);
            }
        } else {
            return response()->json(['error' => 'config (metadata_password) missing'], 420);
        }

        request()->validate([
            'timestamp' => 'sometimes|string',
        ]);

        $timestamp = 0;
        if ($request->has('timestamp')) {
            $timestamp = strtotime($request->query('timestamp'));

            if ($timestamp === false) {
                return response()->json(['error' => 'Invalid timestamp format.'], 422);
            }
        }
        $timestamp = date('Y-m-d H:i:s', $timestamp);

        LogController::set(get_class($this).'@'.__FUNCTION__);
        // might need to be broken down in chunks, since there's a lot of data
        $curricula = Curriculum::select(
                'curricula.id', 'curricula.title', 'curricula.description',
                'grades.title as grade',
                'subjects.title as subject',
                'organization_types.title as organization_type'
            )
            ->join('grades', 'grades.id', '=', 'curricula.grade_id')
            ->join('subjects', 'subjects.id', '=', 'curricula.subject_id')
            ->join('organization_types', 'organization_types.id', '=', 'curricula.organization_type_id')
            ->join('terminal_objectives', 'terminal_objectives.curriculum_id', '=', 'curricula.id')
            ->join('enabling_objectives', 'enabling_objectives.terminal_objective_id', '=', 'terminal_objectives.id')
            ->where('type_id', 1)
            ->where(function($query) use ($timestamp) {
                $query->whereDate('curricula.updated_at', '>=', $timestamp)
                    ->orWhereDate('terminal_objectives.updated_at', '>=', $timestamp)
                    ->orWhereDate('enabling_objectives.updated_at', '>=', $timestamp);
            })
            ->with([
                'terminalObjectives' => function ($query) {
                    $query->select('id', 'title', 'description', 'curriculum_id')
                        ->with([
                            'enablingObjectives' => function ($query) {
                                $query->select('enabling_objectives.id', 'enabling_objectives.title', 'enabling_objectives.description', 'levels.title as level', 'terminal_objective_id')
                                    ->leftjoin('levels', 'levels.id', '=', 'enabling_objectives.level_id')
                                    ->without('terminalObjective', 'level');
                            }
                        ]);
                },
            ])
            ->without('owner')
            ->distinct()
            ->get();

        $max = Config::where('key', 'api_description_length')->first()?->value ?: 75;
        mb_substitute_character('none'); // remove non UTF-8 chars, instead of replacing them

        // strip each HTML-field of its tags
        foreach ($curricula as $curriculum) {
            // since these attributes are casted as 'CleanHTML' they will always be wrapped with a 'p'-tag
            $curriculum->mergeCasts(['description' => 'string']);
            $curriculum->description = strip_tags($curriculum->description);

            // terminal-objectives
            foreach ($curriculum->terminalObjectives as $terminal) {
                // same casting problem
                $terminal->mergeCasts([
                    'title' => 'string',
                    'description' => 'string',
                ]);
                $terminal->title = strip_tags($terminal->title);
                $terminal->description = strip_tags($terminal->description);
                // truncate the description if it's too long and add ellipsis
                if (strlen($terminal->description) > $max) {
                    // non UTF-8 chars will only cause problems, if the text gets truncated
                    $terminal->description = mb_convert_encoding(substr($terminal->description, 0, $max), 'UTF-8').'...';
                }

                // enabling-objectives
                foreach ($terminal->enablingObjectives as $enabling) {
                    // same casting problem
                    $enabling->mergeCasts([
                        'title' => 'string',
                        'description' => 'string',
                    ]);
                    $enabling->title = strip_tags($enabling->title);
                    $enabling->description = strip_tags($enabling->description);
                    if (strlen($enabling->description) > $max) {
                        $enabling->description = mb_convert_encoding(substr($enabling->description, 0, $max), 'UTF-8').'...';
                    }
                }
            }
        }

        return response()->json($curricula);
    }

    public function show(Curriculum $curriculum)
    {
        return $curriculum;
    }

    public function getSingleMetadataset(Curriculum $curriculum, Request $request)
    {
        $config = Config::where([
            ['key', '=',  'metadata_password'],
        ])->get();

        if ($config->first() !== null) {
            if ($config->first()->value != $request->query('password')) {
                return response()->json(['error' => 'Not authorized.'], 403);
            }
        } else {
            return response()->json(['error' => 'config (metadata_password) missing'], 420);
        }

        return 'deactivated: please use /v1/curricula/metadatasets?password={password}'; //$this->generateMetadataset($curriculum);
    }

    public function getAllMetadatasets(Request $request)
    {
        LogController::set(get_class($this).'@'.__FUNCTION__);
        $metadata = DB::table('metadatasets')->latest('updated_at')->first();

        return $metadata->metadataset;

    }
}
