<?php

namespace App\Http\Controllers;

use App\Country;
use App\Curriculum;
use App\CurriculumType;
use App\Grade;
use App\Group;
use App\Medium;
use App\Organization;
use App\OrganizationType;
use App\State;
use App\Subject;
use App\User;
use App\VariantDefinition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\DataTables;

class CurriculumController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->wantsJson() AND request()->has(['term', 'page'])) {
            return $this->getEntriesForSelect2();
        }
        //todo: check if used anymore
        if (request()->wantsJson()) {
            return ['curricula' => auth()->user()->curricula(['curricula.*'])]; //no gate! every user should get his enrolled curricula
        }

        abort_unless(\Gate::allows('curriculum_access'), 403); //check here, cause json return should work for all users

        return view('curricula.index');
    }

    public function list()
    {
        abort_unless(\Gate::allows('curriculum_access'), 403);

        if (auth()->user()->role()->id == 1) {
            $curricula = Curriculum::select([
                'id',
                'title',
                'state_id',
                'country_id',
                'grade_id',
                'subject_id',
                'organization_type_id',
                'type_id',
                'owner_id',
            ])->with(['state', 'country', 'grade',
                'subject', 'organizationType',
                'type', 'owner', ]);
        } else {
            $curricula = Curriculum::select([
                'id',
                'title',
                'state_id',
                'country_id',
                'grade_id',
                'subject_id',
                'organization_type_id',
                'type_id',
                'owner_id',
            ])->where('owner_id', auth()->user()->id);
        }

        $edit_gate = \Gate::allows('curriculum_edit');
        $delete_gate = \Gate::allows('curriculum_delete');

        return DataTables::of($curricula)
            ->addColumn('state', function ($curricula) {
                return isset($curricula->state->lang_de) ? $curricula->state->lang_de : '-';
            })
            ->addColumn('country', function ($curricula) {
                return $curricula->country->lang_de;
            })
            ->addColumn('grade', function ($curricula) {
                return $curricula->grade->title;
            })
            ->addColumn('subject', function ($curricula) {
                return $curricula->subject->title;
            })
            ->addColumn('organizationtype', function ($curricula) {
                return $curricula->organizationType->title;
            })
            ->addColumn('type', function ($curricula) {
                return $curricula->type->title;
            })
            ->addColumn('owner', function ($curricula) {
                return $curricula->owner->firstname.' '.$curricula->owner->lastname;
            })
            ->addColumn('action', function ($curricula) use ($edit_gate, $delete_gate) {
                $actions = '';

                if ($edit_gate and ($curricula->owner_id == auth()->user()->id)) {
                    $actions .= '<a href="'.route('curricula.edit', $curricula->id).'" '
                                    .'class="btn">'
                                    .'<i class="fa fa-pencil-alt"></i>'
                                    .'</a>';
                }
                if ($edit_gate and ($curricula->owner_id == auth()->user()->id)) {
                    $actions .= '<a href="'.route('curricula.editOwner', $curricula->id).'" '
                                    .'class="btn">'
                                    .'<i class="fa fa-user"></i>'
                                    .'</a>';
                }
                if ($delete_gate and ($curricula->owner_id == auth()->user()->id)) {
                    $actions .= '<button type="button" '
                                .'class="btn text-danger" '
                                .'onclick="destroyDataTableEntry(\'curricula\','.$curricula->id.')">'
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
        abort_unless(\Gate::allows('curriculum_create'), 403);

        $grades = Grade::all();
        $subjects = Subject::all();
        $organization_types = OrganizationType::all();
        $curriculum_types = $this->getCurriculumTypesByPermission();
        $variant_definitions = VariantDefinition::all();
        $countries = Country::all();
        $states = State::where('country', 'DE')->get();

        return view('curricula.create')
                ->with(compact('grades'))
                ->with(compact('subjects'))
                ->with(compact('countries'))
                ->with(compact('states'))
                ->with(compact('organization_types'))
                ->with(compact('variant_definitions'))
                ->with(compact('curriculum_types'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        abort_unless(\Gate::allows('curriculum_create'), 403);
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
            'owner_id'              => auth()->user()->id,
        ]);

        LogController::set(get_class($this).'@'.__FUNCTION__);
        // axios call?
        if (request()->wantsJson()) {
            return ['message' => $curriculum->path()];
        }

        return redirect($curriculum->path());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Curriculum  $curriculum
     * @return \Illuminate\Http\Response
     */
    public function show(Curriculum $curriculum, $achievements = false)
    {
        abort_unless((\Gate::allows('curriculum_show') and $curriculum->isAccessible()), 403);
        LogController::set(get_class($this).'@'.__FUNCTION__, $curriculum->id);

        $objectiveTypes = \App\ObjectiveType::all();
        $levels = \App\Level::all();

        $curriculum = Curriculum::with([
            'glossar.contents',
        ])
        ->find($curriculum->id);
        $settings = json_encode([
            'edit' => true,
            'cross_reference_curriculum_id' => false,
        ]);

        //axios request?
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

        //  $curriculum = Curriculum::where('id', $curriculum->id)->with('terminalObjectives.enablingObjectives')->get()->first(); //replaced to use lazy loading on curriculum/courseview
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
        abort_unless(\Gate::allows('curriculum_show'), 403);
        //check if user is enrolled or admin -> else 403
        abort_unless((auth()->user()->curricula()->contains('id', $curriculum->id) // user enrolled
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
        $grades = Grade::all();
        $subjects = Subject::all();
        $organization_types = OrganizationType::all();
        $curriculum_types = CurriculumType::all();
        $variant_definitions = VariantDefinition::all();
        $countries = Country::all();
        $states = State::all();

        return view('curricula.edit')
                ->with(compact('grades'))
                ->with(compact('subjects'))
                ->with(compact('organization_types'))
                ->with(compact('curriculum_types'))
                ->with(compact('countries'))
                ->with(compact('states'))
                ->with(compact('variant_definitions'))
                ->with(compact('curriculum'));
    }

    /**
     * Show edit_owner
     *
     * @param  \App\Curriculum  $curriculum
     * @return \Illuminate\Http\Response
     */
    public function editOwner(Curriculum $curriculum)
    {
        $users = Organization::where('id', auth()->user()->current_organization_id)->get()->first()->users()->get();

        return view('curricula.owner')
                ->with(compact('curriculum'))
                ->with(compact('users'));
    }

    /**
     * Store edit_owner
     *
     * @param  \App\Curriculum  $curriculum
     * @return \Illuminate\Http\Response
     */
    public function storeOwner(Request $request, Curriculum $curriculum)
    {
        abort_unless(\Gate::allows('curriculum_edit'), 403);
        $input = $this->validateRequest();

        $curriculum->update([
            'owner_id' => format_select_input($input['owner_id']),
        ]);

        return redirect('/curricula');
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
        abort_unless(\Gate::allows('curriculum_edit'), 403);

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
            'owner_id'              => auth()->user()->id,
        ]);

        return redirect($curriculum->path());
    }

    /**
     * If $input['filepath'] is set and medium exists, id is return, else return is null
     *
     * @param  array  $input
     * @return mixed
     */
    public function enrol()
    {
        abort_unless(\Gate::allows('course_create'), 403);

        foreach ((request()->enrollment_list) as $enrolment) {
            $return[] = Group::findOrFail($enrolment['group_id'])->curricula()->syncWithoutDetaching($enrolment['curriculum_id']);
        }

        return $return;
    }

    public function references()
    {
        if (request()->wantsJson()) {
            return ['message' => auth()->user()->currentCurriculaEnrolments()];
        }
    }

    public function expel()
    {
        abort_unless(\Gate::allows('course_create'), 403);

        foreach ((request()->expel_list) as $expel) {
            $group = Group::find($expel['group_id']);
            $return[] = $group->curricula()->detach($expel['curriculum_id']);
        }

        return $return;
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
        abort_unless(\Gate::allows('curriculum_delete'), 403);

        // detach groups
        $curriculum->groups()->detach();

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
                    \Gate::allows('curriculum_create_for_user') or
                    \Gate::allows('curriculum_create_for_group') or
                    \Gate::allows('curriculum_create_for_organization') or
                    \Gate::allows('curriculum_create_global')
                ), 403);
        } elseif ($id == 3) { //group
            abort_unless(
                (
                    \Gate::allows('curriculum_create_for_group') or
                    \Gate::allows('curriculum_create_for_organization') or
                    \Gate::allows('curriculum_create_global')
                ), 403);
        } elseif ($id == 2) { //organization
            abort_unless(
                (
                    \Gate::allows('curriculum_create_for_organization') or
                    \Gate::allows('curriculum_create_global')
                ), 403);
        } elseif ($id == 1) { //group
            abort_unless(\Gate::allows('curriculum_create_global'), 403);
        }
    }

    /**
     * @return CurriculumType[]|array|\Illuminate\Database\Eloquent\Collection
     */
    private function getCurriculumTypesByPermission()
    {
        if (\Gate::allows('curriculum_create_global')) {
            $curriculum_types = CurriculumType::all();
        } elseif (\Gate::allows('curriculum_create_for_organization')) {
            $curriculum_types = CurriculumType::where('id', '>', 1)->get();
        } elseif (\Gate::allows('curriculum_create_for_group')) {
            $curriculum_types = CurriculumType::where('id', '>', 2)->get();
        } elseif (\Gate::allows('curriculum_create_for_user')) {
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
            'page' => 'required|integer',
            'term' => 'sometimes|string|max:255|nullable',
        ]);
        if (is_admin())
        {
            return getEntriesForSelect2ByModel("App\Curriculum");
        }
        else if (is_schooladmin())
        {
            return getEntriesForSelect2ByCollection(
                Curriculum::where(
                        function($query) use ($input)
                        {
                            $query->whereIn('owner_id', Organization::where('id', auth()->user()->current_organization_id)
                                ->first()->users()->pluck('id')->toArray())
                                ->where('title', 'LIKE', '%' . $input['term'] . '%')
                                ->where('type_id', 1);
                        })
                    ->orwhere(
                        function($query) use ($input)
                        {
                            $query->where('title', 'LIKE', '%' . $input['term'] . '%')
                                  ->where('type_id', 1);
                        })
            );
        }
        else
        {
            return getEntriesForSelect2ByCollection(
                DB::table('curricula')
                    ->distinct()
                    ->select('curricula.id, curricula.title')
                    ->leftjoin('curriculum_group', 'curricula.id', '=', 'curriculum_group.curriculum_id')
                    ->leftjoin('group_user', 'group_user.group_id', '=', 'curriculum_group.group_id')
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
