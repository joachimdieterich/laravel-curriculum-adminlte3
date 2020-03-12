@extends('layouts.master')
@section('title')
    {{ trans('global.task.title') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/">{{ trans('global.home') }}</a></li>
    <li class="breadcrumb-item active">{{ trans('global.task.title') }}</li>
    <li class="breadcrumb-item "><a href="/documentation" class="text-black-50"><i class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')
@can('user_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a id="add-task"
               class="btn btn-success" 
               href="{{ route("tasks.create") }}" 
               @click.prevent="$modal.show('task-modal', {'method': 'post', 'subscribable_type': 'App\\User', 'subscribable_id': '{{auth()->user()->id}}'})">
               {{ trans('global.task.create') }}
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-body">
       
       <ul class="todo-list" data-widget="todo-list">
           @foreach ($tasks as $task)
           <li>
               <!-- drag handle -->
<!--               <span class="handle">
                   <i class="fas fa-ellipsis-v"></i>
                   <i class="fas fa-ellipsis-v"></i>
               </span>-->
               <!-- checkbox -->
               <div  class="icheck-primary d-inline ml-2">
                   <input 
                       type="checkbox" 
                       value="" 
                       name="todo1" 
                       id="todoCheck1" 
                       onclick="complete( {{ $task->id }} )"
                       
                       {{ (optional($task->subscriptions->first())->completion_date != null) ? "checked" : "" }}
                       >
                     
                   <label for="todoCheck1"></label>
                  
               </div>
               <!-- todo text -->
               <span class="text"><a href="{{ route('tasks.show', $task->id) }}" >{{ $task->title }} </a></span>
               <!-- Emphasis label -->
               <small class="badge badge-primary pull-right"><i class="far fa-clock"></i> {{ $task->due_date }}</small>
               <!-- General tools such as edit or delete-->
               <div class="tools">
                   <a onclick="destroy( {{ $task->id }} )" >
                       <i class="fas fa-trash"></i>
                   </a>
               </div>
           </li>
           @endforeach


       </ul>

    </div>
</div>
<task-modal></task-modal>
@endsection

@section('scripts')
@parent
<script>
function complete(id) {
    $.ajax({
        headers: {'x-csrf-token': _token},
        method: 'POST',
        url: 'tasks/' + id + '/complete',
        data: { _method: 'PATCH' }
    })
    .done(function () { location.reload() })
}
function destroy(id) {
    sendRequest('POST', "tasks/"+id, id, { _method: 'DELETE' });   
}
function sendRequest(method, url, ids, data){
    if (ids.length === 0) {
        alert('{{ trans('global.datatables.zero_selected') }}')
        return
    }
    if (confirm('{{ trans('global.areYouSure') }}')) {
        $.ajax({
                headers: {'x-csrf-token': _token},
                method: method,
                url: url,
                data: data
            })
            .done(function () { location.reload() })
    }
}

</script>
@endsection       