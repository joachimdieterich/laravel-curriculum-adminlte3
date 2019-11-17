<?php

namespace App\Http\Controllers;

use App\Navigator;
use App\NavigatorView;
use App\NavigatorItem;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class NavigatorViewController extends Controller
{

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
                                        
        $navigator_view= NavigatorView::Create([
            'title'         => $new_navigator_item['title'],
            'description'   => $new_navigator_item['description'],
            'navigator_id'  => $new_navigator_item['navigator_id']
        ]);
                                        
        
        return redirect()->route("navigator.view", ['navigator' => $new_navigator_item['navigator_id'], 'navigator_view' => $navigator_view->id]);
    }

    /**
     * Display the specified resource.
     *
     * @param  NavigatorView  $navigatorView
     * @return Response
     */
    public function show(Navigator $navigator, NavigatorView $navigator_view)
    {   
        
        $navigators = Navigator::where('id', $navigator->id)->get()->first();
        $views      = NavigatorView::where('id', $navigator_view->id)
                                    ->where('navigator_id', $navigator->id)
                                    ->with(['items'])
                                    ->get()->first();
        return view('navigators.show')
                ->with(compact('navigators'))
                ->with(compact('views'));
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
            'description' => $request['description']
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
        if ($navigatorItem != null)
        {
            $navigatorItem->delete();
        }    
        $navigator_id = $navigatorView->navigator_id;
        $navigatorView->delete();
        

        return redirect()->route('navigators.show', $navigator_id);
    }
    
    protected function validateRequest()
    {   
        
        return request()->validate([
            'title'             => 'sometimes',
            'description'       => 'sometimes',
            'navigator_id'      => 'sometimes'
           
        ]);
    }
}
