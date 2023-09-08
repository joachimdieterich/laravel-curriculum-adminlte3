<?php

namespace App\Http\Controllers;

use App\Kanban;
use App\KanbanItem;
use App\KanbanStatus;
use App\KanbanSubscription;
use App\Medium;
use App\MediumSubscription;
use App\Organization;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maize\Markable\Models\Like;
use Yajra\DataTables\DataTables;

class KanbanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_unless(\Gate::allows('kanban_access'), 403);

        return view('kanbans.index');
    }

    public function userKanbans($withOwned = true)
    {

        $userCanSee = auth()->user()->kanbans;

        if (auth()->user()->sharing_token !== null)  //tokenuser? only return subscriptions
        {
            return $userCanSee;
        }

        foreach (auth()->user()->groups as $group) {
            $userCanSee = $userCanSee->merge($group->kanbans);
        }
        $organization = Organization::find(auth()->user()->current_organization_id)->kanbans;
        $userCanSee = $userCanSee->merge($organization);

        if ($withOwned)
        {
            $owned = Kanban::where('owner_id', auth()->user()->id)->get();
            $userCanSee = $userCanSee->merge($owned);

        }

        return $userCanSee->unique();
    }

    public function list(Request $request)
    {
        abort_unless(\Gate::allows('kanban_access'), 403);

        switch ($request->filter)
        {
            case 'owner':            $kanbans = Kanban::where('owner_id', auth()->user()->id)->get();
                break;
            case 'shared_with_me':   $kanbans = $this->userKanbans(false);
                break;
            case 'shared_by_me':     $kanbans = Kanban::where('owner_id', auth()->user()->id)->whereHas('subscriptions')->get();
                break;
            case 'all':
            default:                $kanbans = $this->userKanbans();
                break;
        }

        return empty($kanbans) ? '' : DataTables::of($kanbans)
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
        abort_unless(\Gate::allows('kanban_create'), 403);

        return view('kanbans.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        abort_unless(\Gate::allows('kanban_create'), 403);
        $new_kanban = $this->validateRequest();

        $kanban = Kanban::create([
            'title' => $new_kanban['title'],
            'description' => $new_kanban['description'],
            'color' => $new_kanban['color'] ?? '#2980B9',
            'medium_id' => $new_kanban['medium_id'] ?? null,
            'commentable' => isset($input['commentable']) ? 1 : '0',
            'auto_refresh' => isset($input['auto_refresh']) ? 1 : '0',
            'only_edit_owned_items' => isset($input['only_edit_owned_items']) ? 1 : '0',
            'owner_id' => auth()->user()->id,
        ]);

        LogController::set(get_class($this).'@'.__FUNCTION__);
        // axios call?
        if (request()->wantsJson()) {
            return ['message' => $kanban->path()];
        }

        return redirect($kanban->path());
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Kanban  $kanban
     * @return \Illuminate\Http\Response
     */
    public function show(Kanban $kanban, $token = null)
    {

        //abort_unless((\Gate::allows('kanban_show') and $this->userKanbans()->contains($kanban->id)), 403);
        abort_unless(/*\Gate::allows('kanban_show') and*/ $kanban->isAccessible(), 403); // don't use kanban_show -> bugfix for 403 problem on tokens.

        $kanban = $this->getKanbanWithRelations($kanban);

        if ($token == null)
        {
            $may_edit = $kanban->isEditable();
        }
        else
        {
            $may_edit = $kanban->isEditable(auth()->user()->id, $token);
        }

        $is_shared = $kanban->owner_id !== auth()->user()->id; //Auth::user()->sharing_token !== null;
        $is_pusher_active = env('PUSHER_APP_ACTIVE');

        LogController::set(get_class($this).'@'.__FUNCTION__, $kanban->id);

        return view('kanbans.show')
            ->with(compact('kanban', 'may_edit', 'is_shared', 'is_pusher_active'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Kanban  $kanban
     * @return \Illuminate\Http\Response
     */
    public function edit(Kanban $kanban)
    {
        abort_unless((\Gate::allows('kanban_edit') and $kanban->isAccessible()), 403);

        $kanban = $this->getKanbanWithRelations($kanban);

        LogController::set(get_class($this).'@'.__FUNCTION__);

        return view('kanbans.edit')
            ->with(compact('kanban'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Kanban  $kanban
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Kanban $kanban)
    {
        abort_unless((\Gate::allows('kanban_edit') and $kanban->isAccessible()), 403);
        $input = $this->validateRequest();
        $kanban->update([
            'title' => $input['title'] ?? $kanban->title ,
            'description' => $input['description'] ?? $kanban->title,
            'color' => $input['color'] ?? $kanban->color,
            'medium_id' => $input['medium_id'] ?? $kanban->medium_id,
            'commentable' => isset($input['commentable']) ? 1 : '0',
            'auto_refresh' => isset($input['auto_refresh']) ? 1 : '0',
            'only_edit_owned_items' => isset($input['only_edit_owned_items']) ? 1 : '0',
            'owner_id' => auth()->user()->id,
        ]);

        return redirect(route('kanbans.show', ['kanban' => $kanban]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Kanban  $kanban
     * @return \Illuminate\Http\Response
     */
    public function destroy(Kanban $kanban)
    {
        abort_unless((\Gate::allows('kanban_delete') and $kanban->isAccessible()), 403);

        //delete relations
        $kanban->items()->delete();
        $kanban->statuses()->delete();
        $kanban->subscriptions()->delete();

        if ($kanban->delete()) {
            return $this->list();
        }
    }

    public function updateKanbansColor(Request $request)
    {
        $kanban = Kanban::where('id', $request->id)->first();
        abort_unless((\Gate::allows('kanban_create') and $kanban->isAccessible()), 403);

        if (! $kanban) {
            return;
        }
        $kanban->color = $request->color;
        if ($kanban->color == '#DDE6E8') {
            $kanban->color = '#F4F4F4';
        }
        $kanban->save();

        if (request()->wantsJson()) {
            if (!pusher_event(new \App\Events\Kanbans\KanbanColorUpdatedEvent($kanban)))
            {
                return [
                    'message' => $kanban->color
                ];
            }
        }
    }

    public function getKanbansColor($id)
    {
        $kanban = Kanban::where('id', $id)->first();
        if ($kanban->color != null && $kanban->color != '#F4F4F4') {
            return [
                'hex' => $kanban->color,
                'rgba' => $this->transformHexColorToRgba($kanban->color),
            ];
        }

        return [
            'hex' => '#DDE6E8',
            'rgba' => $this->transformHexColorToRgba('#F4F4F4'),
        ];
    }

    public function exportKanbanCsv(Kanban $kanban)
    {
        $h[] = [
            trans('global.kanbanStatus.title'),
            trans('global.kanbanItem.fields.title'),
            trans('global.kanbanItem.fields.description'),
            trans('global.date'),
            trans('global.owner'),
        ];
        $filename = uniqid().'.csv';
        $fp = fopen($filename, 'w');

        foreach ($h as $field) {
            fputcsv($fp, $field);
        }

        foreach ($kanban->statuses as $status) {
            foreach ($status->items as $k) {
                fputcsv(
                    $fp,
                    [
                        $status->title,
                        $k->title,
                        strip_tags(
                            preg_replace('~<a href="(?!https?://)[^"]+">(.*?)</a>~', '$1', $k->description)
                        ),
                        $k->created_at,
                        $k->owner->fullName()
                    ]
                );
            }
        }

        fclose($fp);

        $headers = [
            'Content-Type: text/csv',
        ];

        return response()->download($filename, $kanban->title.'.csv', $headers)->deleteFileAfterSend(true);
    }

    public function exportKanbanPdf(Kanban $kanban)
    {
        $pdf = PDF::loadView('exports.kanban.pdf', ['kanban' => $kanban])->setPaper('a4', 'landscape');
        //return view('exports.kanban.pdf', ['kanban' => $kanban]);
        return $pdf->download($kanban->title.'.pdf');
    }

    private function transformHexColorToRgba($color)
    {
        [$r, $g, $b] = sscanf($color, '#%02x%02x%02x');

        return 'rgba('.$r.', '.$g.', '.$b.', .7)';
    }

    public function getKanbanWithRelations(Kanban $kanban)
    {
        return $kanban->with([
            'statuses',
            'statuses.items' => function ($query) use ($kanban) {
                $query->where('kanban_id', $kanban->id)
                    ->with([
                        'comments',
                        'comments.user',
                        'comments.likes',
                        'likes',
                        'mediaSubscriptions.medium',
                        'owner',
                    ])
                    ->orderBy('order_id');
            },
            'medium',
        ])->where('id', $kanban->id)->get()->first();
    }

    public function getKanbanByToken(Kanban $kanban, Request $request)
    {
        if (Auth::user() == null) {       //if no user is authenticated authenticate guest
            LogController::set('guestLogin');
            LogController::setStatistics();
            Auth::loginUsingId((env('GUEST_USER')), true);
        }

        $input = $this->validateRequest();

        $subscription = KanbanSubscription::where('sharing_token',$input['sharing_token'] )->get()->first();
        if ($subscription->due_date) {
            $now = Carbon::now();
            $due_date = Carbon::parse($subscription->due_date);
            if ($due_date < $now) {
                abort(410, 'Dieser Link ist nicht mehr gültig');
            }
        }

        return $this->show($kanban, $input['sharing_token']);

    }

    public function copyKanban(Kanban $kanban, Request $request)
    {
        $kanbanCopy = Kanban::create([
            'title' => $kanban->title . '_' . date('Y.m.d_H:i:s'),
            'description' => $kanban->description,
            'color' => $kanban->color,
            'medium_id' => $kanban->medium_id,
            'commentable' => $kanban->commentable,
            'auto_refresh' => $kanban->auto_refresh,
            'only_edit_owned_items' => $kanban->only_edit_owned_items,
            'owner_id' => auth()->user()->id,
        ]);

        $statuses = $kanban->statuses;
        foreach ($statuses as $status)
        {
            $statusCopy = KanbanStatus::Create([
                'title' => $status->title,
                'order_id' => $status->order_id,
                'kanban_id' => $kanbanCopy->id,
                'locked' => $kanbanCopy->locked ?? false,
                'visibility' => $kanbanCopy->visibility ?? true,
                'owner_id' => auth()->user()->id,
            ]);

            foreach ($status->items as $item)
            {
               $kanbanItemCopy = KanbanItem::Create([
                    'title'             => $item->title,
                    'description'       => $item->description,
                    'order_id'          => $item->order_id,
                    'kanban_id'         => $kanbanCopy->id,
                    'kanban_status_id'  => $statusCopy->id,
                    'color'             => $item->color,
                    'due_date'          => $item->due_date,
                    'owner_id'          => auth()->user()->id,
                ]);
               // dump($item->mediaSubscriptions);
               foreach ($item->mediaSubscriptions as $mediaSubscription)
               {
                   $subscribe = MediumSubscription::Create([
                       'medium_id' => $mediaSubscription->medium_id,
                       'subscribable_type' => $mediaSubscription->subscribable_type,
                       'subscribable_id' => $kanbanItemCopy->id,
                       'sharing_level_id' => $mediaSubscription->sharing_level_id,
                       'visibility' => $mediaSubscription->visibility,
                       'additional_data' => $mediaSubscription->additional_data,
                       'owner_id' => auth()->user()->id,
                   ]);
               }
            }
        }
        return redirect('/kanbans');
    }

    protected function validateRequest()
    {
        return request()->validate([
            'title' => 'sometimes|required',
            'description' => 'sometimes',
            'medium_id' => 'sometimes',
            'commentable' => 'sometimes',
            'auto_refresh' => 'sometimes',
            'color' => 'sometimes',
            'filter' => 'sometimes',
            'only_edit_owned_items' => 'sometimes',
            'sharing_token' => 'sometimes'
        ]);
    }
}
