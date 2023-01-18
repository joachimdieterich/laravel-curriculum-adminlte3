<?php

namespace App\Http\Controllers;

use App\Navigator;
use App\NavigatorView;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class NavigatorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_unless(\Gate::allows('navigator_access'), 403);

        return view('navigators.index');
    }

    public function list()
    {
        abort_unless(\Gate::allows('navigator_access'), 403);
        $navigators = Navigator::select([
            'id',
            'title',
            'organization_id',
        ]);

        $edit_gate = \Gate::allows('navigator_edit');
        $delete_gate = \Gate::allows('navigator_delete');

        return DataTables::of($navigators)
            ->addColumn('organization', function ($navigators) {
                return $navigators->organization()->first()->title;
            })
            ->addColumn('action', function ($navigators) use ($edit_gate, $delete_gate) {
                $actions = '';
                if ($edit_gate) {
                    $actions .= '<a href="'.route('navigators.edit', $navigators->id).'" '
                                    .'id="edit-navigator-'.$navigators->id.'" '
                                    .'class="btn">'
                                    .'<i class="fa fa-pencil-alt"></i>'
                                    .'</a>';
                }
                if ($delete_gate) {
                    $actions .= '<button type="button" '
                                .'class="btn text-danger" '
                                .'onclick="destroyDataTableEntry(\'navigators\','.$navigators->id.')">'
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
        abort_unless(\Gate::allows('navigator_create'), 403);

        return view('navigators.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        abort_unless(\Gate::allows('navigator_create'), 403);
        $new_navigator = $this->validateRequest();

        $navigator = Navigator::firstOrCreate([
            'title' => $new_navigator['title'],
            'organization_id' => format_select_input($new_navigator['organization_id']),
        ]);

        // axios call?
        if (request()->wantsJson()) {
            return ['message' => $navigator->path()];
        }

        return redirect($navigator->path());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Navigator  $navigator
     * @return \Illuminate\Http\Response
     */
    public function show(Navigator $navigator)
    {
        $navigators = Navigator::where('id', $navigator->id)->get()->first();
        $views = NavigatorView::where('navigator_id', $navigator->id)
                                    ->with(['items'])
                                    ->get()->first();
        $breadcrumbs = $this->breadcrumbs($views);

        LogController::set(get_class($this).'@'.__FUNCTION__, $navigator->id);

        return view('navigators.show')
                ->with(compact('navigators'))
                ->with(compact('views'))
                ->with(compact('breadcrumbs'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Navigator  $navigator
     * @return \Illuminate\Http\Response
     */
    public function edit(Navigator $navigator)
    {
        abort_unless(\Gate::allows('navigator_edit'), 403);
        $organizations = \App\Organization::where('id', $navigator->organization_id)->first()->get();

        return view('navigators.edit')
                ->with(compact('navigator'))
                ->with(compact('organizations'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Navigator  $navigator
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Navigator $navigator)
    {
        abort_unless(\Gate::allows('navigator_edit'), 403);

        $navigator->update([
            'title' => $request['title'],
            'organization_id' => format_select_input($request['organization_id']),
        ]);

        return redirect()->route('navigators.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Navigator  $navigator
     * @return \Illuminate\Http\Response
     */
    public function destroy(Navigator $navigator)
    {
        abort_unless(\Gate::allows('navigator_delete'), 403);

        $navigator->delete();

        return back();
    }

    /**
     * Generate breadcrumb
     *
     * @param  NavigatorView  $view
     * @return array
     */
    protected function breadcrumbs($view)
    {
        $entries = [];
        if (isset($view->navigator)) {
            $first_view = NavigatorView::where('navigator_id', $view->navigator->id)->get()->first();
            $entries[] = ['href' => '/navigators/'.$view->navigator->id.'/'.$view->id, 'title' => $view->title];
            $current_view = $view;
            while ($current_view->id != $first_view->id) {
                $current_item = NavigatorItem::where('referenceable_type', 'App\NavigatorView')
                                             ->where('referenceable_id', $current_view->id)->get()->first();
                $current_view = NavigatorView::where('id', $current_item->navigator_view_id)
                                        ->where('navigator_id', $view->navigator->id)
                                        ->get()->first();
                $entries[] = ['href' => '/navigators/'.$view->navigator->id.'/'.$current_view->id, 'title' => $current_view->title];
            }
        }

        return array_reverse($entries);
    }

    protected function validateRequest()
    {
        return request()->validate([
            'title' => 'sometimes|required',
            'organization_id' => 'sometimes',
        ]);
    }
}
