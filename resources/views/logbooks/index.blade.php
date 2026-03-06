@extends((Auth::user()->id == config('app.guest_user_id')) ? 'layouts.contentonly' : 'layouts.master')

@section('title')
    {{ trans('global.logbook.title') }}
@endsection
@section('breadcrumb')
    <breadcrumbs
        :entries="{{json_encode([
            ['active'=> true, 'title'=> trans('global.logbook.title')]
        ])}}"
    ></breadcrumbs>
@endsection
@section('content')
    <logbooks create_label_field="create"></logbooks>
@endsection
