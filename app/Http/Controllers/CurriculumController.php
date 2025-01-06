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
            return $this->getEntriesForSelect2();
        }

        abort_unless(Gate::allows('curriculum_access'), 403); //check here, cause json return should work for all users

        return view('curricula.index');
    }

    public function getTerminalObjectives(Curriculum $curriculum){
        if (request()->wantsJson()) {
            return getEntriesForSelect2ByCollection($curriculum->terminalObjectives());
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
        }

        if ((env('GUEST_USER') != null))
        {
            $guest_groups = User::find(env('GUEST_USER'))->groups;
        }

        foreach ($guest_groups as $group)
        {
            $userCanSee = $userCanSee->merge($group->curricula);
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
            case 'by_organization':  $curricula = Organization::where('id', auth()->user()->current_organization_id)->get()->first()->curricula;
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
            case 4: // user
            default:
            CurriculumSubscription::updateOrCreate([
                'curriculum_id' => $curriculum->id,
                'subscribable_type' => 'App\User',
                'subscribable_id' => auth()->user()->id,
            ], [
                'editable' => true,
                'owner_id' => auth()->user()->id,
            ]);
            break;
        }

        LogController::set(get_class($this).'@'.__FUNCTION__);

        if (request()->wantsJson()) {
            return ['curriculum' => $curriculum];
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

        $objectiveTypes = \App\ObjectiveType::all();
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
                ->with(compact('settings'))
            ;
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
        $curriculum = Curriculum::with(
            [
                'terminalObjectives',
                'terminalObjectives.achievements',
                'terminalObjectives.enablingObjectives',
                'terminalObjectives.enablingObjectives.achievements' => function ($query) {
                    $query->where('user_id', auth()->user()->id);
                },
            ])
            ->find($curriculum->id);
        //todo: only get achievments of defined users

        if (request()->wantsJson()) {
            return ['curriculum' => $curriculum];
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
        abort_unless(Gate::allows('curriculum_show'), 403);
        //check if user is enrolled or admin -> else 403
        abort_unless((auth()->user()->curricula->contains('id', $curriculum->id) // user enrolled
                  or (auth()->user()->currentRole()->first()->id == 1)), 403);     // or admin
        $user_ids = request()->user_ids;

        $curriculum = Curriculum::with(
            [
                'terminalObjectives',
                'terminalObjectives.achievements' => function ($query) use ($user_ids) {
                    $query->whereIn('user_id', $user_ids);
                },
                'terminalObjectives.enablingObjectives',
                'terminalObjectives.enablingObjectives.achievements' => function ($query) use ($user_ids) {
                    $query->whereIn('user_id', $user_ids);
                },
            ])
            ->find($curriculum->id);
        if (request()->wantsJson()) {
            return ['curriculum' => $curriculum];
        }
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
        abort_unless(Gate::allows('curriculum_edit'), 403);
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
        $this->checkPermissions($input['type_id']);

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
            'owner_id'              => auth()->user()->id,
        ]);

        if (request()->wantsJson()) {
            return ['curriculum' => $curriculum];
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

        return CurriculumSubscription::where('subscribable_type', "App\Group")
            ->where('subscribable_id', $enrolment['group_id'])->get();
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
            return ['message' => auth()->user()->currentCurriculaEnrolments()];
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

        // detach groups
        CurriculumSubscription::where([
            'curriculum_id' => $curriculum,
            'subscribable_type' => "App\Group",
        ])->delete();
        //$curriculum->groups()->detach();

        // delete certificates
        foreach ($curriculum->certificates as $certificate) {
            (new CertificateController)->destroy($certificate);
        }

        foreach ($curriculum->enablingObjectives as $ena) {
            (new EnablingObjectiveController)->destroy($ena);
        }

        foreach ($curriculum->terminalObjectives as $ter) {
            (new TerminalObjectiveController)->destroy($ter);
        }

        //  delete glossar
        $curriculum->glossar()->delete();

        // delete mediaSubscriptions -> media will not be deleted
        $media = $curriculum->media;

        $curriculum->mediaSubscriptions()
                ->where('subscribable_type', '=', 'App\Curriculum')
                ->where('subscribable_id', '=', $curriculum->id)
                ->delete();

        // delete navigator_items
        $curriculum->navigator_item()
                ->where('referenceable_type', '=', 'App\Curriculum')
                ->where('referenceable_id', '=', $curriculum->id)
                ->delete();

        // delete contents
        foreach ($curriculum->contents as $content) {
            (new ContentController)->destroy($content, 'App\Curriculum', $curriculum->id); // delete or unsubscribe if content is still subscribed elsewhere
        }

        $curriculum->subscriptions()->delete();

        $return = $curriculum->delete();

        //delete unused media
        foreach ($media as $medium) {
            Medium::where('id', $medium->id)->delete();
        }

        //todo check/delete unrelated references(in references table)
        if (request()->wantsJson()) {
            return ['message' => $return];
        }
        //   return back();
    }

    public function resetOrderIds(Curriculum $curriculum)
    {
        $curriculum = Curriculum::with(
            [
                'terminalObjectives',
                'terminalObjectives.enablingObjectives',
            ])
            ->find($curriculum->id);
        $t = 0;
        $currentObjectiveType = $curriculum->terminalObjectives->first()->objective_type_id;
        foreach ($curriculum->terminalObjectives as $terminalObjective) {
            if ($currentObjectiveType != $terminalObjective->objective_type_id) {
                $currentObjectiveType = $terminalObjective->objective_type_id;
                $t = 0;
            }

            $e = 0;
            $terminalObjective->order_id = $t;
            $terminalObjective->save();
            $t++;

            foreach ($terminalObjective->enablingObjectives as $enablingObjective) {
                $enablingObjective->order_id = $e;
                $enablingObjective->save();
                $e++;
            }
        }

        $this->show($curriculum);
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
        abort_unless(auth()->user()->id === $curriculum->owner_id, 403);

        $input = $this->validateRequest();

        $curriculum->update([
            'objective_type_order'                 => $input['objective_type_order']
        ]);

        return ['objective_type_order' => $curriculum->objective_type_order];
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
        if (Auth::user() == null) {       //if no user is authenticated authenticate guest
            LogController::set('guestLogin');
            LogController::setStatistics();
            Auth::loginUsingId((env('GUEST_USER')), true);
        }

        $input = $this->validateRequest();

        $subscription = CurriculumSubscription::where('sharing_token',$input['sharing_token'] )->get()->first();
        if ($subscription->due_date) {
            $now = Carbon::now();
            $due_date = Carbon::parse($subscription->due_date);
            if ($due_date < $now) {
                abort(410, 'Dieser Link ist nicht mehr gültig');
            }
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
        $input = request()->validate([
            'page' => 'sometimes|integer',
            'term' => 'sometimes|string|max:255|nullable',
        ]);
        if (is_admin() || is_creator())
        {
            return getEntriesForSelect2ByModel("App\Curriculum");
        }
        else if (is_schooladmin() || is_teacher())
        {
            $curriculum = Curriculum::whereHas('subscriptions', function ($query) use ($input) {
                $query->where(
                    function ($query) {
                        $query->where('subscribable_type', 'App\\Organization')
                            ->where('subscribable_id', auth()->user()->current_organization_id);
                    }
                )->orWhere(
                    function ($query) {
                        $query->where('subscribable_type', 'App\\Group')
                            ->whereIn('subscribable_id', auth()->user()->groups->pluck('id'));
                    }
                )->orWhere(
                    function ($query) {
                        $query->where('subscribable_type', 'App\\User')
                            ->where('subscribable_id', auth()->user()->id);
                    }
                )->orWhere(
                    function ($query) {
                        $query->where( 'owner_id', auth()->user()->id);
                    }
                )->orWhere(
                    function($query) use ($input)
                    {
                        $query->where('title', 'LIKE', '%' . $input['term'] . '%')
                            ->where('type_id', 1);
                    }
                )->orWhere(
                    function($query) use ($input)
                    {
                        $query->whereIn('owner_id', Organization::where('id', auth()->user()->current_organization_id)
                            ->first()->users()->pluck('id')->toArray())
                            ->where('title', 'LIKE', '%' . $input['term'] . '%')
                            ->where('type_id', 1);
                    }
                );
            });

            return getEntriesForSelect2ByCollection($curriculum);
        }
        else
        {
            return getEntriesForSelect2ByCollection(
                DB::table('curricula')
                    ->distinct()
                    ->select('curricula.id, curricula.title')
                    ->leftjoin('curriculum_subscriptions', 'curricula.id', '=', 'curriculum_subscriptions.curriculum_id')
                    ->leftjoin('group_user', 'group_user.group_id', '=', 'curriculum_subscriptions.subscribable_id')
                    ->where('curriculum_subscriptions.subscribable_type', 'App\Group')
                    ->where('group_user.user_id', auth()->user()->id)
                    ->orWhere('curricula.owner_id', auth()->user()->id), //user should also see curricula which he/she owns
                "curricula."
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
