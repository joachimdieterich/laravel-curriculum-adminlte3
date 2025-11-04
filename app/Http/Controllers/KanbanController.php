<?php

namespace App\Http\Controllers;

use App\Kanban;
use App\KanbanItem;
use App\KanbanStatus;
use App\KanbanSubscription;
use App\MediumSubscription;
use App\Organization;
use App\Tag;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Gate;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\View\View;
use Yajra\DataTables\DataTables;

class KanbanController extends Controller
{
    /**
     *  Display a listing of the resource.
     *
     * @return Factory|\Illuminate\Contracts\View\View|Application|JsonResponse|View|object
     */
    public function index()
    {
        abort_unless(Gate::allows('kanban_access'), 403);
        if (request()->wantsJson()) {
            return getEntriesForSelect2ByCollection(
                $this->getKanbans(),
                'kanbans.'
            );
        }

        return view('kanbans.index');
    }

    public function getKanbans($withOwned = true)
    {
        $kanbans = Kanban::with('subscriptions')
            ->whereHas('subscriptions', function ($query) {
                $query->where(
                    function ($query) {
                        $query->where('subscribable_type', 'App\\Organization')->where(
                            'subscribable_id',
                            auth()->user()->current_organization_id
                        );
                    }
                )->orWhere(
                    function ($query) {
                        $query->where('subscribable_type', 'App\\Group')->whereIn(
                            'subscribable_id',
                            auth()->user()->groups->pluck('id')
                        );
                    }
                )->orWhere(
                    function ($query) {
                        $query->where('subscribable_type', 'App\\User')->where(
                            'subscribable_id',
                            auth()->user()->id
                        );
                    }
                );
            })->orWhere('owner_id', auth()->user()->id);

        if ($withOwned) {
            $kanbans = $kanbans->orWhere('owner_id', auth()->user()->id);
        }

        return $kanbans;
    }

    public function userKanbans($withOwned = true)
    {
        $tags = Tag::select()->whereIn('id', request('tags') ?? [])->get();
        $userCanSee = auth()->user()->kanbans()->withAllTags($tags)->get();

        //tokenuser? only return subscriptions
        if (auth()->user()->sharing_token !== null) {
            return $userCanSee;
        }

        foreach (auth()->user()->groups as $group) {
            $userCanSee = $userCanSee->merge($group->kanbans()->withAllTags($tags));
        }
        $organization = Organization::find(auth()->user()->current_organization_id)->kanbans()->withAllTags($tags);
        $userCanSee   = $userCanSee->merge($organization);

        if ($withOwned) {
            $owned      = Kanban::where('owner_id', auth()->user()->id)->withAllTags($tags)->get();
            $userCanSee = $userCanSee->merge($owned);
        }

        return $userCanSee->unique();
    }

    public function list(Request $request)
    {
        abort_unless(Gate::allows('kanban_access'), 403);

        $tags = Tag::select()->whereIn('id', request('tags') ?? [])->get();

        if (request()->has(['group_id'])) {
            $request  = request()->validate(
                [
                    'group_id' => 'required',
                ]
            );
            $group_id = $request['group_id'];
            $kanbans  = Kanban::with('subscriptions')
                ->whereHas('subscriptions', function ($query) use ($group_id) {
                    $query->where(
                        function ($query) use ($group_id) {
                            $query->where('subscribable_type', 'App\\Group')
                                ->where('subscribable_id', $group_id);
                        }
                    );
                });
            $kanbans->withAllTags($tags)->get();
        } else {
            switch ($request->filter) {
                case 'owner':
                    $kanbans = Kanban::where('owner_id', auth()->user()->id)->withAllTags($tags)->get();
                    break;
                case 'shared_with_me':
                    $kanbans = $this->userKanbans(false);
                    break;
                case 'shared_by_me':
                    $kanbans = Kanban::where('owner_id', auth()->user()->id)->whereHas('subscriptions')->withAllTags($tags)->get();
                    break;
                case 'all':
                default:
                    $kanbans = $this->userKanbans();
                    break;
            }
        }

        return empty($kanbans) ? '' : DataTables::of($kanbans)
            ->addColumn('tags', function ($kanbans) {
                return $kanbans->tags->toArray();
            })
            ->setRowId('id')
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        abort(405);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Application|RedirectResponse|Redirector|object
     */
    public function store(Request $request)
    {
        abort_unless(Gate::allows('kanban_create'), 403);
        $new_kanban = $this->validateRequest();

        $kanban = Kanban::create([
            'title'                 => $new_kanban['title'],
            'description'           => $new_kanban['description'],
            'color'                 => $new_kanban['color'] ?? '#2980B9',
            'medium_id'             => $new_kanban['medium_id'] ?? null,
            'commentable'           => $new_kanban['commentable'],
            'auto_refresh'          => $new_kanban['auto_refresh'],
            'only_edit_owned_items' => $new_kanban['only_edit_owned_items'],
            'collapse_items'        => $new_kanban['collapse_items'],
            'allow_copy'            => $new_kanban['allow_copy'],
            'owner_id'              => auth()->user()->id,
        ]);
        $kanban->tags()->sync($request->input('tags'));

        LogController::set(get_class($this) . '@' . __FUNCTION__);
        if (request()->wantsJson()) {
            return $kanban;
        }

        return redirect($kanban->path());
    }


    /**Display the specified resource.
     *
     * @param Kanban $kanban
     * @param        $token
     * @return Factory|\Illuminate\Contracts\View\View|Application|View|object
     */
    public function show(Kanban $kanban, $token = null)
    {
        abort_if(
            $token == null and ( // token-links are subscribed to the guest-user
                auth()->user()->id == env(
                    'GUEST_USER'
                ) // so we need to check if a guest-user is accessing through a token
                or !$kanban->isAccessible()
            ),
            403
        );

        $kanban = $kanban->withRelations();

        $may_edit = $kanban->isEditable(auth()->user()->id, $token);

        $is_shared           = $kanban->owner_id !== auth()->user()->id; //Auth::user()->sharing_token !== null;
        $is_websocket_active = env('WEBSOCKET_APP_ACTIVE');

        LogController::set(get_class($this) . '@' . __FUNCTION__, $kanban->id);

        return view('kanbans.show')
            ->with(compact('kanban', 'may_edit', 'is_shared', 'is_websocket_active'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Kanban $kanban
     * @return never
     */
    public function edit(Kanban $kanban): never
    {
        abort(405);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Kanban  $kanban
     * @return Kanban|Application|RedirectResponse|Redirector|object|null
     */
    public function update(Request $request, Kanban $kanban)
    {
        abort_unless((Gate::allows('kanban_edit') and $kanban->isAccessible()), 403);
        $input = $this->validateRequest();
        $kanban->update([
            'title'                 => $input['title'] ?? $kanban->title,
            'description'           => $input['description'],
            'color'                 => $input['color'] ?? $kanban->color,
            'medium_id'             => $input['medium_id'],
            // ?? $kanban->medium_id, -> to get medium unsubscribe working
            'commentable'           => $input['commentable'],
            'auto_refresh'          => $input['auto_refresh'],
            'only_edit_owned_items' => $input['only_edit_owned_items'],
            'collapse_items'        => $input['collapse_items'],
            'allow_copy'            => $input['allow_copy'],
            'owner_id'              => is_admin() ? $input['owner_id'] : $kanban->owner_id,
        ]);
        $kanban->tags()->sync($request->input('tags'));

        if (request()->wantsJson()) {
            return $kanban->withRelations();
        }

        return redirect(route('kanbans.show', ['kanban' => $kanban]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Kanban $kanban
     * @return Response
     */
    public function destroy(Kanban $kanban)
    {
        abort_unless((Gate::allows('kanban_delete') and $kanban->isAccessible()), 403);

        // delete medium if edusharing-medium
        if ($kanban->medium_id) {
            $medium = $kanban->medium;

            if ($medium->adapter == 'edusharing') {
                $medium->subscriptions()->delete();
                $medium->delete();
            }
        }

        // delete relations
        $kanban->items()->delete();
        $kanban->statuses()->delete();
        $kanban->subscriptions()->delete();
        $kanban->syncTags([]);

        return $kanban->delete();
    }

    public function exportKanbanCsv(Kanban $kanban)
    {
        $h[]      = [
            trans('global.kanbanStatus.title'),
            trans('global.kanbanItem.fields.title'),
            trans('global.kanbanItem.fields.description'),
            trans('global.date'),
            trans('global.owner'),
        ];
        $filename = uniqid() . '.csv';
        $fp       = fopen($filename, 'w');

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

        return response()->download($filename, $kanban->title . '.csv', $headers)->deleteFileAfterSend(true);
    }

    public function exportKanbanPdf(Kanban $kanban)
    {
        $pdf = PDF::loadView('exports.kanban.pdf', ['kanban' => $kanban])->setPaper('a4', 'landscape');

        return $pdf->download($kanban->title . '.pdf');
    }

    private function transformHexColorToRgba($color)
    {
        [$r, $g, $b] = sscanf($color, '#%02x%02x%02x');

        return 'rgba(' . $r . ', ' . $g . ', ' . $b . ', .7)';
    }

    public function getKanbanByToken(Kanban $kanban, Request $request)
    {
        $input = $this->validateRequest();

        $subscription = KanbanSubscription::where('sharing_token', $input['sharing_token'])->get()->first();

        if (!isset($subscription)) {
            abort(410, 'global.token_deleted');
        }

        if (isset($subscription->due_date)) {
            $now      = Carbon::now();
            $due_date = Carbon::parse($subscription->due_date);
            if ($due_date < $now) {
                abort(410, 'global.token_expired');
            }
        }

        return $this->show($kanban, $input['sharing_token']);
    }

    public function copyKanban(Kanban $kanban, Request $request)
    {
        abort_unless(Gate::allows('kanban_create') and $kanban->allow_copy, 403);

        $kanbanCopy = Kanban::create([
            'title'                 => $kanban->title . '_' . date('Y.m.d_H:i:s'),
            'description'           => $kanban->description,
            'color'                 => $kanban->color,
            'medium_id'             => $kanban->medium_id,
            'commentable'           => $kanban->commentable,
            'auto_refresh'          => $kanban->auto_refresh,
            'only_edit_owned_items' => $kanban->only_edit_owned_items,
            'allow_copy'            => $kanban->allow_copy,
            'owner_id'              => auth()->user()->id,
        ]);

        $statuses = $kanban->statuses;
        foreach ($statuses as $status) {
            $statusCopy = KanbanStatus::Create([
                'title'      => $status->title,
                'order_id'   => $status->order_id,
                'kanban_id'  => $kanbanCopy->id,
                'locked'     => $kanbanCopy->locked ?? false,
                'visibility' => $kanbanCopy->visibility ?? true,
                'owner_id'   => auth()->user()->id,
            ]);

            foreach ($status->items as $item) {
                $kanbanItemCopy = KanbanItem::Create([
                    'title'            => $item->title,
                    'description'      => $item->description,
                    'order_id'         => $item->order_id,
                    'kanban_id'        => $kanbanCopy->id,
                    'kanban_status_id' => $statusCopy->id,
                    'color'            => $item->color,
                    'due_date'         => $item->due_date,
                    'owner_id'         => auth()->user()->id,
                ]);

                foreach ($item->mediaSubscriptions as $mediaSubscription) {
                    MediumSubscription::Create([
                        'medium_id'         => $mediaSubscription->medium_id,
                        'subscribable_type' => $mediaSubscription->subscribable_type,
                        'subscribable_id'   => $kanbanItemCopy->id,
                        'sharing_level_id'  => $mediaSubscription->sharing_level_id,
                        'visibility'        => $mediaSubscription->visibility,
                        'additional_data'   => $mediaSubscription->additional_data,
                        'owner_id'          => auth()->user()->id,
                    ]);
                }
            }
        }

        return $kanbanCopy;
    }

    protected function validateRequest()
    {
        return request()->validate([
            'title'                 => 'sometimes|required',
            'description'           => 'sometimes',
            'medium_id'             => 'sometimes',
            'commentable'           => 'sometimes',
            'auto_refresh'          => 'sometimes',
            'color'                 => 'sometimes',
            'filter'                => 'sometimes',
            'only_edit_owned_items' => 'sometimes',
            'collapse_items'        => 'sometimes|boolean',
            'allow_copy'            => 'sometimes',
            'sharing_token'         => 'sometimes',
            'owner_id'              => 'sometimes',
        ]);
    }
}
