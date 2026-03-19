@extends((Auth::user()->id == config('app.guest_user_id')) ? 'layouts.contentonly' : 'layouts.master')

@section('title')
    <title-component></title-component>
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
@endsection
