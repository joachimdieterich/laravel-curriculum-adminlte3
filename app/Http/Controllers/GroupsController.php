<?php

namespace App\Http\Controllers;

use App\CurriculumSubscription;
use App\Group;
use App\Http\Requests\MassDestroyGroupRequest;
use App\Http\Requests\UpdateGroupRequest;
use App\OrganizationRoleUser;
use App\User;
use Exception;
use Gate;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use JsonException;
use Yajra\DataTables\DataTables;

class GroupsController extends Controller
{
    public function index()
    {
        // select2 request
        if (request()->wantsJson()) {
            return $this->getEntriesForSelect2();
        }
        // select2 should return entries for students
        abort_unless(Gate::allows('group_access'), 403);

        return view('groups.index');
    }

    /**
     * @throws Exception
     */
    public function list(): JsonResponse
    {
        abort_unless(Gate::allows('group_access'), 403);

        $groups = Group::select('id', 'title', 'common_name', 'grade_id', 'period_id', 'organization_id')
            ->with('organization:id,title');

        switch (auth()->user()->role()->id) {
            case 1: // admin
                break;
            case 2: // creator
            case 4: // schooladmin
            case 5: // teacher
                $groups->where('organization_id', auth()->user()->current_organization_id);
                break;
            default: // student
                $groups->whereHas('users', function ($query) {
                    $query->where('user_id', auth()->user()->id);
                });
                break;
        }

        if (! request()->boolean('allGroups')) {
            $groups->whereHas('period', function ($query) {
                $query->where('begin', '<=', now());
                $query->where('end', '>=', now());
            });
        }

        return DataTables::of($groups)
            ->addColumn('organization', function ($groups) {
                return $groups->organization->title;
            })
            ->make();
    }

    /**
     * @return Group|void
     */
    public function store()
    {
        abort_unless(Gate::allows('group_create'), 403);
        $new_group = $this->validateRequest();

        $group = Group::firstOrCreate([
            'title'           => $new_group['title'],
            'common_name'     => $new_group['common_name'] ?? null,
            'grade_id'        => format_select_input($new_group['grade_id']),
            'period_id'       => format_select_input($new_group['period_id']),
            'organization_id' => format_select_input($new_group['organization_id']),
        ]);

        if (request()->wantsJson()) {
            return $group;
        }
    }

    /**
     * Display the specified group.
     *
     * @return View|array
     *
     * @throws JsonException
     */
    public function show(Group $group)
    {
        abort_unless((Gate::allows('group_show') && $group->isAccessible()), 403);

        LogController::set(get_class($this) . '@' . __FUNCTION__, $group->id);

        if (request()->wantsJson()) {
            return ['users' => json_encode($group->users, JSON_THROW_ON_ERROR)];
        }

        $group = Group::where('id', $group->id)->with('glossar')->get()->first();

        return view('groups.show')
            ->with(compact('group'));
    }

    public function update(UpdateGroupRequest $request, Group $group)
    {
        abort_unless((Gate::allows('group_edit') && $group->isAccessible()), 403);

        $group->update([
            'title'           => $request['title'],
            'common_name'     => $request['common_name'] ?? $group->common_name,
            'grade_id'        => format_select_input($request['grade_id']),
            'period_id'       => format_select_input($request['period_id']),
            'organization_id' => format_select_input($request['organization_id']),
        ]);

        return $group;
    }

    /**
     * Remove the specified group from storage.
     */
    public function destroy(Group $group): ?bool
    {
        abort_unless((Gate::allows('group_delete') && $group->isAccessible()), 403);

        // first delete all relations
        CurriculumSubscription::where([
            'subscribable_type' => "App\Group",
            'subscribable_id'   => $group->id,
        ])->delete();

        $group->users()->detach();

        return $group->delete();
    }

    public function massDestroy(MassDestroyGroupRequest $request)
    {
        abort_unless(Gate::allows('group_delete'), 403);

        foreach (request('ids') as $id) {
            abort_unless(
                auth()->user()->groups->contains($id)
                || is_admin()
                || (Group::find($id)->first()->organization_id == auth()->user()->current_organization_id), 403
            );

            Group::where('id', $id)->delete();
        }

        return response(null, 204);
    }

    /**
     * @return User|bool|void
     */
    public function enrol()
    {
        abort_unless(Gate::allows('group_enrolment'), 403, 'No permission to enrol users into groups');

        foreach ((request()->enrollment_list) as $enrolment) {
            abort_unless((
                is_admin()
                || auth()->user()->groups->contains($enrolment['group_id']) // user is enrolled in group
                || Group::find($enrolment['group_id'])->organization_id == auth()->user()->current_organization_id // current set organization is group's organization
            ), 403, 'Need to be enrolled in group or have its organization be set as current organization');

            foreach ((array) $enrolment['user_id'] as $user_id) { // iterate over user_ids
                $user = User::findOrFail(format_select_input($user_id));
                foreach ((array) $enrolment['group_id'] as $group_id) { // iterate over group_ids
                    $group = Group::findOrFail($group_id);
                    // if user isn't enrolled to organization, enrol with student role
                    OrganizationRoleUser::firstOrCreate([
                        'user_id'         => $user->id,
                        'organization_id' => $group->first()->organization_id,
                    ],
                        [
                            'role_id' => 6,
                        ]
                    );

                    $user->groups()->syncWithoutDetaching($group->id);
                }
            }
        }

        if (request()->wantsJson()) {
            return $user ?? false;
        }
    }

    /**
     * @return User|bool|void
     */
    public function expel()
    {
        abort_unless(Gate::allows('group_enrolment'), 403, 'No permission to remove users from groups');

        foreach ((request()->expel_list) as $expel) {
            abort_unless((
                is_admin()
                || auth()->user()->groups->contains($expel['group_id']) // user is enrolled in group
                || Group::find($expel['group_id'])->organization_id == auth()->user()->current_organization_id // current set organization is group's organization
            ), 403, 'Need to be enrolled in group or have its organization be set as current organization');

            foreach ((array) $expel['user_id'] as $user_id) {
                $user = User::find($user_id);
                $user->groups()->detach($expel['group_id']);
            }
        }

        if (request()->wantsJson()) {
            return $user ?? false;
        }
    }

    protected function select2RequestWithOptGroup($collection)
    {
        $input = request()->validate([
            'page'     => 'sometimes|integer',
            'term'     => 'sometimes|string|max:255|nullable',
            'selected' => 'sometimes|nullable',
        ]);

        if (request()->has('selected')) {
            return response()->json(Group::whereIn('id', explode(',', $input['selected']))->get());
        }

        $page = $input['page'];
        $term = $input['term'];

        $resultCount = 25;
        $offset      = ($page - 1) * $resultCount;

        $count = count($collection->where(
            function ($query) use ($term) {
                $query->whereHas('groups', function ($query) use ($term) {
                    $query->where('title', 'like', '%' . $term . '%');
                });
            })
            ->get());

        $entries = $collection->where(
            function ($query) use ($term) {
                $query->whereHas('groups', function ($query) use ($term) {
                    $query->where('title', 'like', '%' . $term . '%');
                });
            })
            ->orderBy('title')
            ->skip($offset)
            ->take($resultCount)
            ->select(['organizations.id', DB::raw('title as text')])
            ->get();

        $endCount  = $offset + $resultCount;
        $morePages = $count > $endCount;

        $select2entries = [];
        foreach ($entries as $entry) {
            $select2entries[] = [
                'text'     => $entry['text'],
                'children' => Group::where('organization_id', $entry['id'])
                    ->select(['id', DB::raw('title as text')])
                    ->where('title', 'like', '%' . $term . '%')
                    ->get(),
            ];
        }
        $results = [
            'results'    => $select2entries,
            'pagination' => [
                'more' => $morePages,
            ],
        ];

        return response()->json($results);
    }

    protected function validateRequest()
    {
        return request()->validate([
            'title'           => 'sometimes|required',
            'grade_id'        => 'sometimes',
            'period_id'       => 'sometimes',
            'organization_id' => 'sometimes',
            'common_name'     => 'sometimes',
        ]);
    }

    private function getEntriesForSelect2(): JsonResponse
    {
        if (is_admin() || is_schooladmin() || is_teacher()) {
            return $this->select2RequestWithOptGroup(
                auth()->user()->organizations()->select(['organizations.id', 'organizations.title'])
            );
        }

        return getEntriesForSelect2ByCollection(
            auth()->user()->groups()->orderByDesc('organization_id'),
            'groups.'
        );
    }
}
