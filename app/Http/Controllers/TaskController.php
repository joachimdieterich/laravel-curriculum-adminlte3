<?php

namespace App\Http\Controllers;

use App\Task;
use App\StatusDefinition;
use App\TaskSubscription;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_unless(\Gate::allows('task_access'), 403);
        $tasks = auth()->user()->tasks;
        /*    Task::with(['subscriptions' => function($query) {
                            $query->where('subscribable_type', 'App\User')
                                  ->where('subscribable_id', auth()->user()->id);
                        },  'subscriptions.statuses.model', 'subscriptions.subscribable', 'subscriptions.owner'])
                        ->get();*/
        //dd($tasks);
        return view('tasks.index')
                ->with(compact('tasks'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        abort_unless(\Gate::allows('task_create'), 403);

        $input = $this->validateRequest();
        $task = Task::firstOrCreate([
            'title'             => $input['title'],
            'description'       => $input['description'],
            'start_date'        => $input['start_date'],
            'due_date'          => $input['due_date'],
            'owner_id'          => auth()->user()->id
        ]);

        //subscribe to model
        if (isset($input['subscribable_type']) AND isset($input['subscribable_id'])){
            $model = $input['subscribable_type']::find($input['subscribable_id']);
            $task->subscribe($model);
        }

        // axios call?
        if (request()->wantsJson()){
            return ['message' => $task->path()];
        }
        //dd($organization->path());
        return redirect($task->path());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function show(Task $task)
    {
        $task = $task->with(['contentSubscriptions.content.categories',
                'terminalObjectiveSubscriptions.terminalObjective',
                'enablingObjectiveSubscriptions.enablingObjective.terminalObjective',
                'mediaSubscriptions.medium'])
                ->where('id', $task->id)->get()->first();

        abort_unless(\Gate::allows('task_show'), 403);
        // axios call?
        if (request()->wantsJson()){
            return [
                'task' => $task
            ];
        }
        $status_definitions = StatusDefinition::all();

        return view('tasks.show')
                ->with(compact('task'))
                ->with(compact('status_definitions'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function edit(Task $task)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Task $task)
    {
        abort_unless(\Gate::allows('task_edit'), 403);

        $input = $this->validateRequest();
        $task->update([
            'title'             => $input['title'],
            'description'       => $input['description'],
            'start_date'        => $input['start_date'],
            'due_date'          => $input['due_date'],
        ]);

        if (request()->wantsJson()){
            return ['message' => $task->path()];
        }
        return redirect($task->path());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task)
    {
        abort_unless(\Gate::allows('task_delete'), 403);


        $task->subscriptions()->delete(); //first delete subscriptions
        $task->delete();

        if (request()->wantsJson()){
            return ['message' => '/tasks'];
        }
        return back();
    }

    public function complete(Request $request, Task $task)
    {
        abort_unless(\Gate::allows('task_access'), 403);

        $input = $this->validateRequest();

        //subscribe to model if not already subscribed
        $subscription = $task->subscribe(auth()->user());
        ($subscription->completion_date == null) ? $subscription->complete() : $subscription->incomplete();

        if (request()->wantsJson()){
            return ['status' => $task->path()];
        }
        return redirect($task->path());
    }

    public function activity(Request $request, Task $task)
    {
        abort_unless(\Gate::allows('task_access'), 403);

        $activity = Task::with(['subscriptions.statuses.model', 'subscriptions.subscribable', 'subscriptions.owner'])
                ->where('id', $task->id)->get()->first();
        if (request()->wantsJson()){
            return ['activity' => $activity];
        }
        return redirect($activity);
    }


    protected function validateRequest()
    {

        return request()->validate([
            'id'                => 'sometimes',
            'title'             => 'sometimes|required',
            'description'       => 'sometimes',
            'start_date'        => 'sometimes',
            'due_date'          => 'sometimes',
            'owner_id'          => 'sometimes',
            'subscribable_type' => 'sometimes',
            'subscribable_id'   => 'sometimes',
            ]);
    }
}
