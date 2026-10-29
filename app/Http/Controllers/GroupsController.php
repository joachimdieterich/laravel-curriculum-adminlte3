<?php

namespace App\Http\Controllers;

use App\CurriculumSubscription;
use App\Group;
use App\Http\Requests\MassDestroyGroupRequest;
use App\Http\Requests\UpdateGroupRequest;
use App\Organization;
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

        return view(
            'groups.index',
            [
                'organizations' => Organization::viewable(is_admin())
                    ->whereIn('id', Group::select('organization_id'))
                    ->get()
                    ->toJson(),
            ]
        );
    }

    /**
     * @throws Exception
     */
    public function list(): JsonResponse
    {
        abort_unless(Gate::allows('group_access'), 403);

        $groups = Group::select('groups.id', 'groups.title', 'groups.common_name', 'grade_id', 'period_id', 'organization_id')
            ->whereIn('organization_id', auth()->user()->organizations()->pluck('organizations.id'))
            ->with('organization:id,title');

        if (! request()->boolean('allGroups')) {
            $groups->where(function ($query) {
                $query->where('period_id', 1) // default (unset begin/end)
                    ->orWhereHas('period', function ($query) {
                        $query->where('begin', '<=', now());
                        $query->where('end', '>=', now());
                    });
            });
        }

        if (request()->exists('organizationFilter')) {
            $groups->whereIn('organization_id', request()->array('organizationFilter'));
        }

        return DataTables::of($groups)->make();
    }

    /**
     * @return Group|void
     */
    public function store()
    {
        abort_unless(Gate::allows('group_create'), 403);
        $new_group = $this->validateRequest();

        $group = Group::firstOrCreate([
            'title'             => $new_group['title'],
            'common_name'       => $new_group['common_name'] ?? null,
            'grade_id'          => $new_group['grade_id'],
            'period_id'         => $new_group['period_id'],
            'organization_id'   => $new_group['organization_id'],
        ]);

        return $group;
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
            'title'             => $request['title'],
            'common_name'       => $request['common_name'] ?? $group->common_name,
            'grade_id'          => $request['grade_id'],
            'period_id'         => $request['period_id'],
            'organization_id'   => $request['organization_id'],
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

        $group_id   = request()->group_id;
        $user_id    = request()->user_id;

        abort_unless((
            is_admin()
            || auth()->user()->groups->contains($group_id) // user is enrolled in group
            || Group::select('organization_id')->find($group_id)->organization_id == auth()->user()->current_organization_id // current set organization is group's organization
        ), 403, 'Need to be enrolled in group or have its organization be set as current organization');

        $user = User::select('id', 'firstname', 'lastname')->findOrFail(format_select_input($user_id));
        $group = Group::select('id', 'organization_id')->findOrFail($group_id);

        // if user isn't enrolled to organization, enrol with student role
        OrganizationRoleUser::firstOrCreate(
            [
                'user_id'           => $user->id,
                'organization_id'   => $group->organization_id,
            ],
            [
                'role_id' => 6,
            ]
        );

        $user->groups()->syncWithoutDetaching($group->id);

        return $user;
    }

    public function expel()
    {
        abort_unless(Gate::allows('group_enrolment'), 403, 'No permission to remove users from groups');

        $group_id   = request()->group_id;
        $user_id    = request()->user_id;

        abort_unless((
            is_admin()
            || auth()->user()->groups->contains($group_id) // user is enrolled in group
            || Group::findOrFail($group_id)->organization_id == auth()->user()->current_organization_id // current set organization is group's organization
        ), 403, 'Need to be enrolled in group or have its organization be set as current organization');

        User::select('id')->find($user_id)->groups()->detach($group_id);
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
