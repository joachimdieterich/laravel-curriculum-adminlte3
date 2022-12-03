<?php

namespace App\Http\Controllers;

use App\Variant;
use Illuminate\Http\Request;

class VariantController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        abort_unless((\Gate::allows('objective_create') /*AND $this->parent->isAccessible()*/), 403);
        $input = $this->validateRequest();
        $variant = Variant::updateOrCreate([
            'referenceable_type' => $input['referenceable_type'],
            'referenceable_id' => $input['referenceable_id'],
            'variant_definition_id' => $input['variant_definition_id'],

        ], [
            'title' => $input['title'],
            'description' => $input['description'],
            'owner_id' => auth()->user()->id,
        ]);
        $variant->save();

        if (request()->wantsJson()) {
            return ['variant' => $variant];
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Variant  $variant
     * @return \Illuminate\Http\Response
     */
    public function show(Variant $variant)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Variant  $variant
     * @return \Illuminate\Http\Response
     */
    public function edit(Variant $variant)
    {
        abort_unless((\Gate::allows('objective_create') /*AND $this->parent->isAccessible()*/), 403);
        $input = $this->validateRequest();
        $variant = Variant::updateOrCreate([
            'referenceable_type' => $input['referenceable_type'],
            'referenceable_id' => $input['referenceable_id'],
            'variant_definition_id' => $input['variant_definition_id'],

        ], [
            'title' => $input['title'],
            'description' => $input['description'],
            'owner_id' => auth()->user()->id,
        ]);
        $variant->save();

        if (request()->wantsJson()) {
            return ['variant' => $variant];
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Variant  $variant
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Variant $variant)
    {
        abort_unless((\Gate::allows('objective_edit') /*AND $this->parent->isAccessible()*/), 403);

        $input = $this->validateRequest();
        $variant = Variant::updateOrCreate([
            'id' => $input['id'],
            'referenceable_type' => $input['referenceable_type'],
            'referenceable_id' => $input['referenceable_id'],
            'variant_definition_id' => $input['variant_definition_id'],

        ], [
            'title' => $input['title'],
            'description' => $input['description'],
            'owner_id' => auth()->user()->id,
        ]);
        $variant->save();

        if (request()->wantsJson()) {
            return ['variant' => $variant];
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Variant  $variant
     * @return \Illuminate\Http\Response
     */
    public function destroy(Variant $variant)
    {
        //
    }

    protected function validateRequest()
    {
        return request()->validate([
            'id' => 'sometimes',
            'title' => 'sometimes|required',
            'description' => 'sometimes',
            'referenceable_type' => 'required',
            'referenceable_id' => 'required',
            'variant_definition_id' => 'required',
        ]);
    }
}
