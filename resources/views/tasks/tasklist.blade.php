
<ul class="todo-list" 
    data-widget="todo-list">
    @foreach ($tasks as $task)
    <li>
        <!-- drag handle -->
               <span class="handle">
            <i class="fas fa-ellipsis-v"></i>
            <i class="fas fa-ellipsis-v"></i>
        </span>
        <!-- checkbox -->
        <div  class="icheck-primary d-inline ml-2">
            <input 
                type="checkbox" 
                value="" 
                name="todo1" 
                id="todoCheck1" 
                onclick="complete( {{ $task->id }} )"

                {{ (optional($task->subscriptions->where('subscribable_type', 'App\User')->where('subscribable_id', auth()->user()->id)->first())->completion_date != null) ? "checked" : "" }}
                >

            <label for="todoCheck1"></label>

        </div>
        <!-- todo text -->
        <span class="text"><a class="link-muted" href="{{ route('tasks.show', $task->id) }}" >{{ $task->title }} </a></span>
        <!-- Emphasis label -->
        @if(!isset($hide_due_date))
        <small class="badge badge-secondary pull-right"><i class="fa fa-calendar-check"></i> {{ $task->due_date }}</small>
        @endif
        <!-- General tools such as edit or delete-->
        <div class="tools">
            <a onclick="destroy( {{ $task->id }} )" >
                <i class="fas fa-trash"></i>
            </a>
        </div>
    </li>
    @endforeach
</ul>

@section('scripts')
@parent
<script>
function complete(id) {
    $.ajax({
        headers: {'x-csrf-token': _token},
        method: 'POST',
        url: '/tasks/' + id + '/complete',
        data: { _method: 'PATCH' }
    })
    .done(function () { location.reload() })
}
function destroy(id) {
    sendRequest('POST', "/tasks/"+id, id, { _method: 'DELETE' });   
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