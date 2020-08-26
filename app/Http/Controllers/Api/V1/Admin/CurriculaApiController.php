<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Curriculum;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Config;

class CurriculaApiController extends Controller
{
    public function index()
    {
        $curricula = Curriculum::all();

        return $curricula;
    }


    public function show(Curriculum $curriculum)
    {

        return $curriculum;

    }

    public function getSingleMetadataset(Curriculum $curriculum, Request $request)
    {
        $metadata_password = Config::where([
                ['key', '=',  'metadata_password']
            ])->get()->first()->value;
        if ($metadata_password != $request->query('password'))
        {
            return 'forbidden';
        }

        return 'deactivated: please use /v1/curricula/metadatasets?password={password}';//$this->generateMetadataset($curriculum);
    }

    public function getAllMetadatasets(Request $request)
    {
        $metadata_password = Config::where([
                ['key', '=',  'metadata_password']
            ])->get()->first()->value;
        if ($metadata_password != $request->query('password'))
        {
            return 'forbidden';
        }

        //use 'php artisan curriculum:metadataset [Versionsnumber]' to generate metadataset
        $metadata =  DB::table('metadatasets')->latest('updated_at')->first();
        return $metadata->metadataset;

    }
}
