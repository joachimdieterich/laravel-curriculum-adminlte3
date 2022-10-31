<?php

namespace App\Http\Controllers;

use App\VariantDefinition;
use Yajra\DataTables\DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VariantDefinitionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->wantsJson() AND request()->has(['term', 'page'])) {
            return getEntriesForSelect2ByModel("App\VariantDefinition");
        }
        if (request()->wantsJson()) {
            return ['definitions' => VariantDefinition::all()];
        }//

        abort_unless(\Gate::allows('curriculum_access'), 403);

        return view('variantdefinitions.index');
    }

    public function list()
    {
        abort_unless(\Gate::allows('curriculum_access'), 403);
        $variant_definition = VariantDefinition::select([
            'id',
            'title',
            'color',
            'css_icon',
        ])->get();

        $edit_gate = \Gate::allows('curriculum_edit');
        $delete_gate = \Gate::allows('curriculum_delete');

        return DataTables::of($variant_definition)
            ->addColumn('action', function ($variant_definition) use ($edit_gate, $delete_gate) {
                $actions = '';
                if ($edit_gate) {
                    $actions .= '<a href="'.route('variantDefinitions.edit', $variant_definition->id).'" '
                        .'id="edit-variantDefinition-'.$variant_definition->id.'" '
                        .'class="btn">'
                        .'<i class="fa fa-pencil-alt"></i>'
                        .'</a>';
                }
                if ($delete_gate) {
                    $actions .= '<button type="button" '
                        .'class="btn text-danger" '
                        .'onclick="destroyDataTableEntry(\'variantDefinitions\','.$variant_definition->id.')">'
                        .'<i class="fa fa-trash"></i></button>';
                }

                return $actions;
            })

            ->addColumn('check', '')
            ->setRowId('id')
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort_unless(\Gate::allows('curriculum_create'), 403);

        return view('variantdefinitions.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        abort_unless(\Gate::allows('subject_create'), 403);
        $new_variant_definition = $this->validateRequest();

        VariantDefinition::create([
            'title'         => $new_variant_definition['title'],
            'description'   => $new_variant_definition['description'],
            'color'         => $new_variant_definition['color'],
            'css_icon'      => $new_variant_definition['css_icon'],
            'owner_id'      => auth()->user()->id,

        ]);

        return redirect()->route('variantDefinitions.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\VariantDefinition  $variantDefinition
     * @return \Illuminate\Http\Response
     */
    public function show(VariantDefinition $variantDefinition)
    {
        abort_unless(\Gate::allows('curriculum_show'), 403);

        return view('variantdefinitions.show', compact('variantDefinition'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\VariantDefinition  $variantDefinition
     * @return \Illuminate\Http\Response
     */
    public function edit(VariantDefinition $variantDefinition)
    {
        abort_unless(\Gate::allows('curriculum_edit'), 403);

        return view('variantdefinitions.edit')
            ->with(compact('variantDefinition'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\VariantDefinition  $variantDefinition
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, VariantDefinition $variantDefinition)
    {
        abort_unless(\Gate::allows('curriculum_edit'), 403);
        $new_variant_definition = $this->validateRequest();

        $variantDefinition->update([
            'title'         => $new_variant_definition['title'],
            'description'   => $new_variant_definition['description'],
            'color'         => $new_variant_definition['color'],
            'css_icon'      => $new_variant_definition['css_icon'],
            'owner_id'      => auth()->user()->id,
        ]);

        return redirect()->route('variantDefinitions.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\VariantDefinition  $variantDefinition
     * @return \Illuminate\Http\Response
     */
    public function destroy(VariantDefinition $variantDefinition)
    {
        abort_unless(\Gate::allows('curriculum_delete'), 403);

        if (
            DB::table('curricula')
            ->whereIn('variants->order', (array) $variantDefinition->id)
            ->get()->count() === 0)
        {
            $variantDefinition->delete();
        }


        return back();
    }

    protected function validateRequest()
    {
        return request()->validate([
            'title'         => 'sometimes|required',
            'description'   => 'sometimes|required',
            'color'         => 'sometimes',
            'css_icon'      => 'sometimes',
        ]);
    }
}
