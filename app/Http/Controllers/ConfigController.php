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
        abort_unless(auth()->user()->role()->id == 1, 403);  //only superadmin
        $configs = Config::all();
        return view('configs.index')->with(compact('configs'));
    }

    public function list()
    {
        $users_current_role = auth()->user()->role()->id;
        abort_unless($users_current_role == 1, 403);
        $configs = Config::all() ;

        return DataTables::of($configs)
            ->addColumn('action', function ($configs) use ($users_current_role){
                 $actions  = '';
                    if ($users_current_role == 1){
                        $actions .= '<a href="'.route('configs.edit', $configs->id).'"'
                                    . 'id="edit-config-'.$configs->id.'" '
                                    . 'class="btn p-1">'
                                    . '<i class="fa fa-pencil-alt"></i>'
                                    . '</a>'
                                . '<button type="button" '
                                . 'class="btn text-danger" '
                                . 'onclick="destroyDataTableEntry(\'configs\','.$configs->id.')">'
                                . '<i class="fa fa-trash"></i></button>';
                    }
                return $actions;
            })

            ->addColumn('check', '')
            ->setRowId('id')
            ->setRowAttr([
                'color' => 'primary',
            ])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort_unless(auth()->user()->role()->id == 1, 403);  //only superadmin
        return view('configs.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        abort_unless(auth()->user()->role()->id == 1, 403);  //only superadmin
        $new_config = $this->validateRequest();
        $config = Config::updateOrCreate([
            "key" => $new_config['key'],
            "referenceable_type" => $new_config['referenceable_type'],
            "referenceable_id" => $new_config['referenceable_id'],
            "data_type" => $new_config['data_type'],
        ],
            [
                "value" => $new_config['value'],
            ]
        );

        if (request()->wantsJson()) {
            return ['config' => $config];
        } else {
            return redirect(route('configs.index'));
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Config  $config
     * @return \Illuminate\Http\Response
     */
    public function show(Config $config)
    {
        abort_unless(auth()->user()->role()->id == 1, 403);  //only superadmin
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Config  $config
     * @return \Illuminate\Http\Response
     */
    public function edit(Config $config)
    {
        abort_unless(auth()->user()->role()->id == 1, 403);  //only superadmin

        return view('configs.edit')
                ->with(compact('config'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Config  $config
     * @return \Illuminate\Http\Response
     */
    public function update(Config $config)
    {
        abort_unless(auth()->user()->role()->id == 1, 403);  //only superadmin

        $config->update($this->validateRequest());

        if (request()->wantsJson()) {
            return ['config' => $config];
        } else {
            return redirect(route('configs.index'));
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Config  $config
     * @return \Illuminate\Http\Response
     */
    public function destroy(Config $config)
    {
        abort_unless(auth()->user()->role()->id == 1, 403);  //only superadmin
        $config->delete();

        return back();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function models()
    {
        abort_unless(auth()->user()->role()->id == 1, 403);  //only superadmin
        abort_unless(\Gate::allows('role_access'), 403);
        $roles = Role::select([
            'id',
            'title'
        ])->get();
        $configs = Config::where('key', 'LIKE', '%_limiter')->get();
        return view('configs.models')
            ->with(compact('roles'))
            ->with(compact('configs'));
    }

    protected function validateRequest()
    {
        return request()->validate([
            'key' => 'sometimes|required',
            'value' => 'sometimes',
            'referenceable_type' => 'sometimes',
            'referenceable_id' => 'sometimes',
            'data_type' => 'sometimes',
        ]);
    }
}
