<?php

namespace App\Http\Controllers;

use Illuminate\Support\Collection;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function impressum()
    {
        return view('impressum');
    }

    /**
     * Returns the courses of the user
     * 
     * @return Collection
     */
    public function courses(): Collection
    {
        $user = auth()->user();
        $period_id = $user->current_period_id;
        $org_ids = $user->organizations()->pluck('organizations.id');

        // similar to user()->currentCurriculaEnrollments, but with all enroled organizations
        return \App\Curriculum::select(
                'curricula.id', 'curricula.title', 'groups.title AS group_title',
                'curriculum_subscriptions.id AS course_id',
                'curriculum_subscriptions.subscribable_id AS group_id'
            )
            ->leftjoin('curriculum_subscriptions', 'curricula.id', '=', 'curriculum_subscriptions.curriculum_id')
            ->leftjoin('group_user', 'group_user.group_id', '=', 'curriculum_subscriptions.subscribable_id')
            ->join('groups', 'groups.id', '=', 'group_user.group_id')
            ->where('curriculum_subscriptions.subscribable_type', 'App\Group')
            ->where('groups.period_id', $period_id)
            ->whereIn('groups.organization_id', $org_ids)
            ->where('group_user.user_id', $user->id)
            ->orderBy('curricula.title')
            ->with(['achievements' => function ($query) use ($user) {
                $query->select('achievements.id', 'status')
                    ->where('user_id', $user->id);
            }])
            ->withCount('enablingObjectives')
            ->without('owner')
            ->get();
    }

    /**
     * Returns the groups of the user
     * 
     * @return Collection
     */
    public function groups(): Collection
    {
        return auth()->user()->groups()
            ->select('groups.id', 'groups.title', 'grade_id')
            ->with('grade:id,title')
            ->get();
    }

    /**
     * Returns the logbooks of the user
     * 
     * @return Collection
     */
    public function logbooks(): Collection
    {
        return auth()->user()->logbooks()
            ->select('logbooks.id', 'logbooks.title')
            ->get();
    }

    /**
     * Returns the plans of the user
     * 
     * @return Collection
     */
    public function plans(): Collection
    {
        return auth()->user()->plans()
            ->select('plans.id', 'plans.title')
            ->get();
    }
}