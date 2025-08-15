<?php

namespace App\Http\Controllers;

use App\Medium;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediumController extends Controller
{
    public String $repository;

    public function __construct(Request $request)
    {
        $this->repository = $request->filled('repository') ? $request->input('repository') : config('medium.repositories.default');
    }

    protected function adapter(string $adapter = null)
    {
        return config('medium.repositories.'.$adapter.'.adapter') ?? config('medium.repositories.'.$this->repository.'.adapter');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        abort_unless(\Gate::allows('medium_access'), 403);

        return $this->adapter()->index($request);
    }

    public function list()
    {
        return $this->adapter()->list();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return $this->adapter()->create();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return $this->adapter()->store($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Medium  $medium
     * @return \Illuminate\Http\Response
     */
    public function show(Medium $medium)
    {
        return $this->adapter($medium->adapter)->show($medium);
    }

    public function thumb(Medium $medium, $size = 200)
    {
        return $this->adapter()->thumb($medium, $size);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Medium  $medium
     * @return \Illuminate\Http\Response
     */
    public function edit(Medium $medium)
    {
        return $this->adapter()->edit($medium);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Medium  $medium
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Medium $medium)
    {
        return $this->adapter()->update($request, $medium);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Medium  $medium
     * @return \Illuminate\Http\Response
     */
    public function destroy(Medium $medium, $subscribable_type = null, $subscribable_id = null)
    {
        return $this->adapter()->destroy($medium, $subscribable_type, $subscribable_id);
    }

    public function massDestroy(Medium $medium)
    {
        abort(404);
    }

    public function checkIfUserHasSubscription($subscription)
    {
        return $this->adapter()->checkIfUserHasSubscription($subscription);
    }
}
