<?php

namespace App\Http\Controllers;

use App\Config;
use App\Role;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ConfigController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_unless(is_admin(), 403);  //only superadmin
        $configs = Config::all();

        return view('configs.index')->with(compact('configs'));
    }

    public function list()
    {
        abort_unless(is_admin(), 403);

        return DataTables::of(Config::all())->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort(403);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        abort_unless(is_admin(), 403);  //only superadmin

        $new_config = $this->validateRequest();
        $config = Config::updateOrCreate(
            [
                'key'                   => $new_config['key'],
                'referenceable_type'    => $new_config['referenceable_type'],
                'referenceable_id'      => $new_config['referenceable_id'],
                'data_type'             => $new_config['data_type'],
            ],
            [
                'value' => $new_config['value'],
            ]
        );

        return $config;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Config  $config
     * @return \Illuminate\Http\Response
     */
    public function show(Config $config)
    {
        abort_unless(is_admin(), 403);  //only superadmin
        return view('configs.show')
            ->with(compact('config'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Config  $config
     * @return \Illuminate\Http\Response
     */
    public function edit(Config $config)
    {
       abort(403);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Config  $config
     * @return \Illuminate\Http\Response
     */
    public function update(Config $config)
    {
        abort_unless(is_admin(), 403);  //only superadmin

        $config->update($this->validateRequest());

        return $config;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Config  $config
     * @return \Illuminate\Http\Response
     */
    public function destroy(Config $config)
    {
        abort_unless(is_admin(), 403);  //only superadmin

        return $config->delete();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function models()
    {
        abort_unless(is_admin(), 403);  //only superadmin

        $roles = Role::get(['id', 'title']);
        $configs = Config::where('key', 'LIKE', '%_limiter')->get();

        return view('configs.models')
            ->with(compact('roles'))
            ->with(compact('configs'));
    }

    protected function validateRequest()
    {
        return request()->validate([
            'key'                   => 'sometimes|required',
            'value'                 => 'sometimes',
            'referenceable_type'    => 'sometimes',
            'referenceable_id'      => 'sometimes',
            'data_type'             => 'sometimes',
        ]);
    }
}