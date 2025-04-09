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

        LogController::set(get_class($this).'@'.__FUNCTION__);
        // might need to be broken down in chunks, since there's a lot of data
        return Curriculum::select('id', 'title', 'description', 'grade_id', 'subject_id', 'organization_type_id', 'type_id')
            ->where('type_id', 1)
            ->with([
                'grade',
                'subject',
                'organization_type',
                'terminalObjectives',
                'terminalObjectives.enablingObjectives'
            ])
            ->get();
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
