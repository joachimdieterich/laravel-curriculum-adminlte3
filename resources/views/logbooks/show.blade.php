@extends((Auth::user()->id == env('GUEST_USER')) ? 'layouts.contentonly' : 'layouts.master')

@section('title')
    <small>{{ $logbook->title }}</small>
    @can('logbook_create')
        @if (Auth::user()->id ==  $logbook->owner_id)
            <a class="btn btn-flat"
               href="/logbooks/{{ $logbook->id }}/edit">
                <i class="fa fa-pencil-alt text-secondary"></i>
            </a>
            <button class="btn btn-flat"
                    onclick="app.__vue__.$modal.show('subscribe-modal',  {'modelId': {{ $logbook->id }}, 'modelUrl': 'logbook' });">
                <i class="fa fa-share-alt text-secondary"></i>
            </button>
        @endif
    @endcan
    <p class="h6 pb-1">{{trans('global.owner')}}: {{ $logbook->owner->fullname() }}</p>
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item">
        @if (Auth::user()->id == env('GUEST_USER'))
            <a href="/navigators/{{Auth::user()->organizations()->where('organization_id', '=',  Auth::user()->current_organization_id)->first()->navigators()->first()->id}}">Home</a>
        @else
            <a href="/"><i class="fa fa-home"></i></a>
        @endif
    </li>
    <li class="breadcrumb-item active">{{ trans('global.logbook.title_singular') }}</li>
    <li class="breadcrumb-item "><a href="{{ env('DOCUMENTATION', '/documentation') }}" class="text-black-50"                                    aria-label="{{ trans('global.documentation') }}"><i
                class="fas fa-question-circle"></i></a></li>
@endsection

@section('content')
    <!-- {!! $logbook->description !!}-->

    <logbook :logbook="{{ $logbook }}"
             :period="{{App\Period::find(auth()->user()->current_period_id)}}"></logbook>

    <medium-modal></medium-modal>
    <medium-create-modal></medium-create-modal>
    <subscribe-objective-modal></subscribe-objective-modal>
    <task-modal></task-modal>
    <absence-modal></absence-modal>
    @can('logbook_create')
        <subscribe-modal></subscribe-modal>
    @endcan
@endsection
