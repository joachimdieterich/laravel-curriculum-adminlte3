@extends((Auth::user()->id == env('GUEST_USER')) ? 'layouts.contentonly' : 'layouts.master')

@section('title')
<i class="fa fa-book mr-2"></i><small>{{ $logbook->title }}</small>
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
  
    <!-- Timelime example  -->
        <logbook  :logbook="{{ $logbook }}"></logbook>
    <!-- /.timeline -->
    <medium-modal></medium-modal>
    <medium-create-modal></medium-create-modal>
    <logbook-entry-modal></logbook-entry-modal>
    <subscribe-objective-modal></subscribe-objective-modal>
    <content-create-modal></content-create-modal>
    <task-modal></task-modal>
    <absence-modal></absence-modal>
@endsection

