<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_unless(is_admin(), 403);

        //Todo: put exam stats in log-table
        $data =
            ['exam' => [
                'organizations' => DB::table('exams')
                        ->distinct('school_key')
                        ->count('school_key'),
                'all' => DB::table('exam_user')
                        ->select(DB::raw('count(*) as count'))
                        ->first()->count,
                'started' => DB::table('exam_user')
                        ->select(DB::raw('count(*) as count'))
                        ->where('exam_started', '=', '1')
                        ->where('exam_completed_At', '=', NULL)
                        ->first()->count,
                'completed' => DB::table('exam_user')
                        ->select(DB::raw('count(*) as count'))
                        ->where('exam_started', '=', '1')
                        ->where('exam_completed_At', '!=', NULL)
                        ->first()->count
            ]
        ];

        return view('admin.show', $data); //

    }
}
