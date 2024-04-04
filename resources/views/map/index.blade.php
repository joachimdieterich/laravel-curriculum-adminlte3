@extends((Auth::user()->id == env('GUEST_USER')) ? 'layouts.contentonly' : 'layouts.master')

@section('title')
    {{ trans('global.map.title') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item">
        @if (Auth::user()->id == env('GUEST_USER'))
            <a href="/navigators/{{Auth::user()->organizations()->where('organization_id', '=',  Auth::user()->current_organization_id)->first()->navigators()->first()->id}}">Home</a>
        @else
            <a href="/"><i class="fa fa-home"></i></a>
        @endif
    </li>
    <li class="breadcrumb-item active">{{ trans('global.map.title') }}</li>
    <li class="breadcrumb-item">
        <a href="{{ env('DOCUMENTATION', '/documentation') }}"
           class="text-black-50"
           aria-label="{{ trans('global.documentation') }}">
            <i class="fas fa-question-circle"></i>
        </a>
    </li>
@endsection
@section('content')
    <maps model-url="maps"></maps>

    <medium-modal></medium-modal>
    @can('medium_create')
        <medium-create-modal></medium-create-modal>
    @endcan
@endsection
@section('scripts')
    @parent

    <script>
        $(document).ready( function () {
            var table = $('#maps-datatable').DataTable({
                ajax: "{{ url('maps/list') }}",
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
