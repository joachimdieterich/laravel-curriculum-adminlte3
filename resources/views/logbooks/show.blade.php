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
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item">
        @if (Auth::user()->id == env('GUEST_USER'))
            <a href="/navigators/{{Auth::user()->organizations()->where('organization_id', '=',  Auth::user()->current_organization_id)->first()->navigators()->first()->id}}">Home</a>
        @else
            <a href="/">{{ trans('global.home') }}</a>
        @endif
    </li>
    <li class="breadcrumb-item active">{{ trans('global.logbook.title_singular') }}</li>
    <li class="breadcrumb-item "><a href="/documentation" class="text-black-50"><i class="fas fa-question-circle"></i></a></li>
@endsection

@section('content')
<!-- {!! $logbook->description !!}-->
    @can('logbook_entry_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <button id="add-logbook-entry"
                   class="btn btn-success"
                    onclick="app.__vue__.$modal.show('logbook-entry-modal',  {'logbook_id': {{ $logbook->id }} });">
                   {{ trans('global.logbookEntry.create') }}
                </button>
            </div>
        </div>
    @endcan

<logbook :logbook="{{ $logbook }}"></logbook>

<medium-modal></medium-modal>
<medium-create-modal></medium-create-modal>
<logbook-entry-modal></logbook-entry-modal>
<subscribe-objective-modal></subscribe-objective-modal>
<task-modal></task-modal>
<absence-modal></absence-modal>
@can('logbook_create')
    @if (Auth::user()->id ==  $logbook->owner_id)
        <subscribe-modal></subscribe-modal>
    @endif
@endcan
@endsection
