<?php

namespace App\Http\Controllers;

use App\Task;
use Yajra\DataTables\DataTables;

class TaskSubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $input = $this->validateRequest();
        if (isset($input['subscribable_type']) and isset($input['subscribable_id'])) {
            $model = $input['subscribable_type']::find($input['subscribable_id']);
            abort_unless((\Gate::allows('task_access') and $model->isAccessible()), 403);

            $tasks = Task::with('subscriptions')
                ->join('task_subscriptions', 'tasks.id', '=', 'task_subscriptions.task_id')
                ->where('task_subscriptions.subscribable_type', '=', $input['subscribable_type'])
                ->where('task_subscriptions.subscribable_id', '=', $input['subscribable_id'])
                ->get();
            // TODO: for some reason the task->id gets overwritten by its task->subscription->id, making this unuseable
            return empty($tasks) ? '' : DataTables::of($tasks)
                ->addColumn('check', '')
                ->setRowId('id')
                ->make(true);
        }
        //todo -> make it subscribable?
    }

    protected function validateRequest()
    {
        return request()->validate([
            'subscribable_type' => 'sometimes|string',
            'subscribable_id'   => 'sometimes|integer',
        ]);
    }
}
