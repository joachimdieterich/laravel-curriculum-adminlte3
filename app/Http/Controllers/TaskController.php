<?php

namespace App\Http\Controllers;

use App\StatusDefinition;
use App\Task;
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

        return view('tasks.index')
                ->with(compact('tasks'));
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
            'owner_id'          => auth()->user()->id,
        ]);

        //subscribe to model
        if (isset($input['subscribable_type']) and isset($input['subscribable_id'])) {
            $model = $input['subscribable_type']::find($input['subscribable_id']);
            $task->subscribe($model);
        }

        LogController::set(get_class($this).'@'.__FUNCTION__);

        // axios call?
        if (request()->wantsJson()) {
            return ['message' => $task->path()];
        }

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
        abort_unless((\Gate::allows('task_show') and $task->isAccessible()), 403);

        $task = $task->with(['contentSubscriptions.content.categories',
            'terminalObjectiveSubscriptions.terminalObjective',
            'enablingObjectiveSubscriptions.enablingObjective.terminalObjective',
            'mediaSubscriptions.medium', ])
            ->where('id', $task->id)->get()->first();

        // axios call?
        if (request()->wantsJson()) {
            return [
                'task' => $task,
            ];
        }
        $status_definitions = StatusDefinition::all();

        return view('tasks.show')
                ->with(compact('task'))
                ->with(compact('status_definitions'));
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
        abort_unless((\Gate::allows('task_edit') and $task->isAccessible()), 403);

        $input = $this->validateRequest();
        $task->update([
            'title' => $input['title'],
            'description' => $input['description'],
            'start_date' => $input['start_date'],
            'due_date' => $input['due_date'],
        ]);

        if (request()->wantsJson()) {
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
        abort_unless((\Gate::allows('task_delete') and $task->isAccessible()), 403);

        $task->subscriptions()->delete(); //first delete subscriptions
        $task->delete();

        if (request()->wantsJson()) {
            return ['message' => '/tasks'];
        }

        return back();
    }

    public function complete(Request $request, Task $task)
    {
        abort_unless(\Gate::allows('task_access'), 403);

        //subscribe to model if not already subscribed
        $subscription = $task->subscribe(auth()->user());
        ($subscription->completion_date == null) ? $subscription->complete() : $subscription->incomplete();

        if (request()->wantsJson()) {
            return ['status' => $task->path()];
        }

        return redirect($task->path());
    }

    public function activity(Request $request, Task $task)
    {
        abort_unless(\Gate::allows('task_access'), 403);

        $activity = Task::with(['subscriptions.statuses.model', 'subscriptions.subscribable', 'subscriptions.owner'])
                ->where('id', $task->id)->get()->first();
        if (request()->wantsJson()) {
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
