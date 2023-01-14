@extends((Auth::user()->id == env('GUEST_USER')) ? 'layouts.contentonly' : 'layouts.master')

@section('title')
    {{ trans('global.kanban.title') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item">
        @if (Auth::user()->id == env('GUEST_USER'))
            <a href="/navigators/{{Auth::user()->organizations()->where('organization_id', '=',  Auth::user()->current_organization_id)->first()->navigators()->first()->id}}">Home</a>
        @else
            <a href="/"><i class="fa fa-home"></i></a>
        @endif
    </li>
    <li class="breadcrumb-item active">{{ trans('global.kanban.title') }}</li>
    <li class="breadcrumb-item "><a href="{{ env('DOCUMENTATION', '/documentation') }}" class="text-black-50"                                    aria-label="{{ trans('global.documentation') }}"><i
                class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')
    @can('kanban_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a id="add-kanban"
                   class="btn btn-success"
                   href="{{ route("kanbans.create") }}">
                    {{ trans('global.kanban.create') }}
                </a>
        </div>
    </div>
@endcan

<kanbans model-url="kanbans"></kanbans>
@endsection
@section('scripts')
@parent

<script>
$(document).ready( function () {
    var table = $('#kanbans-datatable').DataTable({
        ajax: "{{ url('kanbans/list') }}",
        columns:
            [
                {data: 'check'},
                {data: 'title'},
                {data: 'action'}
            ],
        buttons: []
    });
});
</script>

@endsection
