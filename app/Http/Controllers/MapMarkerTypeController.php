<?php

namespace App\Http\Controllers;

use App\MapMarkerType;
use App\ObjectiveType;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class MapMarkerTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        if (request()->wantsJson()) {
            return [
                'mapMarkerTypes' => MapMarkerType::all()
            ];
        }

        abort_unless(\Gate::allows('map_access'), 403);

        return view('mapmarkertypes.index');
    }

    public function list()
    {
        abort_unless(\Gate::allows('map_access'), 403);
        $mapMarkerTypes = MapMarkerType::select([
            'id',
            'title',
        ])->get();

        $edit_gate = \Gate::allows('map_edit');
        $delete_gate = \Gate::allows('map_delete');

        return DataTables::of($mapMarkerTypes)
            ->addColumn('action', function ($mapMarkerType) use ($edit_gate, $delete_gate) {
                $actions = '';
                if ($edit_gate) {
                    $actions .= '<a href="'.route('mapMarkerTypes.edit', $mapMarkerType->id).'" '
                        .'id="edit-objectivetype-'.$mapMarkerType->id.'" '
                        .'class="btn">'
                        .'<i class="fa fa-pencil-alt"></i>'
                        .'</a>';
                }
                if ($delete_gate) {
                    $actions .= '<button type="button" '
                        .'class="btn text-danger" '
                        .'onclick="destroyDataTableEntry(\'mapMarkerTypes\','.$mapMarkerType->id.')">'
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
        abort_unless(\Gate::allows('map_create'), 403);

        return view('mapmarkertypes.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        abort_unless(\Gate::allows('map_create'), 403);
        $new_type = $this->validateRequest();

        MapMarkerType::create([
            'title' => $new_type['title'],
            'description' => $new_type['description'] ?? null,
            'color' => $new_type['color'],
            'css_icon' => $new_type['css_icon'],
            'owner_id' => auth()->user()->id
        ]);

        return redirect()->route('mapMarkerTypes.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\MapMarkerType  $mapMarkerType
     * @return \Illuminate\Http\Response
     */
    public function show(MapMarkerType $mapMarkerType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\MapMarkerType  $mapMarkerType
     * @return \Illuminate\Http\Response
     */
    public function edit(MapMarkerType $mapMarkerType)
    {
        abort_unless(\Gate::allows('map_edit'), 403);

        return view('mapmarkertypes.edit')
            ->with(compact('mapMarkerType'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\MapMarkerType  $mapMarkerType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MapMarkerType $mapMarkerType)
    {
        abort_unless(\Gate::allows('map_edit'), 403);

        $new_type = $this->validateRequest();
        $mapMarkerType->update([
            'title' => $new_type['title'] ?? $mapMarkerType->title,
            'description' => $new_type['description'] ?? $mapMarkerType->description,
            'color' => $new_type['color'] ?? $mapMarkerType->color,
            'css_icon' => $new_type['css_icon'] ?? $mapMarkerType->css_icon,
            'owner_id' => $new_type['owner_id'] ?? $mapMarkerType->owner_id
        ]);

        return redirect()->route('mapMarkerTypes.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\MapMarkerType  $mapMarkerType
     * @return \Illuminate\Http\Response
     */
    public function destroy(MapMarkerType $mapMarkerType)
    {
        abort_unless(\Gate::allows('map_delete'), 403);

        $mapMarkerType->delete();

        return redirect()->route('mapMarkerTypes.index');
    }

    protected function validateRequest()
    {
        return request()->validate([
            'title' => 'sometimes|required',
            'description' => 'sometimes|nullable',
            'color' => 'sometimes|string',
            'css_icon' => 'sometimes|string',
        ]);
    }
}
