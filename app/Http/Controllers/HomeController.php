<?php

namespace App\Http\Controllers;

use App\Curriculum;
use App\Domains\Exams\Models\Exam;
use App\Tag;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return Renderable
     */
    public function index()
    {
        return view('home');
    }

    /**
     * Returns the courses of the user
     */
    public function courses(): Collection
    {
        $user    = auth()->user();
        $org_ids = $user->organizations()->pluck('organizations.id');

        $query = Curriculum::leftjoin('curriculum_subscriptions', 'curricula.id', '=', 'curriculum_subscriptions.curriculum_id')
            ->leftjoin('group_user', 'group_user.group_id', '=', 'curriculum_subscriptions.subscribable_id')
            ->join('groups', 'groups.id', '=', 'group_user.group_id')
            ->where('curriculum_subscriptions.subscribable_type', 'App\Group')
            ->whereIn('groups.organization_id', $org_ids)
            ->where('group_user.user_id', $user->id)
            ->orderBy('curricula.title')
            ->with(['achievements' => function ($query) use ($user) {
                $query->select('achievements.id', 'status')
                    ->where('user_id', $user->id)
                    ->whereNot('status', '00');
            }])
            ->withCount('enablingObjectives')
            ->without('owner');

        $hasFavourited = $this->addTagsToQuery($query);

        return $query->select(
            'curricula.id', 'curricula.title', 'groups.title AS group_title',
            'curriculum_subscriptions.id AS course_id',
            'curriculum_subscriptions.subscribable_id AS group_id',
            DB::raw(($hasFavourited ? 1 : 0) . ' AS is_favourited')
        )->get();
    }

    /**
     * Returns the groups of the user
     */
    public function groups(): Collection
    {
        return auth()->user()->groups()
            ->with('grade:id,title')
            ->orderBy('groups.title')
            ->get(['groups.id', 'groups.title', 'grade_id']);
    }

    /**
     * Returns the 10 most recent achievements of the user
     */
    public function achievements(): Collection
    {
        return auth()->user()->achievements()
            ->orderByDesc('achievements.updated_at')
            ->where('status', '!=', '00')
            ->limit(10)
            ->with([
                'referenceable:id,title',
                'history' => function ($query) {
                    $query->orderBy('created_at', 'desc')->limit(1);
                },
                'history.owner',
            ])
            ->get(['id', 'status', 'referenceable_id', 'referenceable_type']);
    }

    /**
     * Returns the logbooks of the user
     */
    public function logbooks(): Collection
    {
        return auth()->user()->logbooks()
            ->orderBy('logbooks.title')
            ->get(['logbooks.id', 'logbooks.title', 'logbooks.owner_id']);
    }

    /**
     * Returns favored kanbans if exists, else all accessible to user
     */
    public function kanbans(): Collection
    {
        $query         = auth()->user()->kanbans()->orderBy('kanbans.title');
        $hasFavourited = $this->addTagsToQuery($query);

        return $query->select('kanbans.id', 'kanbans.title', 'kanbans.owner_id', DB::raw(($hasFavourited ? 1 : 0) . ' AS is_favourited'))->get();
    }

    /**
     * Returns the plans of the user
     */
    public function plans(): Collection
    {
        return auth()->user()->plans()
            ->orderBy('plans.title')
            ->get(['plans.id', 'plans.title', 'plans.owner_id']);
    }

    /**
     * returns the exams of the user
     */
    public function exams(): Collection
    {
        if (is_student()) {
            $exams = auth()->user()->exams()
                ->with('group:id,title')
                ->get(['exams.id', 'exams.tool', 'exams.test_name', 'exams.school_key', 'exams.group_id']);
            foreach ($exams as $exam) {
                $exam->login_url = config('test_tools.tools')[$exam->tool]['adapter']->getExamLoginUrl($exam);
            }
        } else {
            $exams = Exam::whereIn('group_id', auth()->user()->groups()->pluck('groups.id'))
                ->with('group:id,title')
                ->get(['exams.id', 'exams.exam_id', 'exams.test_name', 'exams.group_id']);
        }

        return $exams;
    }

    private function addTagsToQuery(Builder $query): int
    {
        // add withTags so it doesn't fire a query for every single entry
        $query->with(['tags' => function ($query) {
            $query->select('id', 'name', 'slug')
                ->where('user_id', auth()->user()->id);
        }]);

        $favouriteTag = Tag::findFromString(trans('global.tag.favourite.singular')) ?? 0;
        $favCount     = (clone $query)->withAllTags($favouriteTag)->count();

        if ($favCount !== 0) {
            $query->withAllTags($favouriteTag);
        } else {
            $negativeTag = Tag::findFromString(trans('global.tag.hidden.singular')) ?? 0;
            $query->withoutTags($negativeTag);
        }

        return $favCount;
    }
}
