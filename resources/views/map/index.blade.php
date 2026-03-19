@extends((Auth::user()->id == config('app.guest_user_id')) ? 'layouts.contentonly' : 'layouts.master')

@section('title')
    {{ trans('global.map.title') }}
@endsection
@section('breadcrumb')
    @if (Auth::user()->id == config('app.guest_user_id'))
        <breadcrumbs
            :entries="{{json_encode([
            ['active'=> true, 'title'=> trans('global.navigator.title_singular'), 'url' => "/navigators/" . Auth::user()->organizations()->where('organization_id', '=',  Auth::user()->current_organization_id)->first()->navigators()->first()->id],
        ])}}"
        ></breadcrumbs>
    @else
        <breadcrumbs
            :entries="{{json_encode([
            ['active'=> true, 'title'=> trans('global.map.title')],
            ])}}"
        ></breadcrumbs>
    @endif
@endsection
@section('content')
    <maps model-url="maps"></maps>
@endsection
