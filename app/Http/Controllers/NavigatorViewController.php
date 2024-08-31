<?php

namespace App\Http\Controllers;

use App\Navigator;
use App\NavigatorItem;
use App\NavigatorView;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Yajra\DataTables\DataTables;

class NavigatorViewController extends Controller
{

    public function list(NavigatorView $navigator_view)
    {
        abort_unless(\Gate::allows('navigator_access'), 403);
        $items = NavigatorItem::select([
            'id',
            'title',
            'description',
            'navigator_view_id',
            'referenceable_type',
            'referenceable_id',
            'position',
            'css_class',
            'visibility',
        ])->where('navigator_view_id', $navigator_view->id)
            ->with(['referenceable']);

        return DataTables::of($items)
            ->addColumn('check', '')
            ->addColumn('medium_id', function ($items)  {
                if ($items->medium) {
                    return $items->medium->id;
                }
            })
            ->addColumn('url', function ($items)  {

               switch ($items->referenceable_type){
                   case 'App\NavigatorView':
                       return '/navigatorViews' ;
                       break;
                   case 'App\Curriculum':
                       return '/curricula' ;
                       break;
                   case 'App\Content':
                       return '/contents';
                       break;
               }
            })
            ->setRowId('referenceable_id')
            ->make(true);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        abort_unless(\Gate::allows('navigator_create'), 403);

        return view('navigators.views.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        abort_unless(\Gate::allows('navigator_create'), 403);
        $new_navigator_item = $this->validateRequest();

        $navigator_view = NavigatorView::Create([
            'title'         => $new_navigator_item['title'],
            'description'   => $new_navigator_item['description'],
            'navigator_id'  => $new_navigator_item['navigator_id'],
        ]);

        return redirect()->route('navigator.view', ['navigator' => $new_navigator_item['navigator_id'], 'navigator_view' => $navigator_view->id]);
    }

    /**
     * Display the specified resource.
     *
     * @param  NavigatorView  $navigatorView
     * @return Response
     */
    public function show(NavigatorView $navigatorView)
    {
        $navigator = $navigatorView->navigator;

        $view = NavigatorView::where('id', $navigatorView->id)
                    ->where('navigator_id', $navigator->id)
                    ->get()
                    ->first();
        $breadcrumbs = $this->breadcrumbs($view);

        return view('navigators.show')
                ->with(compact('navigator'))
                ->with(compact('view'))
                ->with(compact('breadcrumbs'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  NavigatorView  $navigatorView
     * @return Response
     */
    public function edit(NavigatorView $navigatorView)
    {
        abort_unless(\Gate::allows('navigator_edit'), 403);

        return view('navigators.views.edit')
                    ->with(compact('navigatorView'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  NavigatorView  $navigatorView
     * @return Response
     */
    public function update(Request $request, NavigatorView $navigatorView)
    {
        abort_unless(\Gate::allows('navigator_edit'), 403);

        $navigatorView->update([
            'title' => $request['title'],
            'description' => $request['description'],
        ]);

        return redirect($navigatorView->path());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  NavigatorView  $navigatorView
     * @return Response
     */
    public function destroy(NavigatorView $navigatorView)
    {
        abort_unless(\Gate::allows('navigator_delete'), 403);
        $navigatorItem = NavigatorItem::where('referenceable_type', 'App\NavigatorView')
                                      ->where('referenceable_id', $navigatorView->id)->first();
        if ($navigatorItem != null) {
            $navigatorItem->delete();
        }
        $navigator_id = $navigatorView->navigator_id;
        $navigatorView->delete();

        return redirect()->route('navigators.show', $navigator_id);
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

        return array_reverse($entries);
    }

    protected function validateRequest()
    {
        return request()->validate([
            'title'             => 'sometimes',
            'description'       => 'sometimes',
            'navigator_id'      => 'sometimes',

        ]);
    }
}
