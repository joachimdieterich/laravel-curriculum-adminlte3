<?php

namespace App\Http\Controllers;

use App\Http\Requests\Tags\FavouriteModelRequest;
use App\Http\Requests\Tags\HideModelRequest;
use App\Kanban;
use App\KanbanSubscription;
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
                getSubscribedModels(Kanban::class),
                'kanbans.'
            );
        }

        return view('kanbans.index');
    }

    public function list(Request $request)
    {
        abort_unless(Gate::allows('kanban_access'), 403);

        $kanbans = Kanban::query();
        $withOwned = false;
        $withSubscribed = false;
        $tags = request('tags') ?? [];
        $negativeTags = request('negativeTags') ?? [];

        if (request()->has(['group_id'])) {
            $group_id = request()->validate(
                [
                    'group_id' => 'required',
                ]
            )['group_id'];

            $kanbans = Kanban::with('subscriptions')
                ->whereHas('subscriptions', function ($query) use ($group_id) {
                    $query->where(
                        function ($query) use ($group_id) {
                            $query->where('subscribable_type', 'App\\Group')
                                ->where('subscribable_id', $group_id);
                        }
                    );
                });
        } else {
            switch ($request->filter) {
                case 'owner':           $withOwned = true;
                    break;
                case 'shared_with_me':  $withSubscribed = true;
                    break;
                case 'shared_by_me':    $kanbans->where('owner_id', auth()->user()->id)->whereHas('subscriptions');
                    break;
                case 'all':             $withSubscribed = $withOwned = true;
                    break;
                case 'hidden':          $withSubscribed = $withOwned = true;
                                        $tags[] = Tag::findFromString(trans('global.tag.hidden.singular'))->id;
                    break;
                case 'favourite':
                default:                $withSubscribed = $withOwned = true;
                                        $tags[] = Tag::findFromString(trans('global.tag.favourite.singular'))->id;
                    break;
            }
        }

        return getDataTableWithEntries($kanbans, $withSubscribed, $withOwned, $tags, $negativeTags);
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
                // so we need to check if a guest-user is accessing through a token
                auth()->user()->id == config('app.guest_user_id')
                or !$kanban->isAccessible()
            ),
            403
        );

        $kanban = $kanban->withRelations();

        $may_edit = $kanban->isEditable(auth()->user()->id, $token);
        $may_favour = auth()->user()->id != config('app.guest_user_id');

        $is_shared           = $kanban->owner_id !== auth()->user()->id; //Auth::user()->sharing_token !== null;
        $is_websocket_active = config('broadcasting.active');

        LogController::set(get_class($this) . '@' . __FUNCTION__, $kanban->id);

        return view('kanbans.show')
            ->with(compact('kanban', 'may_edit', 'is_shared', 'is_websocket_active', 'may_favour'));
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
     * @return bool|null
     */
    public function destroy(Kanban $kanban)
    {
        abort_unless((Gate::allows('kanban_delete') and $kanban->isAccessible()), 403);

        $kanban->delete();
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

    public function favourKanban(Kanban $kanban, FavouriteModelRequest $request)
    {
        if ($request->input('mark')) {
            $kanban->attachTag(trans('global.tag.favourite.singular'));
            LogController::set(get_class($this) . '@' . __FUNCTION__, $kanban->id);
        } else {
            $kanban->detachTag(trans('global.tag.favourite.singular'));
        }

        return response($kanban);
    }

    public function hideKanban(Kanban $kanban, HideModelRequest $request)
    {
        if ($request->input('mark')) {
            $kanban->attachTag(trans('global.tag.hidden.singular'));
            LogController::set(get_class($this) . '@' . __FUNCTION__, $kanban->id);
        } else {
            $kanban->detachTag(trans('global.tag.hidden.singular'));
        }

        return response($kanban);
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

    public function copyKanban(Kanban $kanban)
    {
        abort_unless(
            (Gate::allows('kanban_create') and $kanban->allow_copy)
            || $kanban->owner_id == auth()->user()->id
            || is_admin()
        , 403);

        $kanbanCopy = $kanban->replicate()->fill([
            'title'    => $kanban->title . date(' [Y.m.d_H:i:s]'),
            'owner_id' => auth()->user()->id,
        ]);
        $kanbanCopy->save();

        if ($kanban->medium_id) {
            try {
                $this->copyMediumSubscription($kanban->medium->subscriptions()->first(), 'App\\Kanban', $kanbanCopy->id);
            } catch (\Throwable) {
                $kanbanCopy->update(['medium_id' => null]);
            }
        }

        foreach ($kanban->statuses as $status) {
            $statusCopy = $status->replicate()->fill([
                'kanban_id' => $kanbanCopy->id,
                'owner_id'  => auth()->user()->id,
            ]);
            $statusCopy->save();

            foreach ($status->items as $item) {
                $itemCopy = $item->replicate()->fill([
                    'kanban_status_id' => $statusCopy->id,
                    'kanban_id'        => $kanbanCopy->id,
                    'editors_ids'      => [],
                    'owner_id'         => auth()->user()->id,
                ]);
                $itemCopy->save();

                foreach ($item->mediaSubscriptions as $mediumSubscription) {
                    $this->copyMediumSubscription($mediumSubscription, 'App\\KanbanItem', $itemCopy->id);
                }
            }
        }

        return $kanbanCopy;
    }

    protected function copyMediumSubscription(\App\MediumSubscription $subscription, string $model,int $modelId): void
    {
        $usage = null;
        // if Medium is external, we need to create a new usage
        if (!is_null($subscription->additional_data)) {
            $usage = app(\App\Plugins\Repositories\edusharing\Edusharing::class)->createUsage(
                $model,
                $modelId,
                $subscription->additional_data['nodeId'],
                $subscription->medium()->pluck('owner_id')->first()
            );
        }

        $subscription->replicate()->fill([
            'subscribable_id'   => $modelId,
            'owner_id'          => auth()->user()->id,
            'additional_data'   => $usage,
        ])->save();
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