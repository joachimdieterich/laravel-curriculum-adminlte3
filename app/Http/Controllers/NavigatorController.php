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
        abort_unless(\Gate::allows('navigator_show'), 403);

        return view('navigators.index');
    }

    public function list()
    {
        abort_unless(\Gate::allows('navigator_show'), 403);
        $navigators = Navigator::select([
            'id',
            'title',
            'organization_id',
        ]);

        return DataTables::of($navigators)
            ->addColumn('organization', function ($navigators) {
                return $navigators->organization()->first()->title;
            })
            ->addColumn('url', function ($navigators)  {
                return '/navigatorViews/' . $navigators->views()->first()?->id ;
            })
            ->addColumn('check', '')
            ->setRowId('id')
            ->make(true);
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
        $navigator_view =  NavigatorView::firstOrCreate([
            'title' => $new_navigator['title'],
            'description' => 'Home',
            'navigator_id' => $navigator->id,
        ]);

        $navigator->url = '/navigatorViews/' . $navigator_view->id;

        if (request()->wantsJson()) {
            return $navigator;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Navigator  $navigator
     * @return \Illuminate\Http\Response
     */
    public function show(Navigator $navigator)
    {
        abort_unless(\Gate::allows('navigator_show'), 403);
        $model = 'navigator'; //strtolower(class_basename( $navigator ));


        $view = NavigatorView::where('navigator_id', $navigator->id)
                                    /*->with(['items'])*/
                                    ->get()->first();
        $breadcrumbs = $this->breadcrumbs($view);

        LogController::set(get_class($this).'@'.__FUNCTION__, $navigator->id);

        return view('navigators.show')
                ->with(compact('navigator'))
                ->with(compact('view'))
                ->with(compact('breadcrumbs'))
                ->with(compact('model'));
    }

    public function listViews(Navigator $navigator)
    {
        abort_unless(\Gate::allows('navigator_access'), 403);
        $navigatorViews = NavigatorView::where('navigator_id', $navigator->id)
            ->select([
                'id',
                'title',
                'description',
                'navigator_id',
            ]);

        return DataTables::of($navigatorViews)
            ->addColumn('check', '')
            ->setRowId('id')
            ->make(true);
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

        return $navigator;
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

        $navigator->views()->delete(); //delete views

        return $navigator->delete();
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
                $entries[] = ['url' => '/navigators/'.$view->navigator->id.'/'.$current_view->id, 'title' => $current_view->title];
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
