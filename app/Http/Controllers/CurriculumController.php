<?php

namespace App\Http\Controllers;

use App\Certificate;
use App\Curriculum;
use App\CurriculumSubscription;
use App\CurriculumType;
use App\Medium;
use App\Organization;
use App\User;
use App\VariantDefinition;
use Carbon\Carbon;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class CurriculumController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     */
    public function index()
    {
        if (request()->wantsJson()) {
            if (request()->has('owner')) {
                return getEntriesForSelect2ByCollection(Curriculum::where('owner_id', auth()->user()->id));
            } else {
                return $this->getEntriesForSelect2();
            }
        }

        abort_unless(Gate::allows('curriculum_access'), 403); //check here, cause json return should work for all users

        return view('curricula.index');
    }

    public function getTerminalObjectives(Curriculum $curriculum){
        if (request()->wantsJson()) {
            return getEntriesForSelect2ByCollectionAlternative($curriculum->terminalObjectives);
        }
    }

    public function types()
    {
        if (request()->wantsJson()) {

            if (Gate::allows('curriculum_create_global'))
            {
                return CurriculumType::all();
            }
            else if (Gate::allows('curriculum_create_for_organization'))
            {
                return CurriculumType::where('id', ">", 1)->get();
            }
            else if (Gate::allows('curriculum_create_for_group'))
            {
                return CurriculumType::where('id', ">", 2)->get();
            }
            else if (Gate::allows('curriculum_create_for_user'))
            {
                return CurriculumType::where('id', ">",  3)->get();
            }
        }
    }

    public function userCurricula($withOwned = true, $user = null)
    {
        if ($user == null)
        {
            $user = auth()->user();
        }
        $userCanSee = $user->curricula;

        foreach ($user->groups as $group) {
            $userCanSee = $userCanSee->merge($group->curricula);
        }
        $organization = Organization::find($user->current_organization_id)->curricula;
        $userCanSee = $userCanSee->merge($organization);

        if ($withOwned)
        {
            $owned = Curriculum::where('owner_id', $user->id)->get();
            $userCanSee = $userCanSee->merge($owned);
            // global curricula are visible for all users, but shouldn't be shown on 'shared with me'
            $global = Curriculum::where('type_id', 1)->get();
            $userCanSee = $userCanSee->merge($global);
        }

        return $userCanSee->unique();
    }

    public function list(Request $request)
    {
        abort_unless(Gate::allows('curriculum_access'), 403);

        switch ($request->filter)
        {
            case 'owner':            $curricula = Curriculum::where('owner_id', auth()->user()->id)->get();
                break;
            case 'shared_with_me':   $curricula = $this->userCurricula(false);
                break;
            case 'shared_by_me':     $curricula = Curriculum::where('owner_id', auth()->user()->id)->whereHas('subscriptions')->get();
                break;
            case 'by_organization':  $curricula = Organization::find(auth()->user()->current_organization_id)->curricula;
                break;
            case 'all':
            default:                 $curricula = $this->userCurricula();
                break;
        }

        return empty($curricula) ? '' : DataTables::of($curricula)
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
        abort(403);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        abort_unless(Gate::allows('curriculum_create'), 403);
        $input = $this->validateRequest();

        $this->checkPermissions($input['type_id']);

        $curriculum = Curriculum::firstOrCreate([
            'title'                 => $input['title'],
            'description'           => $input['description'],
            'author'                => $input['author'],
            'publisher'             => $input['publisher'],
            'city'                  => $input['city'],
            'date'                  => $input['date'],
            'color'                 => $input['color'],
            'grade_id'              => format_select_input($input['grade_id']),
            'subject_id'            => format_select_input($input['subject_id']),
            'organization_type_id'  => format_select_input($input['organization_type_id']),
            'type_id'               => format_select_input($input['type_id']),
            'state_id'              => format_select_input($input['state_id']),
            'country_id'            => format_select_input($input['country_id']),
            'medium_id'             => $input['medium_id'],
            'variants'              => $this->formatVariantsField(
                                            $input['variants'] ?? NULL,
                                            $input['variant_default_title'] ?? NULL,
                                            $input['variant_default_description'] ?? NULL
                                        ),
            'archived'              =>  $input['archived'] ?? false,
            'owner_id'              => auth()->user()->id,
        ]);

        switch ($curriculum->type_id) {
            case 2: // organization
                CurriculumSubscription::updateOrCreate([
                    'curriculum_id' => $curriculum->id,
                    'subscribable_type' => 'App\Organization',
                    'subscribable_id' => auth()->user()->current_organization_id,
                ], [
                    'editable' => true,
                    'owner_id' => auth()->user()->id,
                ]);
                break;
            case 3: // group
                //Todo: if type_id == 3 there should be an option to add group_id
                break;
        }

        LogController::set(get_class($this).'@'.__FUNCTION__);

        if (request()->wantsJson()) {
            return $curriculum->with('owner')->find($curriculum->id);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Curriculum  $curriculum
     * @return \Illuminate\Http\Response
     */
    public function show(Curriculum $curriculum, $achievements = false, $token = null)
    {
        abort_unless((Gate::allows('curriculum_show') and $curriculum->isAccessible()), 403);
        LogController::set(get_class($this).'@'.__FUNCTION__, $curriculum->id);

        $objectiveTypes = \App\ObjectiveType::select('objective_types.id', 'objective_types.title', 'objective_types.uuid')
            ->join('terminal_objectives', 'objective_types.id', '=', 'terminal_objectives.objective_type_id')
            ->join('curricula', 'curricula.id', '=', 'terminal_objectives.curriculum_id')
            ->where('curricula.id', $curriculum->id)
            ->distinct()
            ->get();
        $levels = \App\Level::all();

        $curriculum = Curriculum::with([
            'glossar.contents',
        ])
        ->find($curriculum->id);

        if ($token == null)
        {
            $may_edit = $curriculum->isEditable();
        }
        else
        {
            $may_edit = $curriculum->isEditable(auth()->user()->id, $token);
        }

        $settings = json_encode([
            'edit' => $may_edit, //(auth()->user()->id === $curriculum->owner_id) ? true : false,
            'cross_reference_curriculum_id' => false,
        ]);

        if (request()->wantsJson()) {
            return ['contents' => $curriculum->contents];
        }

        return view('curricula.show')
            ->with(compact('curriculum'))
            ->with(compact('objectiveTypes'))
            ->with(compact('levels'))
            ->with(compact('settings'));
    }

    /**
     * Display the specified resource with achievements.
     *
     * @param  \App\Curriculum  $curriculum
     * @return \Illuminate\Http\Response
     */
    public function showAchievements(Curriculum $curriculum)
    {
        $this->show($curriculum, true);
    }

    public function getObjectives(Curriculum $curriculum)
    {
        if (request()->wantsJson()) {
            return $this->getObjectivesByType($curriculum);
        }
    }

    /**
     * Get achievements
     *
     * @param  \App\Curriculum  $curriculum
     * @return \Illuminate\Http\Response
     */
    public function getAchievements(Curriculum $curriculum)
    {
        abort_unless(Gate::allows('curriculum_show') and $curriculum->isAccessible(), 403, "No access to this curriculum");

        if (request()->wantsJson()) {
            return $this->getObjectivesByType($curriculum, request()->user_ids);
        }
    }

    private function getObjectivesByType(Curriculum $curriculum, $user_ids = null)
    {
        if ($user_ids == null) {
            $user_ids = [auth()->user()->id];
        }

        return \App\ObjectiveType::select('id', 'title', 'uuid')
            ->whereHas('terminalObjectives', function ($query) use ($curriculum) {
                $query->where('curriculum_id', $curriculum->id);
            })
            ->with(['terminalObjectives' => function ($query) use ($curriculum, $user_ids) {
                $query->select('id', 'title', 'description', 'color', 'time_approach', 'objective_type_id', 'curriculum_id', 'order_id', 'uuid', 'visibility')
                    ->where('curriculum_id', $curriculum->id)
                    ->with([
                        'enablingObjectives' => function($query) use ($user_ids) {
                            $query->without('terminalObjective')
                                ->with(['achievements' => function($query) use ($user_ids) {
                                    $query->select('id', 'status', 'referenceable_id', 'referenceable_type', 'updated_at')
                                        ->whereIn('user_id', $user_ids);
                                }])
                                ->orderBy('order_id');
                        },
                    ])
                    ->orderBy('order_id');
            }])
            ->get();
    }

    /**
     * Show curriculum in edit mode
     *
     * @param  \App\Curriculum  $curriculum
     * @return \Illuminate\Http\Response
     */
    public function edit(Curriculum $curriculum)
    {
        abort(403);
    }

    /**
     * Show edit_owner
     *
     * @param  \App\Curriculum  $curriculum
     * @return \Illuminate\Http\Response
     */
    public function editOwner(Curriculum $curriculum)
    {
        abort(403);
    }

    /**
     * Store edit_owner
     *
     * @param  \App\Curriculum  $curriculum
     * @return \Illuminate\Http\Response
     */
    public function storeOwner(Request $request, Curriculum $curriculum)
    {
        abort_unless(Gate::allows('curriculum_edit'), 403, "No permission to change owner");
        $input = $this->validateRequest();

        $curriculum->update([
            'owner_id' => format_select_input($input['owner_id']),
        ]);

        if (request()->wantsJson()) {
            return $curriculum;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Curriculum  $curriculum
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Curriculum $curriculum)
    {
        abort_unless(Gate::allows('curriculum_edit'), 403);

        $input = $this->validateRequest();

        $curriculum->update([
            'title'                 => $input['title'],
            'description'           => $input['description'],
            'author'                => $input['author'],
            'publisher'             => $input['publisher'],
            'city'                  => $input['city'],
            'date'                  => $input['date'],
            'color'                 => $input['color'],
            'grade_id'              => format_select_input($input['grade_id']),
            'subject_id'            => format_select_input($input['subject_id']),
            'organization_type_id'  => format_select_input($input['organization_type_id']),
            'type_id'               => format_select_input($input['type_id']),
            'state_id'              => isset($input['state_id']) ? format_select_input($input['state_id']) : null,
            'country_id'            => format_select_input($input['country_id']),
            'medium_id'             => $input['medium_id'],
            'variants'              => $this->formatVariantsField(
                                        $input['variants'] ?? NULL,
                                        $input['variant_default_title'] ?? NULL,
                                        $input['variant_default_description'] ?? NULL
                                       ),
            'archived'              =>  $input['archived'] ?? false,
            'owner_id'              => is_admin() ? $input['owner_id'] : auth()->user()->id,
        ]);

        if (request()->wantsJson()) {
            return $curriculum;
        }
        //return redirect($curriculum->path());
    }

    /**
     * If $input['filepath'] is set and medium exists, id is return, else return is null
     *
     * @param  array  $input
     * @return mixed
     */
    public function enrol()
    {
        abort_unless(Gate::allows('course_create'), 403);

        foreach ((request()->enrollment_list) as $enrolment)
        {
            if(is_array($enrolment['curriculum_id']))
            {
                foreach ($enrolment['curriculum_id'] as $curriculum_id)
                {
                    $this->subscribe(format_select_input($curriculum_id), $enrolment['group_id']);
                }
            } else {
                $this->subscribe(format_select_input($enrolment['curriculum_id']), $enrolment['group_id']);
            }
        }

        return Curriculum::select('curricula.id', 'curricula.title', 'curricula.description', 'curricula.color', 'curricula.medium_id', 'curricula.type_id', 'curricula.archived')
            ->join('curriculum_subscriptions', 'curricula.id', '=', 'curriculum_subscriptions.curriculum_id')
            ->where('subscribable_id', request()->enrollment_list[0]['group_id'])
            ->where('subscribable_type', "App\Group")
            ->get();
    }

    private function subscribe($curriculum_id, $group_id, $model = "App\Group", $editable = false)
    {
        $subscribe = CurriculumSubscription::updateOrCreate([
            'curriculum_id' => $curriculum_id,
            'subscribable_type' => $model,
            'subscribable_id' => $group_id,
        ], [
            'editable' => $editable,
            'owner_id' => auth()->user()->id,
        ]);
        $subscribe->save();
    }

    public function references()
    {
        if (request()->wantsJson()) {
            return getEntriesForSelect2ByCollectionAlternative(auth()->user()->currentCurriculaEnrolments());
        }
    }

    public function expel()
    {
        abort_unless(Gate::allows('course_create'), 403);

        foreach ((request()->expel_list) as $expel)
        {
            if(is_array($expel['curriculum_id']))
            {
                foreach ($expel['curriculum_id'] as $curriculum_id)
                {
                    $this->unsubscribe($curriculum_id, $expel['group_id']);
                }
            } else {
                $this->unsubscribe($expel['curriculum_id'], $expel['group_id']);
            }
        }

        return CurriculumSubscription::where('subscribable_type', "App\Group")
        ->where('subscribable_id', $expel['group_id'])->get();;
    }

    private function unsubscribe($curriculum_id, $group_id, $model = "App\Group", $editable = false)
    {
        CurriculumSubscription::where([
            'curriculum_id' => $curriculum_id,
            'subscribable_type' => "App\Group",
            'subscribable_id' => $group_id,
        ])->delete();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Curriculum  $curriculum
     * @return \Illuminate\Http\Response
     */
    public function destroy(Curriculum $curriculum)
    {
        //todo: delete media attached to content, descriptions...
        abort_unless(Gate::allows('curriculum_delete'), 403);

        // delete mediaSubscriptions -> media will not be deleted
        $media = $curriculum->media;

        // delete contents
        foreach ($curriculum->contents as $content) {
            (new ContentController)->destroy($content, 'App\Curriculum', $curriculum->id); // delete or unsubscribe if content is still subscribed elsewhere
        }

        $return = $curriculum->delete();

        //delete unused media
        foreach ($media as $medium) {
            Medium::where('id', $medium->id)->delete();
        }

        //todo check/delete unrelated references(in references table)
        if (request()->wantsJson()) {
            return $return;
        }
    }

    public function resetOrderIds(Curriculum $curriculum)
    {
        $t = 0;
        $terminalObjectives = $curriculum->terminalObjectives()->reorder()->orderBy('objective_type_id')->get();
        $currentObjectiveType = $terminalObjectives->first()->objective_type_id;
        foreach ($terminalObjectives as $terminalObjective) {
            if ($currentObjectiveType != $terminalObjective->objective_type_id) {
                $currentObjectiveType = $terminalObjective->objective_type_id;
                $t = 0;
            }

            $terminalObjective->update(['order_id' => $t]);
            $t++;
            
            $e = 0;
            foreach ($terminalObjective->enablingObjectives as $enablingObjective) {
                $enablingObjective->update(['order_id' => $e]);
                $e++;
            }
        }
    }

    public function print(Curriculum $curriculum)
    {

        //LogController::set(get_class($this).'@'.__FUNCTION__);
        $html = view('print.curriculum')
            ->with(compact('curriculum'))
            ->render();
        if (request()->wantsJson()) {
            return ['path' => (app('App\Http\Controllers\PrintController')->print($html, $curriculum->title.'.pdf', 'save'))];
        }
        //  return app('App\Http\Controllers\PrintController')->print($html, $curriculum->title.'.pdf', 'save');
    }

    public function syncObjectiveTypesOrder(Curriculum $curriculum)
    {
        abort_unless((
            auth()->user()->id === $curriculum->owner_id
            OR is_admin()
        ), 403, "Only the owner has permission to change the order of objective types");

        $input = $this->validateRequest();

        $curriculum->update([
            'objective_type_order' => $input['objective_type_order']
        ]);

        return $curriculum->objective_type_order;
    }

    public function getVariantDefinitions(Curriculum $curriculum)
    {
        $definition = array();
        if (isset($curriculum->variants['order']))
        {
            foreach($curriculum->variants['order'] AS $variant_definitition)
            {

                if ($variant_definitition == 0)
                {
                    $definition[] = array(
                        'id'            => 0,
                        'title'         => $curriculum->variants['title'] ?? '',
                        'description'   => $curriculum->variants['description'] ?? '',
                        'color'         => $curriculum->variants['color'] ?? $curriculum->color,
                        'css_icon'      => $curriculum->variants['css_icon'] ?? '',
                        'owner_id'      => $curriculum->variants['owner_id'] ?? $curriculum->owner_id,
                        'created_at'    => $curriculum->variants['created_at'] ?? $curriculum->created_at,
                        'updated_at'    => $curriculum->variants['updated_at'] ?? $curriculum->updated_at,
                    );
                }
                else
                {
                    $definition[] = VariantDefinition::find($variant_definitition);
                }
            }
        }


        if (request()->wantsJson()) {
            return ['definitions' => $definition];
        }
    }

    public function setVariantDefinitions(Curriculum $curriculum)
    {
        $input = $this->validateRequest();
        DB::table('curricula')
            ->where('id', $curriculum->id)
            ->update(['variants->order' => $input['variants']]);
    }

    public function getCertificates(Curriculum $curriculum)
    {
        if (request()->wantsJson()) {
            return getEntriesForSelect2ByCollection(
                Certificate::where([
                    ['curriculum_id', '=', $curriculum->id],
                    ['organization_id', '=', auth()->user()->current_organization_id],
                ])
                    ->orWhere([
                        ['curriculum_id', '=', $curriculum->id],
                        ['global', '=', 1],
                    ])
                    ->orWhere([
                        ['type', '=', 'group'],
                        ['global', '=', 1],
                    ])
            );
        }
    }

    public function getCurriculumByToken(Curriculum $curriculum, Request $request)
    {
        $input = $this->validateRequest();

        $subscription = CurriculumSubscription::where('sharing_token',$input['sharing_token'] )->get()->first();
        if (!$subscription?->due_date) abort(403, 'global.token_deleted');

        $now = Carbon::now();
        $due_date = Carbon::parse($subscription->due_date);
        if ($due_date < $now) {
            abort(410, 'global.token_expired');
        }

        return $this->show($curriculum, false, $input['sharing_token']);

    }

    protected function validateRequest()
    {
        return request()->validate([
            'title'                 => 'sometimes',
            'description'           => 'sometimes',
            'author'                => 'sometimes',
            'publisher'             => 'sometimes',
            'city'                  => 'sometimes',
            'date'                  => 'sometimes',
            'color'                 => 'sometimes',
            'grade_id'              => 'sometimes',
            'subject_id'            => 'sometimes',
            'organization_type_id'  => 'sometimes',
            'type_id'               => 'sometimes',
            'state_id'              => 'sometimes',
            'country_id'            => 'sometimes',
            'medium_id'             => 'sometimes',
            'owner_id'              => 'sometimes',
            'objective_type_order'  => 'sometimes',
            'variants'  => 'sometimes',
            'variant_default_title'  => 'sometimes',
            'variant_default_description'  => 'sometimes',
            'archived' => 'sometimes',
            'sharing_token' => 'sometimes'
        ]);
    }

    /**
     * @param $type_id
     */
    private function checkPermissions($type_id): void
    {
        $id = format_select_input($type_id);

        if ($id == 4) { //user
            abort_unless(
                (
                    Gate::allows('curriculum_create_for_user') or
                    Gate::allows('curriculum_create_for_group') or
                    Gate::allows('curriculum_create_for_organization') or
                    Gate::allows('curriculum_create_global')
                ), 403);
        } elseif ($id == 3) { //group
            abort_unless(
                (
                    Gate::allows('curriculum_create_for_group') or
                    Gate::allows('curriculum_create_for_organization') or
                    Gate::allows('curriculum_create_global')
                ), 403);
        } elseif ($id == 2) { //organization
            abort_unless(
                (
                    Gate::allows('curriculum_create_for_organization') or
                    Gate::allows('curriculum_create_global')
                ), 403);
        } elseif ($id == 1) { //group
            abort_unless(Gate::allows('curriculum_create_global'), 403);
        }
    }

    /**
     * @return CurriculumType[]|array|\Illuminate\Database\Eloquent\Collection
     */
    private function getCurriculumTypesByPermission()
    {
        if (Gate::allows('curriculum_create_global')) {
            $curriculum_types = CurriculumType::all();
        } elseif (Gate::allows('curriculum_create_for_organization')) {
            $curriculum_types = CurriculumType::where('id', '>', 1)->get();
        } elseif (Gate::allows('curriculum_create_for_group')) {
            $curriculum_types = CurriculumType::where('id', '>', 2)->get();
        } elseif (Gate::allows('curriculum_create_for_user')) {
            $curriculum_types = CurriculumType::where('id', '>', 3)->get();
        } else {
            $curriculum_types = [];
        }

        return $curriculum_types;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    private function getEntriesForSelect2(): \Illuminate\Http\JsonResponse
    {
        if (is_admin())
        {
            return getEntriesForSelect2ByModel("App\Curriculum");
        }
        else if (is_creator() || is_schooladmin() || is_teacher())
        {
            $curriculum = Curriculum::where('owner_id', auth()->user()->id) // owner
                ->orWhere('type_id', 1) // global curricula
                ->orWhereHas('subscriptions', function ($query) {
                    $query->where( // organization-subscription
                        function ($query) {
                            $query->where('subscribable_type', 'App\\Organization')
                                ->where('subscribable_id', auth()->user()->current_organization_id);
                        }
                    )->orWhere( // group-subscription
                        function ($query) {
                            $query->where('subscribable_type', 'App\\Group')
                                ->whereIn('subscribable_id', auth()->user()->groups->pluck('id'));
                        }
                    )->orWhere( // user-subscription
                        function ($query) {
                            $query->where('subscribable_type', 'App\\User')
                                ->where('subscribable_id', auth()->user()->id);
                        }
                    );
                })->get();

            return getEntriesForSelect2ByCollectionAlternative($curriculum);
        }
        else
        {
            return getEntriesForSelect2ByCollectionAlternative(
                Curriculum::select('curricula.id', 'curricula.title')
                    ->distinct()
                    ->leftjoin('curriculum_subscriptions', 'curricula.id', '=', 'curriculum_subscriptions.curriculum_id')
                    ->leftjoin('group_user', 'group_user.group_id', '=', 'curriculum_subscriptions.subscribable_id')
                    ->where('curriculum_subscriptions.subscribable_type', 'App\Group')
                    ->where('group_user.user_id', auth()->user()->id)
                    ->orWhere('curricula.owner_id', auth()->user()->id) //user should also see curricula which he/she owns
                    ->get()
            );
        }
    }

    private function formatVariantsField($variant_definition_ids, $variant_default_title, $variant_default_description ){

        if (isset ($variant_definition_ids))
        {
            array_unshift($variant_definition_ids, 0); // add default
            return [
                "order" => $variant_definition_ids,
                "title" => $variant_default_title,
                "description" => $variant_default_description,
            ];
        }
        else
        {
            return null;
        }

    }
}
