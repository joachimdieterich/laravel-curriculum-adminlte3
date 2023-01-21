<?php

namespace App\Http\Controllers;

use App\Kanban;
use App\Medium;
use App\Organization;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

    protected function userKanbans()
    {
        $userCanSee = auth()->user()->kanbans;

        foreach (auth()->user()->currentGroups as $group) {
            $userCanSee = $userCanSee->merge($group->kanbans);
        }
        $organization = Organization::find(auth()->user()->current_organization_id)->kanbans;
        $userCanSee = $userCanSee->merge($organization);

        $owned = Kanban::where('owner_id', auth()->user()->id)->get();
        $userCanSee = $userCanSee->merge($owned);

        return $userCanSee->unique();
    }

    public function list()
    {
        abort_unless(\Gate::allows('kanban_access'), 403);
        $kanbans = $this->userKanbans();

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
            'color' => $new_kanban['color'],
            'medium_id' => $this->getMediumIdByInputFilepath($new_kanban),
            'commentable' => ($new_kanban['commentable'] == 'on') ? 1 : 0 ,
            'auto_refresh' => ($new_kanban['commentable'] == 'on') ? 1 : 0 ,
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
     * If $input['filepath'] is set and medium exists, id is return, else return is null
     *
     * @param  array  $input
     * @return mixed
     */
    public function getMediumIdByInputFilepath($input)
    {
        if (isset($input['filepath'])) {
            $medium = new Medium();

            return (null !== $medium->getByFilemanagerPath($input['filepath'])) ? $medium->getByFilemanagerPath($input['filepath'])->id : null;
        } else {
            return null;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Kanban  $kanban
     * @return \Illuminate\Http\Response
     */
    public function show(Kanban $kanban)
    {
        abort_unless((\Gate::allows('kanban_show') and $this->userKanbans()->contains($kanban->id)), 403);
        $kanban = $this->getKanbanWithRelations($kanban);

        $may_edit = $kanban->isEditable();
        $is_shared = Auth::user()->sharing_token !== null;

        LogController::set(get_class($this).'@'.__FUNCTION__);

        return view('kanbans.show')
            ->with(compact('kanban', 'may_edit', 'is_shared'));
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
            'medium_id' => $this->getMediumIdByInputFilepath($input) ?? $kanban->medium_id,
            'commentable' => isset($input['commentable']) ? 1 : '0',
            'auto_refresh' => isset($input['auto_refresh']) ? 1 : '0',
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
        if (! $kanban) {
            return;
        }
        $kanban->color = $request->color;
        if ($kanban->color == '#DDE6E8') {
            $kanban->color = '#F4F4F4';
        }
        $kanban->save();
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
                fputcsv($fp, [$status->title, $k->title, strip_tags($k->description),  $k->created_at, $k->owner->fullName()]);
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
            'statuses', 'statuses.items' => function ($query) use ($kanban) {
                $query->where('kanban_id', $kanban->id)
                    ->with(['owner',
                        /*'taskSubscription.task.subscriptions' => function ($query) {
                            $query->where('subscribable_id', auth()->user()->id)
                                ->where('subscribable_type', 'App\User');
                        },*/
                        'mediaSubscriptions.medium'])
                    ->orderBy('order_id');
            }, 'statuses.items.subscriptions', 'statuses.items.comments', 'statuses.items.comments.user',
        ])->where('id', $kanban->id)->get()->first();
    }

    protected function validateRequest()
    {
        return request()->validate([
            'title' => 'sometimes|required',
            'description' => 'sometimes',
            'filepath' => 'sometimes',
            'commentable' => 'sometimes',
            'auto_refresh' => 'sometimes',
            'color' => 'sometimes',
        ]);
    }
}
