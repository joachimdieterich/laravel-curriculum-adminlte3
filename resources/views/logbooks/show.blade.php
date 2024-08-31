@extends((Auth::user()->id == env('GUEST_USER')) ? 'layouts.contentonly' : 'layouts.master')

@section('title')
    <small>{{ $logbook->title }}</small>
    @can('logbook_create')
        @if (Auth::user()->id ==  $logbook->owner_id)
            <a class="btn btn-flat"
               href="/logbooks/{{ $logbook->id }}/edit"
               onclick="this.$eventHub.emit('showSearchbar');"
            >
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
    <breadcrumbs
        :entries="{{json_encode([
            ['active'=> true, 'title'=> trans('global.logbook.title_singular'), 'url' => "/logbooks"],
            ['active'=> true, 'title'=> $logbook->title]
        ])}}"
    ></breadcrumbs>
@endsection

@section('content')
    <!-- {!! $logbook->description !!}-->

    <logbook :logbook="{{ $logbook }}"
             :period="{{App\Period::find(auth()->user()->current_period_id)}}"></logbook>

<!--    <medium-modal></medium-modal>-->
<!--    <medium-create-modal></medium-create-modal>-->
<!--    <subscribe-objective-modal></subscribe-objective-modal>
    <logbook-entry-subject-modal></logbook-entry-subject-modal>
    <task-modal></task-modal>
    <absence-modal></absence-modal>
    @can('logbook_create')
        <subscribe-modal></subscribe-modal>
    @endcan-->
@endsection
